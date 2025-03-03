<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Company;
use App\Models\Tools;
use App\Models\ToolsCategorie;
use App\Models\Projects;

class ToolsController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view tools', ['only' => ['index']]);
        $this->middleware('permission:show tools', ['only' => ['show']]);
        $this->middleware('permission:create tools', ['only' => ['create', 'store']]);
        $this->middleware('permission:update tools', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete tools', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // generate default code
        $tools = Tools::with('categorie', 'owner')->get();
        $categories = ToolsCategorie::select('id', 'code', 'name', 'description')->get();
        $ownerships = Branch::select('id', 'name')->get();
        return view('tools.index', compact('tools', 'categories', 'ownerships'));
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
        // Validate request data
        $request->validate([
            'ownership' => 'required|exists:companies,id',
            'categories' => 'required|exists:tools_categories,id',
            'serial_number' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'origin' => 'nullable|string|max:255',
            'quantity' => 'nullable|integer|min:1',
            'unit' => 'nullable|in:ROL,PCS,SET,UNIT,METER,LITER,KG',
            'description' => 'nullable|string',
            'purchase_date' => 'nullable|date',
            'purchase_price' => 'nullable|numeric|min:0',
            'warranty' => 'nullable|string|max:255',
            'warranty_start' => 'nullable|date',
            'warranty_end' => 'nullable|date|after_or_equal:warranty_start',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Generate default code where category is selected
        $getCategory = ToolsCategorie::where('id', $request->categories)->first();
        // Ambil 4 huruf pertama nama kategori
        $prefix = strtoupper(substr($getCategory->name, 0, 4));
        // Cari item terakhir yang memiliki prefix sama
        $lastItem = Tools::where('code', 'LIKE', $prefix . '%')->latest('id')->first();
        // Ambil angka terakhir dari kode, jika ada
        $lastNumber = $lastItem ? intval(substr($lastItem->code, strlen($prefix))) : 0;
        // Tambahkan 1 ke angka terakhir
        $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        // Gabungkan prefix dengan angka baru
        $code = $prefix . $newNumber;

        // image upload
        $photoFile = null;
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoFile = time() . '.' . $photo->getClientOriginalExtension();
            $photo->storeAs('public/tools/photo', $photoFile);
        }

        $tools = [
            'owner_id' => $request->ownership,
            'category_id' => $request->categories,
            'code' => $code,
            'serial_number' => $request->serial_number,
            'name' => $request->name,
            'brand' => $request->brand,
            'type' => $request->type,
            'model' => $request->model,
            'year' => $request->year,
            'origin' => $request->origin,
            'quantity' => $request->quantity,
            'unit' => $request->unit,
            'description' => $request->description,
            'purchase_date' => $request->purchase_date,
            'purchase_price' => $request->purchase_price,
            'warranty' => $request->warranty,
            'warranty_start' => $request->warranty_start,
            'warranty_end' => $request->warranty_end,
            'photo' => $photoFile,
        ];

        DB::beginTransaction();
        try {
            $tools = Tools::create($tools);
            DB::commit();
            return redirect()->back()->with('success', 'Tools ' . $tools->name . ' created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $tools = Tools::with('categorie', 'owner')->findOrFail($id);
        $log = Tools::with('maintenance', 'transactions.transaction')->find($id);
        // Gabungkan aktivitas
        $activities = collect();

        // Ambil aktivitas maintenance
        if ($log->maintenance->isNotEmpty()) {
            foreach ($log->maintenance as $main) {
                $activities->push([
                    'type' => 'Maintenance',
                    'details' => [
                        'date' => date('d-m-Y', strtotime($main->maintenance_date)),
                        'description' => $main->description,
                        'cost' => $main->cost,
                        'status' => $main->status,
                        'completed_at' => date('d-m-Y', strtotime($main->completion_date)),
                    ],
                ]);
            }
        }

        // Ambil aktivitas transaksi
        if ($log->transactions->isNotEmpty()) {
            foreach ($log->transactions as $detail) {
                $tran = $detail->transaction;

                // Ambil data proyek sekali saja
                $projectMap = Projects::whereIn('id', $detail->transaction->pluck('source_project_id')
                    ->merge($detail->transaction->pluck('destination_project_id')))
                    ->get()
                    ->keyBy('id'); // Map proyek berdasarkan ID untuk akses cepat
                $sourceProjectName = $projectMap[$tran->source_project_id]->name ?? 'Unknown';
                $destinationProjectName = $projectMap[$tran->destination_project_id]->name ?? 'Unknown';

                if ($tran) {
                    $activities->push([
                        'type' => 'Activity',
                        'transaction' => $tran->type,
                        'date' => date('d-m-Y', strtotime($tran->created_at)),
                        'driver' => $tran->driver,
                        'driver_phone' => $tran->driver_phone,
                        'status' => $tran->statusName,
                        'notes' => $tran->notes,
                        'details' => [
                            'source_project' => $sourceProjectName,
                            'destination_project' => $destinationProjectName,
                            'last_location' => $detail->last_location,
                        ],
                    ]);
                }
            }
        }

        return view('tools.show', compact('tools', 'activities'));
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
        // Validate request data
        $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'origin' => 'nullable|string|max:255',
            'quantity' => 'nullable|integer|min:1',
            'unit' => 'nullable|in:ROL,PCS,SET,UNIT,METER,LITER,KG',
            'status' => 'required|string|max:255',
            'description' => 'nullable|string',
            'purchase_date' => 'nullable|date',
            'purchase_price' => 'nullable|numeric|min:0',
            'warranty' => 'nullable|string|max:255',
            'warranty_start' => 'nullable|date',
            'warranty_end' => 'nullable|date|after_or_equal:warranty_start',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // image upload update file exists
        $photoFile = null;
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoFile = time() . '.' . $photo->getClientOriginalExtension();
            $photo->storeAs('public/tools/photo', $photoFile);
        }

        $data = [
            'name' => $request->name,
            'brand' => $request->brand,
            'type' => $request->type,
            'model' => $request->model,
            'year' => $request->year,
            'origin' => $request->origin,
            'quantity' => $request->quantity,
            'unit' => $request->unit,
            'status' => $request->status,
            'description' => $request->description,
            'purchase_date' => $request->purchase_date,
            'purchase_price' => $request->purchase_price,
            'warranty' => $request->warranty,
            'warranty_start' => $request->warranty_start,
            'warranty_end' => $request->warranty_end,
            'photo' => $photoFile,
        ];

        DB::beginTransaction();
        try {
            $tools = Tools::findOrFail($id);
            $tools->update($data);
            DB::commit();
            return redirect()->back()->with('success', 'Tools ' . $tools->name . ' update successfuly');
        } catch (\Exception $e) {
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
            $tools = Tools::findOrFail($id);
            $tools->delete();
            DB::commit();
            return redirect()->back()->with('message', 'Tools ' . $tools->name . 'Successfully delete');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
