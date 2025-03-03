<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /*
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view profile', ['only' => ['index']]);
        $this->middleware('permission:update profile', ['only' => ['update']]);
    }

    /*
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('profile');
    }

    /*
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
        return view('profile');
    }

    /*
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . Auth::user()->id,
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:8|max:12|required_with:current_password',
            'password_confirmation' => 'nullable|min:8|max:12|required_with:new_password|same:new_password'
        ]);


        $user = User::findOrFail(Auth::user()->id);
        $user->name = $request->input('name');
        $user->last_name = $request->input('last_name');
        $user->email = $request->input('email');

        if (!is_null($request->input('current_password'))) {
            if (Hash::check($request->input('current_password'), $user->password)) {
                $user->password = $request->input('new_password');
            } else {
                return redirect()->back()->withInput();
            }
        }

        $user->save();

        return redirect()->route('profile')->withSuccess('Profile updated successfully.');
    }

    public function updateImage(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $fileName = null;
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $fileName = $request->first_name . '.' . $file->getClientOriginalExtension();

            // hapus file lama 
            if (Auth::user()->employe->photo && Storage::exists('public/employees/photo/' . Auth::user()->employe->photo)) {
                Storage::delete('public/employees/photo/' . Auth::user()->employe->photo);
            }

            $file->storeAs('public/employees/photo', $fileName);
        }

        DB::beginTransaction();
        try {
            $user = User::with('employe')->findOrFail(Auth::user()->id);
            $user->employe->photo = $fileName;
            $user->employe->save();
            DB::commit();
            return redirect()->route('profile')->withSuccess('Profile updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withError($e->getMessage());
        }
    }
}
