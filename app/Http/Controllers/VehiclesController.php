<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Employe;
use App\Models\Vehicle;
use App\Models\VehicleType;
use App\Models\VehicleAssignment;

class VehiclesController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view vehicles', ['only' => ['index']]);
        $this->middleware('permission:show vehicles', ['only' => ['show']]);
        $this->middleware('permission:create vehicles', ['only' => ['create', 'store']]);
        $this->middleware('permission:assign vehicles', ['only' => ['assign']]);
        $this->middleware('permission:update vehicles', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete vehicles', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vehicleTypes = VehicleType::select('id', 'code', 'name', 'description')->get();
        $vehicleOwnerships = Branch::select('id', 'name')->get();
        $users = User::all();
        $vehicles = Vehicle::with('type', 'ownership', 'assigned.employe')->get();
        return view('vehicles.index', compact('vehicles', 'vehicleTypes', 'vehicleOwnerships', 'users'));
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
        // Validate the form data
        $request->validate([
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'ownership' => 'required|exists:companies,id',
            'license_plate' => 'required|string|max:10',
            'year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'color' => 'required|string|max:50',
            'vehicle_type' => 'required|exists:vehicle_type,id',
            'fuel' => 'required|in:Gasoline,Diesel',
            'purchase_price' => 'nullable|numeric|min:0',
            'purchase_date' => 'nullable|date',
            'transmission' => 'required|in:Automatic,Manual',
            'tax_year' => 'required|date',
            'tax_five_year' => 'required|date',
            'inspected' => 'nullable|date',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Proses upload gambar
        $photoFile = null;
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoFile = time() . '.' . $photo->getClientOriginalName();
            $photo->storeAs('public/vehicle/photo', $photoFile);
        }

        $data = [
            'code' => 'KO-' . str_pad(Vehicle::count() + 1, 4, '0', STR_PAD_LEFT),
            'type_id' => $request->vehicle_type,
            'brand' => $request->brand,
            'model' => $request->model,
            'owner_id' => $request->ownership,
            'license_plate' => $request->license_plate,
            'year' => $request->year,
            'color' => $request->color,
            'fuel' => $request->fuel,
            'transmission' => $request->transmission,
            'tax_year' => $request->tax_year,
            'tax_five_year' => $request->tax_five_year,
            'inspected' => $request->inspected,
            'purchase_date' => $request->purchase_date,
            'purchase_price' => $request->purchase_price,
            'description' => $request->description,
            'origin' => $request->origin,
            'photo' => $photoFile,
        ];

        DB::beginTransaction();
        try {
            $vehicle = Vehicle::create($data);
            DB::commit();
            return redirect()->back()->with('success', 'Vehicle ' . $vehicle->model . ' created successfully.');
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
        $vehicle = Vehicle::with('type', 'ownership')->findOrFail($id);
        $logs = Vehicle::with('assigned.employe', 'maintenanceRecords')->find($id);

        $activities = collect();

        foreach ($logs->assigned as $assignment) {
            $activities->push([
                'type' => 'Assigned',
                'user' => $assignment->employe->full_name,
                'date' => $assignment->assignment_date,
                'return_date' => $assignment->return_date,
                'notes' => $assignment->notes,
            ]);
        }

        foreach ($logs->maintenanceRecords as $maintenanceRecord) {
            $activities->push([
                'type' => 'Maintenance',
                'date' => $maintenanceRecord->maintenance_date,
                'description' => $maintenanceRecord->description,
                'status' => $maintenanceRecord->status,
            ]);
        }

        return view('vehicles.show', compact('vehicle', 'activities'));
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
        // Validate the form data   
        $request->validate([
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'ownership' => 'required|exists:companies,id',
            'license_plate' => 'required|string|max:20',
            'year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'color' => 'required|string|max:50',
            'vehicle_type' => 'required|exists:vehicle_type,id',
            'fuel' => 'required|in:Gasoline,Diesel',
            'purchase_price' => 'nullable|numeric|min:0',
            'purchase_date' => 'nullable|date',
            'transmission' => 'required|in:Automatic,Manual',
            'tax_year' => 'required|date',
            'tax_five_year' => 'required|date',
            'inspected' => 'nullable|date',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Proses upload gambar
        $photoFile = null;
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoFile = time() . '.' . $photo->getClientOriginalName();
            $photo->storeAs('public/vehicle/photo', $photoFile);
        }

        $data = [
            'type_id' => $request->vehicle_type,
            'owner_id' => $request->ownership,
            'brand' => $request->brand,
            'model' => $request->model,
            'color' => $request->color,
            'transmission' => $request->transmission,
            'fuel' => $request->fuel,
            'year' => $request->year,
            'license_plate' => $request->license_plate,
            'tax_year' => $request->tax_year,
            'tax_five_year' => $request->tax_five_year,
            'inspected' => $request->inspected,
            'purchase_date' => $request->purchase_date,
            'purchase_price' => $request->purchase_price,
            'description' => $request->description,
            'origin' => $request->origin,
            'status' => $request->status,
            'photo' => $photoFile,
        ];

        DB::beginTransaction();
        try {
            $vehicle = Vehicle::findOrFail($id);
            $vehicle->update($data);
            DB::commit();
            return redirect()->back()->with('success', 'Vehicle ' . $vehicle->model . ' updated successfully.');
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
            $vehicle = Vehicle::find($id);
            $vehicle->delete();
            DB::commit();
            return redirect()->back()->with('success', 'Vehicle ' . $vehicle->model . ' deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Assign a vehicle to an employee. 
     */
    public function assign(Request $request, string $id)
    {
        // Validasi input
        $request->validate([
            'employee' => 'required|exists:employees,id',
            'assignment_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        // Ambil data kendaraan dan karyawan
        $vehicle = Vehicle::findOrFail($id);
        $employee = Employe::findOrFail($request->employee);

        // Periksa status kendaraan
        if ($vehicle->status === 'Inactive' || $vehicle->status === 'Maintenance') {
            return redirect()->back()->with('error', "Failed to assign $employee->full_name. Vehicle $vehicle->model is inactive or under maintenance.");
        }

        DB::beginTransaction();
        try {
            // Cek apakah karyawan sudah memiliki kendaraan yang assigned
            $existingAssignment = VehicleAssignment::where('user_id', $employee->id)
                ->whereNull('return_date')  // Pastikan hanya yang belum dikembalikan
                ->first();

            if ($existingAssignment) {
                return redirect()->back()->with('error', "Failed to assign $employee->full_name. They already have a vehicle assigned.");
            }

            // Cek apakah kendaraan sudah diassign ke karyawan lain
            $anotherAssignment = VehicleAssignment::where('vehicle_id', $vehicle->id)
                ->whereNull('return_date')
                ->first();

            // Update return_date pada assignment sebelumnya
            if ($anotherAssignment) {
                $anotherAssignment->update(['return_date' => now()]);
            }

            // Buat data assignment baru
            VehicleAssignment::create([
                'user_id' => $request->employee,
                'vehicle_id' => $vehicle->id,
                'code' => 'UD/ASSIGN/' . str_pad(VehicleAssignment::count() + 1, 4, '0', STR_PAD_LEFT),
                'assignment_date' => $request->assignment_date,
                'return_date' => null,
                'notes' => $request->notes,
            ]);

            DB::commit();
            return redirect()->back()->with('success', "Vehicle $vehicle->model assigned to $employee->full_name successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
