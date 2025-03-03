<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class PermissionsController extends Controller
{
    /**
     * Create a new controller instance. 
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view permissions', ['only' => ['index']]);
        $this->middleware('permission:create permissions', ['only' => ['create', 'store']]);
        $this->middleware('permission:update permissions', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete permissions', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $data = [
            'name' => $request->name,
        ];

        DB::beginTransaction();
        try {
            $permissions = Permission::create($data);
            DB::commit();
            return redirect()->back()->with('success', 'Permission ' . $permissions->name . ' created successfully.');
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
        return view();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $data = [
            'name' => $request->name,
        ];

        DB::beginTransaction();
        try {
            $permissions = Permission::findOrFail($id);
            $permissions->name = $data['name'];
            $permissions->save();
            DB::commit();
            return redirect()->back()->with('success', 'Permission ' . $permissions->name . ' updated successfully.');
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
            $permissions = Permission::findOrFail($id);
            $permissions->delete();
            DB::commit();
            return redirect()->back()->with('success', 'Permission ' . $permissions->name . ' deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
