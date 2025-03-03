<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AsuransiProject;
use App\Models\Projects;

class AsuransiProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view asuransi project', ['only' => ['index']]);
    }
    public function index()
    {
        $project = Projects::all();
        // return $project;
        $asuransi = AsuransiProject::all();
        return view('tools.asuransi.index', compact('asuransi', 'project'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'project_id' => 'required|exists:projects,id', // Pastikan project ID valid
            'name' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'masa_berlaku' => 'required|string',
            'tanggal_jatuh_tempo' => 'required|date',
            'status' => 'required|string',
            'catatan' => 'nullable|string',
        ]);
        // return $validatedData;

        AsuransiProject::create($request->all());
        return redirect()->back()->with('success', 'Data asuransi berhasil disimpan.');
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
