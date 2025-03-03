<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Branch;
use App\Models\Company;

class BranchController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view branches', ['only' => ['index']]);
        $this->middleware('permission:show branches', ['only' => ['show']]);
        $this->middleware('permission:create branches', ['only' => ['create', 'store']]);
        $this->middleware('permission:update branches', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete branches', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $branches = Branch::all();
        $companies = Company::all();
        return view('settings.companymanage.branch', compact('branches', 'companies'));
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
        // validate the request
        $request->validate([
            'company_id' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'phone' => 'required|digits_between:10,15',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'type' => 'required|string|in:Head Office,Branch Office',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // store the photo
        $photoFile = null;
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoFile = time() . '.' . $photo->getClientOriginalExtension();
            $photo->storeAs('public/branch/photo', $photoFile);
        }

        DB::beginTransaction();
        try {
            Branch::create([
                'company_id' => $request->company_id,
                'code' => 'B' . str_pad(Branch::count() + 1, 5, '0', STR_PAD_LEFT),
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'description' => $request->description,
                'type' => $request->type,
                'photo' => $photoFile
            ]);
            DB::commit();
            return redirect()->back()->with('success', 'Branch ' . $request->name . ' created successfully');
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
        $branches = Branch::with('company')->findOrFail($id);
        return view('settings.companymanage.branchshow', compact('branches'));
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
        // validate the request
        $request->validate([
            'company_id' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'phone' => 'required|digits_between:10,15',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'type' => 'required|string|in:Head Office,Branch Office',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // store the photo
        $branch = Branch::find($id);
        $photoFile = $branch->photo;
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoFile = time() . '.' . $photo->getClientOriginalExtension();

            if ($branch->photo && Storage::exists('public/branch/photo/' . $branch->photo)) {
                Storage::delete('public/branch/photo/' . $branch->photo);
            }

            $photo->storeAs('public/branch/photo', $photoFile);
        }

        DB::beginTransaction();
        try {
            $branch->update([
                'company_id' => $request->company_id,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'status' => $request->status,
                'description' => $request->description,
                'type' => $request->type,
                'photo' => $photoFile
            ]);
            DB::commit();
            return redirect()->back()->with('success', 'Branch ' . $branch->name . ' updated successfully');
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
            $branch = Branch::find($id);
            $branch->delete();
            DB::commit();
            return redirect()->back()->with('success', 'Branch ' . $branch->name . ' deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
