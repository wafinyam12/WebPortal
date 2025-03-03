<?php

namespace App\Http\Controllers;

use App\Exports\PettyExport;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\PettyCash;
use App\Models\Saldo;
use App\Models\User;
use App\Models\COA;
use App\Models\ItemGroup;
use App\Models\KodeProject;
use App\Models\SKU;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class PettyCashController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view petty cash', ['only' => ['index']]);
        $this->middleware('permission:show petty cash', ['only' => ['report']]);
        $this->middleware('permission:create petty cash', ['only' => ['trans']]);
        $this->middleware('permission:export petty cash', ['only' => ['exportExcel']]);
        $this->middleware('permission:approve petty cash', ['only' => ['approve']]);
        $this->middleware('permission:add saldo', ['only' => ['saldo']]);
        $this->middleware('permission:add coa sku', ['only' => ['addcoasku']]);
    }

    public function index()
    {

        $users = User::whereHas('roles', function ($query) {
            $query->where('name', 'LIKE', '%admin%');
        })->get();

        $coa = COA::all();
        $sku = SKU::all();

        $user = User::with(['employe', 'roles'])->find(auth()->user()->id);
        $isSuperadmin = $user->roles->where('name', 'Superadmin')->isNotEmpty();
        $isFinance = $user->roles->where('name', 'Account Payable')->isNotEmpty();
        $budgetControl = $user->roles->where('name', 'Budget Control')->isNotEmpty();
        $notax = PettyCash::where('no_nota', 'LIKE', 'NOTA%')
            ->orderBy('no_nota', 'desc')
            ->pluck('no_nota')
            ->first();

        // Jika ada no_nota yang ditemukan
        if ($notax) {
            // Ambil angka terakhir setelah "NOTA"
            $lastNumber = (int) substr($notax, 4); // Mengambil angka setelah "NOTA"
            $newNumber = $lastNumber + 1;
            // Buat no_nota baru dengan padding 4 digit
            $nota = 'NOTA' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
        } else {
            // Jika belum ada, mulai dengan NOTA0001
            $nota = 'NOTA0001';
        }


        if (!$isSuperadmin && !$isFinance && !$budgetControl) {
            $pettycash = PettyCash::with('saldo', 'owner')->where('owner_id', auth()->user()->id)->get();
            $saldo = Saldo::where('owner_id', auth()->user()->id)->pluck('saldo')->first();
        } else {
            $pettycash = PettyCash::with('saldo', 'owner')->get();
            $saldo = Saldo::where('owner_id', auth()->user()->id)->pluck('saldo')->first();
        }

        // return $nota;
        return view('petty.index', compact('pettycash', 'saldo', 'users', 'coa', 'sku', 'nota'));
    }

    public function trans(Request $request)
    {

        $request->validate([
            'tanggal' => 'required',
            'no_nota' => 'required|string|size:8',
            'coa' => 'required',
            'nama_file' => 'required',
            'nama_file' => 'max:5120', // 5120 KB = 5MB
        ]);

        $nama_file = null;
        if ($request->hasFile('nama_file')) {
            $file = $request->file('nama_file');
            $nama_file = time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/storage/pattycash', $nama_file);
        }

        $formattedStates = $request->input('formatted_states');
        $sku = $request->input('sku_values');
        $coa = $request->coa;
        // return $coa;

        // Cek apakah user memiliki saldo
        $saldo = Saldo::where('owner_id', auth()->user()->id)->first();

        if (!$saldo) {
            // Jika belum ada saldo, buat akun saldo baru dengan saldo awal 0
            $saldo = Saldo::create([
                'owner_id' => auth()->user()->id,
                'saldo' => 0,
            ]);
        }

        try {
            if ($request->kredit > 0) {
                if ($saldo->saldo < $request->kredit) {
                    return redirect()->route('petty-cash')->with('error', 'Saldo tidak mencukupi!');
                }
                $saldo->update([
                    'saldo' => $saldo->saldo - $request->kredit,
                ]);
            } else {
                $saldo->update([
                    'saldo' => $saldo->saldo + $request->debet,
                ]);
            }

            PettyCash::create([
                'owner_id' => auth()->user()->id,
                'no_nota' => $request->no_nota,
                'tanggal' => now(),
                'tanggal_nota' => $request->tanggal,
                'coa' => $coa,
                'sku' => $sku,
                'keterangan' => $formattedStates,
                'debet' => $request->debet,
                'kredit' => $request->kredit,
                'saldo_id' => $saldo->id,
                'balance' => $saldo->saldo,
                'file_name' => $nama_file,
            ]);

            return redirect()->route('petty-cash')->with('success', 'Transaksi berhasil');
        } catch (\Exception $e) {
            return redirect()->route('petty-cash')->with('error', $e->getMessage());
        }
    }
    public function report(Request $request)
    {
        $id = $request->input('name'); // Ambil nilai dari request
        $start = $request->input('start_date');
        $end = $request->input('end_date');

        $user = User::with(['employe', 'roles'])->find(auth()->id()); // Ambil user yang sedang login
        $isSuperadmin = $user->roles->contains('name', 'Superadmin');
        $isFinance = $user->roles->contains('name', 'Budget Control');

        // Jika user adalah Superadmin atau Finance, ambil data berdasarkan $id yang dikirim
        if ($isSuperadmin || $isFinance) {
            $data = PettyCash::where('owner_id', $id)
                ->whereBetween('tanggal', [$start, $end])
                ->get();
        } else {
            // Jika bukan Superadmin atau Finance, hanya bisa melihat data sendiri
            $data = PettyCash::where('owner_id', auth()->id())
                ->whereBetween('tanggal', [$start, $end])
                ->get();
        }

        return view('petty.report', compact('data', 'start', 'end', 'id'));
    }

    public function exportExcel(Request $request)
    {
        $start = $request->input('tanggal_start');
        $end = $request->input('tanggal_end');
        $user = $request->input('name1');

        // return $start;

        return Excel::download(new PettyExport($start, $end, $user), 'pettycash Report-' . date('d-m-Y') . '.xlsx');
    }

    public function approve(Request $request)
    {
        //jika yang approve adalah budget control
        $user = User::with(['employe', 'roles'])->find(auth()->user()->id);
        $isBudgetControl = auth()->user()->roles->contains('name', 'Budget Control',);
        $isAP = auth()->user()->roles->contains('name', 'Account Payable');
        // $isAP = auth()->user()->roles->contains('name', 'AP');

        if ($isBudgetControl) {
            $id = $request->id;
            // return $id;
            $petty = PettyCash::find($id);
            $petty->update([
                'status_budget_control' => 'Done',
                'approved_date' => now(),
                'approved_by' => auth()->user()->name,
            ]);
        } else {
            $id = $request->id;
            // return $id;
            $petty = PettyCash::find($id);
            $petty->update([
                'status_ap' => 'Done',
                'approved_ap_date' => now(),
                'approved_ap_by' => auth()->user()->name,
            ]);
        }

        try {
            return redirect()->route('petty-cash')->with('success', 'Data Berhasil Disetujui');
        } catch (\Exception $e) {
            return redirect()->route('petty-cash')->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request)
    {
        $id = $request->id;
        $reason = $request->reason;

        // Ambil data petty cash berdasarkan ID
        $petty = PettyCash::find($id);

        // Dapatkan saldo yang terkait dengan owner_id (user)
        $saldo = Saldo::where('owner_id', $petty->owner_id)->first();

        // Update saldo jika ada perubahan
        if ($petty->kredit > 0) {
            $saldo->update([
                'saldo' => $saldo->saldo + $petty->kredit
            ]);
        } else {
            $saldo->update([
                'saldo' => $saldo->saldo - $petty->debet
            ]);
        }

        // Cek role user
        $user = User::with(['employe', 'roles'])->find(auth()->user()->id);
        $isBudgetControl = auth()->user()->roles->contains('name', 'Budget Control');

        // Proses penolakan untuk transaksi dengan no_nota yang sama
        $no_nota = $petty->no_nota;

        // Cari semua transaksi dengan no_nota yang sama dan lakukan penolakan
        $pettyTransactions = PettyCash::where('no_nota', $no_nota)->get();
        foreach ($pettyTransactions as $transaction) {
            if ($isBudgetControl) {
                $transaction->update([
                    'status_budget_control' => 'Rejected',
                    'reject_reason' => $reason,
                    'approved_date' => now(),
                    'approved_by' => auth()->user()->name,
                ]);
            } else {
                $transaction->update([
                    'status_ap' => 'Rejected',
                    'reject_reason' => $reason,
                    'approved_ap_date' => now(),
                    'approved_ap_by' => auth()->user()->name,
                ]);
            }
        }

        try {
            return redirect()->route('petty-cash')->with('success', 'Data Berhasil Ditolak');
        } catch (\Exception $e) {
            return redirect()->route('petty-cash')->with('error', $e->getMessage());
        }
    }


    public function saldo(Request $request)
    {
        $request->validate([
            'name' => 'required|exists:users,id', // Pastikan user ada di tabel users
            'debet' => 'required|numeric',
            'tanggal' => 'required|date',
            'transaksi' => 'required|string|max:255',
            'nama_file' => 'max:5120', // 5120 KB = 5MB
        ]);

        // Ambil no_nota terakhir
        $lastNota = PettyCash::where('no_nota', 'LIKE', 'SALDO%')
            ->orderBy('no_nota', 'desc')
            ->first();

        if ($lastNota) {
            $lastNumber = (int) substr($lastNota->no_nota, 4); // Ambil angka terakhir (mengabaikan "SALD")
            $newNumber = $lastNumber + 1;
            $no_nota = 'SALD' . str_pad($newNumber, 4, '0', STR_PAD_LEFT); // Padding dengan 4 digit
        } else {
            $no_nota = 'SALD0001'; // Jika belum ada data, mulai dari SALD0001
        }

        $nama_file = null;
        if ($request->hasFile('nama_file')) {
            $file = $request->file('nama_file');
            $nama_file = time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/storage/pattycash', $nama_file);
        }

        // Cek apakah user sudah memiliki saldo
        $saldo = Saldo::where('owner_id', $request->name)->first();

        if (!$saldo) {
            // Jika belum ada saldo, buat akun saldo baru
            $saldo = Saldo::create([
                'owner_id' => $request->name,
                'saldo' => 0, // Default saldo awal 0
            ]);
        }

        try {
            // Update saldo dengan jumlah debet yang baru
            $saldo->update([
                'saldo' => $saldo->saldo + $request->debet,
            ]);

            // Buat histori transaksi di tabel PettyCash
            PettyCash::create([
                'owner_id' => $request->name,
                'no_nota' => $no_nota,
                'tanggal' => $request->tanggal,
                'tanggal_nota' => null,
                'keterangan' => $request->transaksi,
                'debet' => $request->debet,
                'kredit' => 0,
                'saldo_id' => $saldo->id,
                'balance' => $saldo->saldo,
                'file_name' => $nama_file,
            ]);

            return redirect()->route('petty-cash')->with('success', 'Saldo berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->route('petty-cash')->with('error', $e->getMessage());
        }
    }

    // +++++++++++++=====================================+++++++++++++++++++++++++
    public function listcoasku()
    {
        $item_group = ItemGroup::all();
        $coa = coa::all();
        $sku = sku::all();
        $projects = KodeProject::all();
        return view('petty.addcoa&sku', compact('item_group', 'sku', 'coa', 'projects'));
    }

    // PROJECT petty cash
    public function storeProject(Request $request)
    {
        $request->validate([
            'code_project' => 'required|unique:project_petty,code_project|max:50',
            'name' => 'required|max:100',
        ]);

        KodeProject::create([
            'code_project' => $request->code_project,
            'keterangan' => $request->name,
        ]);

        return redirect()->back()->with('success', 'Project berhasil ditambahkan.');
    }

    // Mengupdate data project
    public function updateProject(Request $request, $id)
    {
        $request->validate([
            'code' => 'required|max:50|unique:project_petty,code_project,' . $id,
            'name' => 'required|max:100',
        ]);

        $project = KodeProject::findOrFail($id);
        $project->update([
            'code_project' => $request->code,
            'keterangan' => $request->name,
        ]);

        return redirect()->back()->with('success', 'Project berhasil diperbarui.');
    }

    // Menghapus project
    public function destroyProject($id)
    {
        $project = KodeProject::findOrFail($id);
        $project->delete();

        return redirect()->back()->with('success', 'Project berhasil dihapus.');
    }

    // ================================================

    public function storeCoa(Request $request)
    {
        $request->validate([
            'coa' => 'required|unique:coa,coa|max:50',
            'name' => 'required|max:100',
        ]);

        coa::create([
            'coa' => $request->coa,
            'keterangan' => $request->name,
        ]);

        return redirect()->back()->with('success', 'Project berhasil ditambahkan.');
    }

    // Mengupdate data project
    public function updateCoa(Request $request, $id)
    {
        $request->validate([
            'coa' => 'required|max:50|unique:coa,coa,' . $id,
            'name' => 'required|max:100',
        ]);

        $coas = coa::findOrFail($id);
        $coas->update([
            'coa' => $request->coa,
            'keterangan' => $request->name,
        ]);

        return redirect()->back()->with('success', 'Project berhasil diperbarui.');
    }

    // Menghapus project
    public function destroyCoa($id)
    {
        $coas = coa::findOrFail($id);
        $coas->delete();

        return redirect()->back()->with('success', 'Project berhasil dihapus.');
    }

    public function storeGroup(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:item_group,code|max:4',
            'name' => 'required|max:100',
        ]);

        ItemGroup::create([
            'code' => $request->code,
            'keterangan' => $request->name,
            'coa_id' => $request->coa_id,
        ]);

        return redirect()->back()->with('success', 'Project berhasil ditambahkan.');
    }

    // Mengupdate data project
    public function updateGroup(Request $request, $id)
    {
        $request->validate([
            'edit_group_code' => 'required|max:50|unique:item_group,code,' . $id,
            'name' => 'required|max:100',
        ]);

        $item_group = ItemGroup::findOrFail($id);
        $item_group->update([
            'code' => $request->edit_group_code,
            'keterangan' => $request->name,
            'coa_id' => $request->edit_coa_id,
        ]);

        return redirect()->back()->with('success', 'Project berhasil diperbarui.');
    }

    // Menghapus project
    public function destroyGroup($id)
    {
        $item_group = ItemGroup::findOrFail($id);
        $item_group->delete();

        return redirect()->back()->with('success', 'Project berhasil dihapus.');
    }

    public function storeSku(Request $request)
    {
        $request->validate([
            'name' => 'required|max:100',
            'itemGroup' => 'required|exists:item_group,id', // Validasi item group
        ]);

        // Ambil kode item group berdasarkan ID
        $group_id = $request->itemGroup;
        $code_group = ItemGroup::find($group_id)->code; // Misal: "ATK" atau "TOOL"

        // Cari SKU terakhir dengan prefix yang sama
        $lastSku = Sku::where('sku', 'LIKE', "{$code_group}%")->orderBy('sku', 'desc')->first();

        if ($lastSku) {
            // Ambil nomor terakhir dari SKU, misal dari "ATK0123" menjadi 123
            $lastNumber = (int) substr($lastSku->sku, strlen($code_group));
            $newNumber = $lastNumber + 1;
        } else {
            // Jika belum ada SKU, mulai dari 1
            $newNumber = 1;
        }
        // return $lastNumber;

        // Hitung berapa digit angka yang dibutuhkan agar total panjang SKU tetap 8 karakter
        $digitCount = 8 - strlen($code_group);
        $formattedNumber = str_pad($newNumber, $digitCount, '0', STR_PAD_LEFT);

        // Gabungkan kode grup dengan angka
        $newSku = $code_group . $formattedNumber;


        // Simpan ke database
        Sku::create([
            'sku' => $newSku,
            'keterangan' => $request->name,
            'item_group_id' => $group_id,
        ]);

        return redirect()->back()->with('success', 'SKU berhasil ditambahkan.');
    }

    public function updateSku(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:100',
        ]);

        $sku = Sku::findOrFail($id);
        $sku->update([
            'keterangan' => $request->name,
        ]);

        return redirect()->back()->with('success', 'SKU berhasil diperbarui.');
    }

    public function destroySku($id)
    {
        $sku = Sku::findOrFail($id);
        $sku->delete(); // Hapus data SKU

        return redirect()->back()->with('success', 'SKU berhasil dihapus.');
    }
}
