<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VehicleReimbursementExport;
use App\Models\VehicleReimbursement;
use App\Models\Vehicle;
use App\Models\User;

class VehicleReimbursementController extends Controller
{
    /**
     *  Create a new controller instance. 
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view vehicle reimbursements', ['only' => ['index']]);
        $this->middleware('permission:show vehicle reimbursements', ['only' => ['show']]);
        $this->middleware('permission:create vehicle reimbursements', ['only' => ['create', 'store']]);
        $this->middleware('permission:update vehicle reimbursements', ['only' => ['edit', 'update']]);
        $this->middleware('permission:approved vehicle reimbursements', ['only' => ['approved']]);
        $this->middleware('permission:rejected vehicle reimbursements', ['only' => ['rejected']]);
        $this->middleware('permission:export vehicle reimbursements', ['only' => ['exportExcel']]);
        $this->middleware('permission:delete vehicle reimbursements', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with(['employe:id,user_id,full_name'])->get();
        $reimbursements = VehicleReimbursement::all();
        return view('vehicles.reimbursement.index', compact('reimbursements', 'users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $getVehicles = Vehicle::whereHas('assigned', function ($query) {
            $query->where('user_id', auth()->user()->id);
        })->get();
        $vehicleCode = $getVehicles->pluck('code')->first();
        $reimbursements = VehicleReimbursement::with('vehicle.assigned')
            ->whereHas('vehicle.assigned', function ($query) {
                $query->where('user_id', auth()->user()->id);
            })
            ->get();

        return view('vehicles.reimbursement.create', compact('vehicleCode', 'reimbursements'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|exists:vehicle,code',
            'date' => 'required|date',
            'type' => 'required|string|in:Refueling,Parking,E-Toll',
            'first_mileage' => 'nullable|numeric',
            'last_mileage' => 'nullable|numeric',
            'fuel' => 'nullable|string|in:Pertalite,Pertamax,Pertamax Turbo,Solar,Dex,Dex Lite,Shell Super,Shell V-Power,Shell V-Power Diesel',
            'amount' => 'nullable|numeric',
            'price' => 'required|numeric',
            'notes' => 'nullable|string',
            'attachment_mileage' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'receipt' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        // Simpan file jika ada
        $mileageFile = null;
        $receiptFile = null;

        if ($request->hasFile('attachment_mileage')) {
            $attachment_mileage = $request->file('attachment_mileage');
            $mileageFile = time() . '.' . $attachment_mileage->getClientOriginalExtension();
            $attachment_mileage->storeAs('public/vehicle/reimbursements/mileage', $mileageFile);
        }

        if ($request->hasFile('receipt')) {
            $receipt = $request->file('receipt');
            $receiptFile = time() . '.' . $receipt->getClientOriginalExtension();
            $receipt->storeAs('public/vehicle/reimbursements/receipt', $receiptFile);
        }

        DB::beginTransaction();
        try {
            $getVehicle = Vehicle::where('code', $request->code)->first();
            VehicleReimbursement::create([
                'vehicle_id' => $getVehicle->id,
                'date_recorded' => $request->date,
                'user_by' => auth()->user()->id,
                'fuel' => $request->fuel,
                'amount' => $request->amount,
                'price' => $request->price,
                'first_mileage' => $request->first_mileage,
                'last_mileage' => $request->last_mileage,
                'attachment_mileage' => $mileageFile,
                'attachment_receipt' => $receiptFile,
                'notes' => $request->notes,
                'status' => 'Pending',
                'type' => $request->type,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Reimbursement ' . $getVehicle->brand . ' Created Successfully');
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
        $reimbursement = VehicleReimbursement::with('vehicle.assigned.employe', 'user', 'approvedBy', 'rejectedBy')->find($id);
        return view('vehicles.reimbursement.show', compact('reimbursement'));
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
            'code' => 'required|string|exists:vehicle,code',
            'date' => 'required|date',
            'type' => 'required|string|in:Refueling,Parking,E-Toll',
            'first_mileage' => 'nullable|numeric',
            'last_mileage' => 'nullable|numeric',
            'fuel' => 'nullable|string|in:Pertalite,Pertamax,Pertamax Turbo,Solar,Dex,Dex Lite,Shell Super,Shell V-Power,Shell V-Power Diesel',
            'amount' => 'nullable|numeric',
            'price' => 'required|numeric',
            'notes' => 'nullable|string',
            'attachment_mileage' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'receipt' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        // Simpan file jika ada
        $mileageFile = null;
        $receiptFile = null;

        if ($request->hasFile('attachment_mileage')) {
            $attachment_mileage = $request->file('attachment_mileage');
            $mileageFile = time() . '.' . $attachment_mileage->getClientOriginalExtension();
            $attachment_mileage->storeAs('public/vehicle/reimbursements/mileage', $mileageFile);
        }

        if ($request->hasFile('receipt')) {
            $receipt = $request->file('receipt');
            $receiptFile = time() . '.' . $receipt->getClientOriginalExtension();
            $receipt->storeAs('public/vehicle/reimbursements/receipt', $receiptFile);
        }

        DB::beginTransaction();
        try {
            $getVehicle = Vehicle::where('code', $request->code)->first();
            $reimbursement = VehicleReimbursement::find($id);
            $reimbursement->update([
                'vehicle_id' => $getVehicle->id,
                'date_recorded' => $request->date,
                'user_by' => auth()->user()->id,
                'fuel' => $request->fuel,
                'amount' => $request->amount,
                'price' => $request->price,
                'first_mileage' => $request->first_mileage,
                'last_mileage' => $request->last_mileage,
                'attachment_mileage' => $mileageFile,
                'attachment_receipt' => $receiptFile,
                'notes' => $request->notes,
                'status' => 'Pending',
                'type' => $request->type,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Reimbursement ' . $getVehicle->brand . ' Updated Successfully');
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
            $reimbursement = VehicleReimbursement::find($id);
            $reimbursement->delete();
            DB::commit();
            return redirect()->back()->with('success', 'Reimbursement ' . $reimbursement->vehicle->brand . ' Deleted Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function approved(Request $request)
    {
        DB::beginTransaction();
        try {
            $reimbursement = VehicleReimbursement::find($request->id);
            $reimbursement->update([
                'approved_by' => auth()->user()->id,
                'status' => 'Approved',
                'approved_at' => now()
            ]);
            DB::commit();
            return redirect()->back()->with('success', 'Reimbursement ' . $reimbursement->vehicle->brand . ' Approved Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function rejected(Request $request)
    {
        DB::beginTransaction();
        try {
            $reimbursement = VehicleReimbursement::find($request->id);
            $reimbursement->update([
                'rejected_by' => auth()->user()->id,
                'reason' => $request->reason,
                'status' => 'Rejected',
                'rejected_at' => now()
            ]);
            DB::commit();
            return redirect()->back()->with('success', 'Reimbursement ' . $reimbursement->vehicle->brand . ' Rejected Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function exportExcel(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $fileName = 'reimbursements_' . date('Y-m-d') . '.xlsx';

        try {
            return Excel::download(new VehicleReimbursementExport($request), $fileName);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
