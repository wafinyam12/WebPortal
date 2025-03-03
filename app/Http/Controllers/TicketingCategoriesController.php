<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\TicketsCategories;

class TicketingCategoriesController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view ticketing categories', ['only' => ['index']]);
        $this->middleware('permission:create ticketing categories', ['only' => ['create', 'store']]);
        $this->middleware('permission:update ticketing categories', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete ticketing categories', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = TicketsCategories::all();
        return view('ticketing.category', compact('categories'));
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
            'description' => 'required|string',
        ]);

        $data = [
            'name' => $request->name,
            'slug' => str_replace(' ', '-', strtolower($request->name)),
            'description' => $request->description
        ];

        DB::beginTransaction();
        try {
            $category = TicketsCategories::create($data);
            DB::commit();
            return redirect()->back()->with('success', 'Category ' . $category->name . ' created successfully');
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

        $data = [
            'name' => $request->name,
            'slug' => str_replace(' ', '-', strtolower($request->name)),
            'description' => $request->description
        ];

        DB::beginTransaction();
        try {
            $category = TicketsCategories::findOrFail($id);
            $category->update($data);
            DB::commit();
            return redirect()->back()->with('success', 'Category ' . $category->name . ' updated successfully');
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
            $category = TicketsCategories::findOrFail($id);
            $category->delete();
            DB::commit();
            return redirect()->back()->with('success', 'Category ' . $category->name . ' deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
