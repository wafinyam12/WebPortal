<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\IncomingSupplier;

class IncomingSupplierController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view incoming suppliers', ['only' => ['index']]);
        $this->middleware('permission:show incoming suppliers', ['only' => ['show']]);
        $this->middleware('permission:create incoming suppliers', ['only' => ['create', 'store']]);
        $this->middleware('permission:update incoming suppliers', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete incoming suppliers', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $suppliers = IncomingSupplier::all();
        return view('incomings.supplier.index', compact('suppliers'));
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
            'name' => 'required|string|max:255',
            'phone' => 'required|numeric|digits_between:10,15',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $supplier = IncomingSupplier::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
            ]);
            DB::commit();
            return redirect()->back()->with('success', 'Supplier ' . $supplier->name . ' created successfully');
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
        $supplier = IncomingSupplier::findOrFail($id);
        return view('incomings.supplier.show', compact('supplier'));
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
            'name' => 'required|string|max:255',
            'phone' => 'required|numeric|digits_between:10,15',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $supplier = IncomingSupplier::findOrFail($id);
            $supplier->update([
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
            ]);
            DB::commit();
            return redirect()->back()->with('success', 'Supplier ' . $supplier->name . ' updated successfully');
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
            $supplier = IncomingSupplier::findOrFail($id);
            $supplier->delete();
            DB::commit();
            return redirect()->back()->with('success', 'Supplier ' . $supplier->name . ' deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
