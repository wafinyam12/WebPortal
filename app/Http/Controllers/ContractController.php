<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ContractExport;
use App\Imports\ContractImport;
use App\Models\Contract;

class ContractController extends Controller
{
    /**
     * Create a new controller instance.
     * 
     * @return void
     * */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view contracts', ['only' => ['index', 'show']]);
        $this->middleware('permission:create contracts', ['only' => ['create', 'store', 'importContract']]);
        $this->middleware('permission:update contracts', ['only' => ['edit', 'update']]);
        $this->middleware('permission:print contracts', ['only' => ['export', 'exportExcel']]);
        $this->middleware('permission:delete contracts', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contracts = Contract::all();
        return view('contracts.index', compact('contracts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'code' => 'required|string|unique:contract,code',
            'name' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $contracts = Contract::create([
                'code' => $request->code,
                'created_by' => auth()->user()->name,
                'name' => $request->name,
                'nama_perusahaan' => $request->nama_perusahaan,
                'nama_pekerjaan' => $request->nama_pekerjaan,
                'status_kontrak' => $request->status_kontrak,
                'jenis_pekerjaan' => $request->jenis_pekerjaan,
                'nominal_kontrak' => $request->nominal_kontrak,
                'tanggal_kontrak' => $request->tanggal_kontrak,
                'masa_berlaku' => $request->masa_berlaku,
                'status_proyek' => $request->status_proyek,
                'retensi' => $request->retensi,
                'masa_retensi' => $request->masa_retensi,
                'status_retensi' => $request->status_retensi,
                'pic_sales' => $request->pic_sales,
                'pic_pc' => $request->pic_pc,
                'pic_customer' => $request->pic_customer,
                'mata_uang' => $request->mata_uang,
                'bast_1' => $request->bast_1,
                'bast_1_nomor' => $request->bast_1_nomor,
                'bast_2' => $request->bast_2,
                'bast_2_nomor' => $request->bast_2_nomor,
                'overall_status' => $request->overall_status,
                'kontrak_milik' => $request->kontrak_milik,
                'keterangan' => $request->keterangan,
                'memo' => $request->memo,
            ]);
            DB::commit();
            return redirect()->back()->with('success', 'Data ' . $contracts->name . ' berhasil disimpan.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $contract = Contract::findOrFail($id);
        return view('contracts.show', compact('contract'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            // Cari kontrak berdasarkan ID
            $contract = Contract::findOrFail($id);
            $contract->update([
                'name' => $request->name,
                'nama_perusahaan' => $request->nama_perusahaan,
                'nama_pekerjaan' => $request->nama_pekerjaan,
                'status_kontrak' => $request->status_kontrak,
                'jenis_pekerjaan' => $request->jenis_pekerjaan,
                'nominal_kontrak' => $request->nominal_kontrak,
                'tanggal_kontrak' => $request->tanggal_kontrak,
                'masa_berlaku' => $request->masa_berlaku,
                'status_proyek' => $request->status_proyek,
                'retensi' => $request->retensi,
                'masa_retensi' => $request->masa_retensi,
                'status_retensi' => $request->status_retensi,
                'pic_sales' => $request->pic_sales,
                'pic_pc' => $request->pic_pc,
                'pic_customer' => $request->pic_customer,
                'mata_uang' => $request->mata_uang,
                'bast_1' => $request->bast_1,
                'bast_1_nomor' => $request->bast_1_nomor,
                'bast_2' => $request->bast_2,
                'bast_2_nomor' => $request->bast_2_nomor,
                'overall_status' => $request->overall_status,
                'kontrak_milik' => $request->kontrak_milik,
                'keterangan' => $request->keterangan,
                'memo' => $request->memo,
            ]);
            DB::commit();
            return redirect()->back()->with('success', 'Data ' . $contract->name . ' berhasil diperbarui.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $contract = Contract::findOrFail($id);
            $contract->delete();
            DB::commit();
            return redirect()->back()->with('success', 'Data ' . $contract->name . ' berhasil dihapus.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Export the specified resource to PDF. 
     */
    public function export($id)
    {
        $contract = Contract::findOrFail($id);
        $pdf = PDF::loadView('contracts.pdf', compact('contract'));
        $newFilename = 'contracts-' . str_replace('/', '-', $contract->name) . '.pdf';
        return $pdf->stream($newFilename);
    }

    /**
     * Export the specified resource to Excel. 
     */
    public function exportExcel(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Validasi parameter
        if (!$startDate || !$endDate) {
            return redirect()->back()->withErrors(['error' => 'Start date and end date are required.']);
        }

        return Excel::download(new ContractExport($startDate, $endDate), 'all-contracts-' . date('d-m-Y') . '.xlsx');
    }

    /**
     * Import the specified resource from Excel. 
     */
    public function importContract(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,xlsx,xls',
        ]);
        DB::beginTransaction();
        try {
            $file = $request->file('file');
            Excel::import(new ContractImport, $file);
            DB::commit();
            return redirect()->back()->with('success', 'Data kontrak berhasil diimport.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
