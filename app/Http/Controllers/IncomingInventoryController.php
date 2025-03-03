<?php

namespace App\Http\Controllers;

use App\Models\IncomingInventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use App\Models\IncomingShipments;
use App\Models\IncomingSupplier;
use App\Models\Branch;
use App\Models\Warehouses;

class IncomingInventoryController extends Controller
{
    /**
     * Create a new controller instance. 
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view incoming plan', ['only' => ['index']]);
        $this->middleware('permission:show incoming plan', ['only' => ['show']]);
        $this->middleware('permission:create incoming plan', ['only' => ['create', 'store']]);
        $this->middleware('permission:update incoming plan', ['only' => ['edit', 'update']]);
        $this->middleware('permission:print incoming plan', ['only' => ['printPdf']]);
        $this->middleware('permission:delete incoming plan', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $incomings = IncomingShipments::with('branch', 'drop')->get();
        return view('incomings.inventory.index', compact('incomings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $branches = Branch::select('id', 'name')->get();
        $warehouses = Warehouses::select('id', 'name')->get();
        $suppliers = IncomingSupplier::select('id', 'name')->get();

        return view('incomings.inventory.create', compact('branches', 'warehouses', 'suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'supplier_id' => 'required|exists:incoming_suppliers,id',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'drop_site' => 'nullable|string',
            'phone_drop_site' => 'nullable|numeric|digits_between:10,15',
            'email_drop_site' => 'nullable|email',
            'eta' => 'required|date',
            'notes' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:pdf|max:2048',
            'items' => 'required|array',
            'items.*' => 'required',
            'items.*.item_name' => 'required|string',
            'items.*.quantity' => 'required|string',
        ]);

        // Create default code for incoming ex: PO-001
        $lastCode = IncomingShipments::lockForUpdate()->max('code');
        if ($lastCode) {
            $number = (int) substr($lastCode, 5);
            $code = 'RBM-' . str_pad($number + 1, 5, '0', STR_PAD_LEFT);
        } else {
            $code = 'RBM-00001';
        }

        $filename = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = $code . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/incomings/file', $filename);
        }

        DB::beginTransaction();
        try {
            $incoming = IncomingShipments::create([
                'code' => $code,
                'created_by' => auth()->user()->id,
                'branch_id' => $request->branch_id,
                'supplier_id' => $request->supplier_id,
                'warehouse_id' => $request->warehouse_id,
                'drop_site' => $request->drop_site,
                'phone_drop_site' => $request->phone_drop_site,
                'email_drop_site' => $request->email_drop_site,
                'eta' => $request->eta,
                'notes' => $request->notes,
                'attachment' => $filename,
            ]);

            foreach ($request->items as $item) {
                IncomingInventory::create([
                    'shipment_id' => $incoming->id,
                    'item_name' => $item['item_name'],
                    'quantity' => $item['quantity'],
                ]);
            }
            DB::commit();
            return redirect()->back()->with('success', 'Incoming ' . $incoming->code . ' created successfully');
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
        $incomings = IncomingShipments::with('item', 'branch', 'drop', 'supplier')->findOrFail($id);
        return view('incomings.inventory.show', compact('incomings'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $branches = Branch::select('id', 'name')->get();
        $warehouses = Warehouses::select('id', 'name')->get();
        $suppliers = IncomingSupplier::select('id', 'name')->get();
        $incomings = IncomingShipments::with('item', 'branch', 'drop', 'supplier')->findOrFail($id);

        return view('incomings.inventory.edit', compact('incomings', 'branches', 'warehouses', 'suppliers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'supplier_id' => 'required|exists:incoming_suppliers,id',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'drop_site' => 'nullable|string',
            'phone_drop_site' => 'nullable|numeric|digits_between:10,15',
            'email_drop_site' => 'nullable|email',
            'eta' => 'required|date',
            'notes' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:pdf|max:2048',
            'items' => 'required|array',
            'items.*' => 'required',
            'items.*.item_name' => 'required|string',
            'items.*.quantity' => 'required|string',
        ]);
        // Temukan data transaksi
        $incoming = IncomingShipments::findOrFail($id);

        // Cek apakah ada file baru atau tidak
        $filename = $incoming->attachment;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename =  $incoming->code . '.' . $file->getClientOriginalExtension();

            // Hapus file lama jika ada
            if (Storage::exists('public/incomings/file/' . $incoming->attachment)) {
                Storage::delete('public/incomings/file/' . $incoming->attachment);
            }

            // Simpan file baru
            $file->storeAs('public/incomings/file', $filename);
        }

        DB::beginTransaction();
        try {
            // Update data transaksi
            $incoming->update([
                'branch_id' => $request->branch_id,
                'supplier_id' => $request->supplier_id,
                'eta' => $request->eta,
                'warehouse_id' => $request->warehouse_id,
                'drop_site' => $request->drop_site,
                'phone_drop_site' => $request->phone_drop_site,
                'email_drop_site' => $request->email_drop_site,
                'notes' => $request->notes,
                'attachment' => $filename
            ]);

            // Ambil item yang ada di database untuk transaksi ini
            $existingItems = IncomingInventory::where('shipment_id', $incoming->id)
                ->pluck('id', 'item_name')
                ->toArray();

            // Data item baru dari request
            $newItems = collect($request->items)->mapWithKeys(function ($item) {
                return [$item['item_name'] => $item['quantity']];
            });

            // Hapus item yang tidak ada di data baru
            foreach ($existingItems as $itemName => $itemId) {
                if (!$newItems->has($itemName)) {
                    IncomingInventory::find($itemId)->delete();
                }
            }

            // Tambah atau update item baru
            foreach ($newItems as $itemName => $quantity) {
                IncomingInventory::updateOrCreate(
                    [
                        'shipment_id' => $incoming->id,
                        'item_name' => $itemName,
                        'quantity' => $quantity
                    ],
                );
            }

            DB::commit();
            return redirect()->back()->with('success', 'Incoming ' . $incoming->code . ' updated successfully');
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
            $getIncomingId = IncomingShipments::find($id);
            $getItem = IncomingInventory::where('shipment_id', $getIncomingId->id)->get();
            foreach ($getItem as $item) {
                $item->delete();
            }
            $getIncomingId->delete();
            DB::commit();
            return redirect()->back()->with('success', 'Incoming ' . $getIncomingId->code . ' deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Export the specified resource from storage.
     */
    public function printPdf(string $id)
    {
        $incomings = IncomingShipments::with('item', 'branch', 'drop', 'supplier')->findOrFail($id);
        $pdf = PDF::loadView('incomings.inventory.pdf', compact('incomings'));
        return $pdf->stream('incomings-' . $incomings->code . '.pdf');
    }
}
