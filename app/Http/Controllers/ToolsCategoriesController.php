<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ToolsCategorie;

class ToolsCategoriesController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view tools categories', ['only' => ['index']]);
        $this->middleware('permission:create tools categories', ['only' => ['create', 'store']]);
        $this->middleware('permission:update tools categories', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete tools categories', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
            'description' => 'nullable|string',
        ]);

        // Create a new tool category
        $data = [
            'code' => 'TC' . str_pad(ToolsCategorie::count() + 1, 4, '0', STR_PAD_LEFT),
            'name' => $request->name,
            'description' => $request->description,
        ];

        // Save the tool category
        DB::beginTransaction();
        try {
            $toolCategory = ToolsCategorie::create($data);
            DB::commit();
            return redirect()->back()->with('success', 'Tool category ' . $toolCategory->name . ' created successfully');
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
        // Validate the form data
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Update the tool category
        $data = [
            'name' => $request->name,
            'description' => $request->description,
        ];

        DB::beginTransaction();
        try {
            $toolCategory = ToolsCategorie::findOrFail($id);
            $toolCategory->update($data);
            DB::commit();
            return redirect()->back()->with('success', 'Tool category ' . $toolCategory->name . ' updated successfully');
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
        // Delete the tool category
        DB::beginTransaction();
        try {
            $toolCategory = ToolsCategorie::findOrFail($id);
            $toolCategory->delete();
            DB::commit();
            return redirect()->back()->with('success', 'Tool category ' . $toolCategory->name_categorie . ' deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
