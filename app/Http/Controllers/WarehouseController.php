<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Company;
use App\Models\Branch;
use App\Models\Warehouses;

class WarehouseController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view warehouses', ['only' => ['index']]);
        $this->middleware('permission:show warehouses', ['only' => ['show']]);
        $this->middleware('permission:create warehouses', ['only' => ['create', 'store']]);
        $this->middleware('permission:update warehouses', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete warehouses', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $warehouses = Warehouses::all();
        $companies = Company::all();
        $branches = Branch::all();
        return view('settings.companymanage.warehouse', compact('warehouses', 'companies', 'branches'));
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
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'branch_id' => 'required|exists:branches,id',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'phone' => 'required|digits_between:10,15',
            'address' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
            'type' => 'required|string|in:Raw Material,Finished Goods,Warehouse',
        ]);

        DB::beginTransaction();
        try {
            $warehouse = Warehouses::create([
                'company_id' => $request->company_id,
                'branch_id' => $request->branch_id,
                'code' => 'WHRS' . str_pad(Warehouses::count() + 1, 5, '0', STR_PAD_LEFT),
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'description' => $request->description,
                'type' => $request->type
            ]);
            DB::commit();
            return redirect()->back()->with('success', 'Warehouse ' . $warehouse->name . ' created successfully');
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
        $warehouses = Warehouses::with('company', 'branch')->findOrFail($id);
        return view('settings.companymanage.warehouseshow', compact('warehouses'));
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
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'branch_id' => 'required|exists:branches,id',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'phone' => 'required|digits_between:10,15',
            'address' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
            'status' => 'required|string|in:Active,Inactive',
            'type' => 'required|string|in:Raw Material,Finished Goods,Warehouse',
        ]);
        DB::beginTransaction();
        try {
            $warehouse = Warehouses::findOrFail($id);
            $warehouse->update([
                'company_id' => $request->company_id,
                'branch_id' => $request->branch_id,
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
                'description' => $request->description,
                'status' => $request->status,
                'type' => $request->type
            ]);
            DB::commit();
            return redirect()->back()->with('success', 'Warehouse ' . $warehouse->name . ' updated successfully');
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
            $warehouse = Warehouses::find($id);
            $warehouse->delete();
            DB::commit();
            return redirect()->back()->with('success', 'Warehouse ' . $warehouse->name . ' deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
