<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\VehicleInsurance;
use App\Models\Vehicle;

class VehicleInsuranceController extends Controller
{
    /*
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view vehicle insurances', ['only' => ['index']]);
        $this->middleware('permission:show vehicle insurances', ['only' => ['show']]);
        $this->middleware('permission:create vehicle insurances', ['only' => ['create', 'store']]);
        $this->middleware('permission:update vehicle insurances', ['only' => ['edit', 'update']]);
        $this->middleware('permission:print vehicle insurances', ['only' => ['exportPdf']]);
        $this->middleware('permission:delete vehicle insurances', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $insurances = VehicleInsurance::with('vehicle')->get();
        return view('vehicles.insurence.insurances', compact('insurances'));
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
            'vehicle_code' => 'required|exists:vehicle,code',
            'insurance_provider' => 'required|string|max:255',
            'policy_number' => 'required|string|max:255',
            'coverage_start' => 'required|date',
            'coverage_end' => 'required|date',
            'premium' => 'required|numeric',
            'notes' => 'nullable|string|max:255',
        ]);

        // Create a new vehicle insurance record
        $vehicle = Vehicle::where('code', $request->vehicle_code)->first();
        $data = [
            'vehicle_id' => $vehicle->id,
            'code' => 'INS-' . str_pad(VehicleInsurance::count() + 1, 4, '0', STR_PAD_LEFT),
            'insurance_provider' => $request->insurance_provider,
            'policy_number' => $request->policy_number,
            'coverage_start' => $request->coverage_start,
            'coverage_end' => $request->coverage_end,
            'premium' => $request->premium,
            'notes' => $request->notes,
        ];

        DB::beginTransaction();
        try {
            VehicleInsurance::create($data);
            DB::commit();
            return redirect()->back()->with('success', 'Insurance ' . $vehicle->model . ' Successfully Created');
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
        $insurances = VehicleInsurance::with('vehicle')->findOrFail($id);
        return view('vehicles.insurence.show', compact('insurances'));
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
            'insurance_provider' => 'required|string|max:255',
            'policy_number' => 'required|string|max:255',
            'coverage_start' => 'required|date',
            'coverage_end' => 'required|date',
            'premium' => 'required|numeric',
        ]);

        // Update the vehicle insurance record
        $insurances = VehicleInsurance::where('id', $id)->first();
        $data = [
            'insurance_provider' => $request->insurance_provider,
            'policy_number' => $request->policy_number,
            'coverage_start' => $request->coverage_start,
            'coverage_end' => $request->coverage_end,
            'premium' => $request->premium,
        ];

        DB::beginTransaction();
        try {
            $insurances->update($data);
            DB::commit();
            return redirect()->back()->with('success', 'Insurance Successfully Updated');
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
            $insurances = VehicleInsurance::findOrFail($id);
            $insurances->delete();
            DB::commit();
            return redirect()->back()->with('success', 'Insurance' . $insurances->vehicle->name . ' Successfully Deleted');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    /**
     * Export PDF of the specified resource.
     */
    public function exportPdf($id)
    {
        $insurances = VehicleInsurance::with('vehicle')->findOrFail($id);
        $pdf = PDF::loadView('vehicles.insurence.pdf', compact('insurances'));
        return $pdf->stream('insurance.pdf');
    }
}
