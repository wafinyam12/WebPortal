<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use App\Providers\RouteServiceProvider;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SAPServices;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function redirectTo()
    {
        session()->flash('success', 'You are logged in!');
        return $this->redirectTo;
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        $loginField = $this->username();
        $userExists = User::where($loginField, $request->username)->exists();

        if (!$userExists) {
            throw ValidationException::withMessages([
                $loginField => ['Akun dengan username atau email ini tidak ditemukan.'],
            ]);
        }

        throw ValidationException::withMessages([
            'password' => ['Password yang dimasukkan salah.'],
        ]);
    }

    public function username()
    {
        $login = request()->input('username');
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        request()->merge([$field => $login]);
        return $field;
    }

    protected function authenticated(Request $request, $user)
    {
        // Ambil nama branch melalui relasi
        $branchDB = $user->employe->branch->database ?? null;

        // Simpan nama branch ke dalam session
        Session::put('company_db', $branchDB);
    }

    public function logout(Request $request)
    {
        try {
            app(SAPServices::class)->logout();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        // Lanjutkan proses logout Laravel
        $this->guard()->logout();

        // Invalidate session
        $request->session()->invalidate();

        // Regenerate token
        $request->session()->regenerateToken();

        // Jika ada respon khusus setelah logout
        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect('/login');
    }
}
