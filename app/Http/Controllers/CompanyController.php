<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view companies', ['only' => ['index']]);
        $this->middleware('permission:show companies', ['only' => ['show']]);
        $this->middleware('permission:create companies', ['only' => ['create', 'store']]);
        $this->middleware('permission:update companies', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete companies', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $companies = Company::all();
        return view('settings.companymanage.company', compact('companies'));
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
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'phone' => 'required|digits_between:10,15',
            'address' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        // Save the company
        DB::beginTransaction();
        try {
            Company::create([
                'code' => 'C' . str_pad(Company::count() + 1, 5, '0', STR_PAD_LEFT),
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'description' => $request->description
            ]);
            DB::commit();
            return redirect()->back()->with('success', 'Company ' . $request->company . ' created successfully');
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
        $companies = Company::find($id);
        return view('settings.companymanage.companyshow', compact('companies'));
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
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'phone' => 'required|digits_between:10,15',
            'address' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        // Save the company
        DB::beginTransaction();
        try {
            $companies = Company::find($id);
            $companies->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'description' => $request->description
            ]);
            DB::commit();
            return redirect()->back()->with('success', 'Company ' . $request->company . ' updated successfully');
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
            $companies = Company::find($id);
            $companies->delete();
            DB::commit();
            return redirect()->back()->with('success', 'Company ' . $companies->company . ' deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
