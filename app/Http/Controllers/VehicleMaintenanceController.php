<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Vehicle;
use App\Models\VehicleMaintenance;

class VehicleMaintenanceController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view vehicle maintenances', ['only' => ['index']]);
        $this->middleware('permission:create vehicle maintenances', ['only' => ['create', 'store']]);
        $this->middleware('permission:update vehicle maintenances', ['only' => ['edit', 'update']]);
        $this->middleware('permission:complete vehicle maintenances', ['only' => ['completeMaintenance']]);
        $this->middleware('permission:cancel vehicle maintenances', ['only' => ['cancelMaintenance']]);
        $this->middleware('permission:print vehicle maintenances', ['only' => ['printMaintenance']]);
        $this->middleware('permission:delete vehicle maintenances', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $maintenances = VehicleMaintenance::with('vehicle')->get();
        return view('vehicles.maintenance.maintenances', compact('maintenances'));
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
        // Validate the request data
        $request->validate([
            'vehicle_code' => 'required|exists:vehicle,code',
            'maintenances_date' => 'required|date',
            'mileage' => 'required|numeric',
            'maintenances_description' => 'nullable|string',
            'maintenances_cost' => 'required|numeric',
            'next_maintenances' => 'required|date',
        ]);

        // Create a new vehicle maintenance record
        $vehicles = Vehicle::where('code', $request->vehicle_code)->first();
        $data = [
            'vehicle_id' => $vehicles->id,
            'code' => 'MTCN-' . str_pad(VehicleMaintenance::count() + 1, 4, '0', STR_PAD_LEFT),
            'mileage' => $request->mileage,
            'maintenance_date' => $request->maintenances_date,
            'description' => $request->maintenances_description,
            'cost' => $request->maintenances_cost,
            'next_maintenance' => $request->next_maintenances,
            'status' => 'In Progress',
            'photo' => null, // cooming soon
        ];

        DB::beginTransaction();
        try {
            if ($vehicles->status == 'Maintenance' || $vehicles->status == 'Inactive') {
                return redirect()->back()->with('error', 'Vehicle ' . $vehicles->model . ' is already in maintenance mode or inactive.');
            }
            VehicleMaintenance::create($data);
            $vehicles->update(['status' => 'Maintenance']);
            DB::commit();
            return redirect()->back()->with('success', 'Maintenance ' . $vehicles->model . ' Succes fully created');
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
        $maintenances = VehicleMaintenance::with('vehicle')->find($id);
        return view('vehicles.maintenance.show', compact('maintenances'));
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
        // Validate the request data
        $request->validate([
            'mileage' => 'required|numeric',
            'maintenances_date' => 'required|date',
            'maintenances_description' => 'nullable|string',
            'maintenances_cost' => 'required|numeric',
            'next_maintenances' => 'required|date',
        ]);

        // Update the vehicle maintenance record
        $maintenance = VehicleMaintenance::find($id);
        $data = [
            'mileage' => $request->mileage,
            'maintenance_date' => $request->maintenances_date,
            'description' => $request->maintenances_description,
            'cost' => $request->maintenances_cost,
            'next_maintenance' => $request->next_maintenances,
        ];

        DB::beginTransaction();
        try {
            $maintenance->update($data);
            DB::commit();
            return redirect()->back()->with('success', 'Maintenance ' . $maintenance->vehicle->model . ' Succes fully updated');
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
            $maintenance = VehicleMaintenance::find($id);
            $maintenance->delete();
            DB::commit();
            return redirect()->back()->with('success', 'Maintenance ' . $maintenance->vehicle->model . ' Succes fully deleted');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Complete the specified resource from storage.
     */
    public function completeMaintenance(string $id)
    {
        DB::beginTransaction();
        try {
            $vehicle = VehicleMaintenance::with('vehicle')->find($id);
            $vehicle->update(['status' => 'Completed']);
            $vehicle->vehicle->update(['status' => 'Active']);
            DB::commit();
            return redirect()->back()->with('success', 'Maintenance ' . $vehicle->vehicle->model . ' Succes fully completed');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Cancel the specified resource from storage.
     */
    public function cancelMaintenance(string $id)
    {
        DB::beginTransaction();
        try {
            $vehicle = VehicleMaintenance::with('vehicle')->find($id);
            $vehicle->update(['status' => 'Cancelled']);
            $vehicle->vehicle->update(['status' => 'Maintenance']);
            DB::commit();
            return redirect()->back()->with('success', 'Maintenance ' . $vehicle->vehicle->model . ' Succes fully canceled');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Print the specified resource from storage.
     */
    public function printMaintenance(string $id)
    {
        $maintenance = VehicleMaintenance::with('vehicle')->findOrFail($id);
        $pdf = PDF::loadView('vehicles.maintenance.pdf', compact('maintenance'));
        return $pdf->stream('maintenance.pdf');
    }
}
