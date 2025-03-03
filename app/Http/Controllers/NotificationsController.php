<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Models\Notifications;
use App\Models\User;

class NotificationsController extends Controller
{
    /**
     * Create a new controller instance.
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view notifications', ['only' => ['index']]);
        $this->middleware('permission:show notifications', ['only' => ['show']]);
        $this->middleware('permission:create notifications', ['only' => ['create', 'store']]);
        $this->middleware('permission:update notifications', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete notifications', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $notifications = Notifications::all();
        $roles = Role::all();
        return view('settings.usersmanagement.notifications', compact('notifications', 'roles'));
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
            'template' => 'required|string',
            'description' => 'nullable|string',
            'roles' => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            $notifications = Notifications::create([
                'name' => $request->name,
                'template' => $request->template,
                'description' => $request->description,
                'roles' => json_encode($request->roles),
            ]);
            DB::commit();
            return redirect()->back()->with('success', 'Notification ' . $notifications->name . ' created successfully.');
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
            'template' => 'required|string',
            'description' => 'nullable|string',
            'roles' => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            $notifications = Notifications::findOrFail($id);
            $notifications->update([
                'name' => $request->name,
                'template' => $request->template,
                'description' => $request->description,
                'roles' => json_encode($request->roles),
            ]);
            DB::commit();
            return redirect()->back()->with('success', 'Notification ' . $notifications->name . ' updated successfully.');
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
            $notifications = Notifications::findOrFail($id);
            $notifications->delete();
            DB::commit();
            return redirect()->back()->with('success', 'Notification ' . $notifications->name . ' deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
