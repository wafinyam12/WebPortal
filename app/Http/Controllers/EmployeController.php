<?php

namespace App\Http\Controllers;

use App\Models\Employe;
use Illuminate\Http\Request;

class EmployeController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view employees', ['only' => ['index']]);
        $this->middleware('permission:show employees', ['only' => ['show']]);
        $this->middleware('permission:create employees', ['only' => ['create', 'store']]);
        $this->middleware('permission:update employees', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete employees', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = Employe::all();
        return view('settings.companymanage.employe', compact('employees'));
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $employees = Employe::with('company', 'branch', 'department', 'user')->findOrFail($id);
        return view('settings.companymanage.employeshow', compact('employees'));
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
