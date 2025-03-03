<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Projects;

class ProjectsController extends Controller
{
    /**
     * Create a new controller instance. 
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view projects', ['only' => ['index']]);
        $this->middleware('permission:create projects', ['only' => ['create', 'store']]);
        $this->middleware('permission:update projects', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete projects', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Projects::all();
        return view('tools.projects.index', compact('projects'));
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
        // Validation
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'required|numeric',
            'email' => 'required|email',
            'ppic' => 'required|string',
            'description' => 'nullable|string',
        ]);

        // Create Project
        $data = [
            'code' => 'PRJ' . str_pad(Projects::count() + 1, 4, '0', STR_PAD_LEFT),
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
            'email' => $request->email,
            'ppic' => $request->ppic,
            'description' => $request->description,
        ];

        DB::beginTransaction();
        try {
            $projects = Projects::create($data);
            DB::commit();
            return redirect()->back()->with('success', 'Project ' . $projects->name . ' created successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
        // Validation
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'required|numeric',
            'email' => 'required|email',
            'ppic' => 'required|string',
            'description' => 'nullable|string',
        ]);

        // Update Project
        $data = [
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
            'email' => $request->email,
            'ppic' => $request->ppic,
            'description' => $request->description,
        ];

        DB::beginTransaction();
        try {
            $projects = Projects::find($id);
            $projects->update($data);
            DB::commit();
            return redirect()->back()->with('success', 'Project ' . $projects->name . ' updated successfully');
        } catch (\Exception $e) {
            DB::rollback();
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
            $projects = Projects::find($id);
            $projects->delete();
            DB::commit();
            return redirect()->back()->with('success', 'Project ' . $projects->name . ' deleted successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
