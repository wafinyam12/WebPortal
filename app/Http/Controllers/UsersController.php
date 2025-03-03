<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Company;
use App\Models\Department;
use App\Models\Branch;

class UsersController extends Controller
{
    /**
     * 
     * Create a new controller instance.
     * 
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view users', ['only' => ['index']]);
        $this->middleware('permission:create users', ['only' => ['create', 'store', 'importUsers']]);
        $this->middleware('permission:update users', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete users', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get all users & roles
        $users = User::with('employe')->get();
        $roles = Role::all();
        $companies = Company::all();
        $departments = Department::all();
        $branches = Branch::all();
        return view('settings.usersmanagement.index', compact('users', 'roles', 'companies', 'departments', 'branches'));
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
        // Validasi input dari form
        $request->validate([
            // 'nik' => 'nullable|string|max:16|unique:employees,nik',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'required|exists:roles,id',
            'phone' => 'required|digits_between:10,15|unique:employees,phone',
            'gender' => 'required|in:Male,Female',
            // 'age' => 'required|integer|min:0',
            'position' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'company_id' => 'required|exists:companies,id',
            'department_id' => 'required|exists:departments,id',
            'branch_id' => 'required|exists:branches,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Upload photo
        $fileName = null;
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $fileName = $request->first_name . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/employees/photo', $fileName);
        }

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->first_name,
                'username' => $request->username,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'email_verified_at' => now(),
                'password' => Hash::make($request->password),
                'remember_token' => Str::random(10),
            ]);
            $user->employe()->create([
                'user_id' => $user->id,
                'company_id' => $request->company_id,
                'department_id' => $request->department_id,
                'branch_id' => $request->branch_id,
                'code' => 'EMP' . str_pad(User::count() + 1, 5, '0', STR_PAD_LEFT),
                'nik' => $request->nik,
                'full_name' => $request->first_name . ' ' . $request->last_name,
                'gender' => $request->gender,
                'phone' => $request->phone,
                'address' => $request->address,
                'position' => $request->position,
                // 'age' => $request->age,
                'photo' => $fileName
            ]);
            // Assign Role
            $role = Role::findById($request->roles);
            $user->assignRole($role->name);
            DB::commit();
            return redirect()->route('users.index')->with('success', 'User ' . $user->name . ' created successfully.');
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
        $users = User::with('employe.company', 'employe.department', 'employe.branch')->findOrFail($id);
        return view('settings.usersmanagement.showuser', compact('users'));
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
        // Validasi input
        $request->validate([
            'status' => 'required|in:Active,Inactive',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'username' => 'required|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'required|exists:roles,id',
            'phone' => 'required|numeric|digits_between:10,15',
            'gender' => 'required|in:Male,Female',
            // 'age' => 'required|integer|min:0',
            'position' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'company_id' => 'required|exists:companies,id',
            'department_id' => 'required|exists:departments,id',
            'branch_id' => 'required|exists:branches,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Upload photo jika ada file baru
        $fileName = User::findOrFail($id)->employe->photo;
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $fileName = $request->first_name . '.' . $file->getClientOriginalExtension();

            // Hapus foto lama jika ada
            if (User::findOrFail($id)->employe->photo && Storage::exists('public/employees/photo/' . User::findOrFail($id)->employe->photo)) {
                Storage::delete('public/employees/photo/' . User::findOrFail($id)->employe->photo);
            }

            // Simpan file photo baru ke storage
            $file->storeAs('public/employees/photo', $fileName);
        }

        // jika password tidak diubah gunakan password lama
        if (empty($request->password)) {
            $request->merge(['password' => User::findOrFail($id)->password]);
        }

        // Update data to database
        DB::beginTransaction();
        try {
            // Get the user & employe data
            $users = User::with('employe')->findOrFail($id);
            $users->update([
                'name' => $request->first_name,
                'email' => $request->email,
                'username' => $request->username,
                'last_name' => $request->last_name,
                'password' => $request->password,
            ]);

            $users->employe()->update([
                'user_id' => $users->id,
                'company_id' => $request->company_id,
                'branch_id' => $request->branch_id,
                'department_id' => $request->department_id,
                'full_name' => $request->first_name . ' ' . $request->last_name,
                'gender' => $request->gender,
                // 'age' => $request->age,
                'phone' => $request->phone,
                'position' => $request->position,
                'address' => $request->address,
                'status' => $request->status,
                'photo' => $fileName
            ]);

            // Assign Role
            if ($request->has('roles')) {
                $role = Role::findById($request->roles);
                $users->assignRole($role->name);
            }
            DB::commit();
            return redirect()->back()->with('success', 'User ' . $users->name . ' updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update user. ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $user = User::findOrFail($id);
            $user->delete();
            $user->employe()->delete();
            DB::commit();
            return redirect()->back()->with('success', 'User ' . $user->fullName . ' deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function exportExcel(Request $request)
    {
        // Validate the request
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        // Get the start date and end date
        $startDate = request('start_date');
        $endDate = request('end_date');

        $users = User::whereBetween('created_at', [$startDate, $endDate])->get();

        // return Excel::download(new UsersExport($users), 'users.xlsx');
    }

    public function importUsers(Request $request)
    {
        // Validate the request
        $request->validate([
            'file' => 'required|mimes:csv,xlsx, xls',
        ]);

        $file = $request->file('file');

        //move file to storage
        $path = $file->store('import');
        $file = storage_path('public/uploads/users/' . $path);

        // Excel::import(new UsersImport, $file);

        return redirect()->back()->with('success', 'Users imported successfully.');
    }
}
