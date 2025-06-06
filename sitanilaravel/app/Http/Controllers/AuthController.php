<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\DB;     // <— import DB facade
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;



class AuthController extends Controller
{
    // Tampilkan form login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Proses login
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
            'peran'    => 'required|in:admin,petani',
        ]);

        // Cari user berdasarkan username & peran
        $user = User::where('username', $request->username)
                    ->where('peran', $request->peran)
                    ->first();

        if ($user && Hash::check($request->password, $user->password)) {
            Auth::login($user);
            // Simpan nama + peran di session (opsional)
            session(['nama' => $user->nama, 'peran' => $user->peran]);

            // Redirect berdasarkan peran
            if ($user->peran === 'admin') {
                return redirect()->route('dashboard');
            } elseif ($user->peran === 'petani') {
                return redirect()->route('dashboard.petani');
            } else {
                Auth::logout();
                return back()
                    ->withErrors(['login' => 'Peran tidak dikenal.'])
                    ->withInput();
            }
        }

        // Kredensial salah
        return back()
            ->withErrors(['login' => 'Username, password, atau peran salah.'])
            ->withInput();
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function showResetPasswordForm()
    {
        return view('auth.reset-password');
    }

    /**
     * Proses Reset Password (POST /resetpassword)
     */
    public function processResetPassword(Request $request)
    {
        // Validasi input
        $request->validate([
            'username'           => 'required|string',
            'email'              => 'required|email',
            'password_baru'      => 'required|string|min:6|confirmed',
        ]);

        // Cari akun berdasarkan username & email
        $akun = DB::table('Akun')
            ->where('username', $request->username)
            ->where('email', $request->email)
            ->first();

        if (!$akun) {
            return back()
                ->withErrors(['email' => 'Username atau email tidak cocok.'])
                ->withInput();
        }

        // Hash password baru dan update DB
        DB::table('Akun')
            ->where('id_akun', $akun->id_akun) // gunakan primary key id_akun
            ->update([
                'password' => Hash::make($request->password_baru),
            ]);

        return redirect()->route('login')
                         ->with('success', 'Password berhasil diperbarui. Silakan login.');
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Proses data registrasi (POST /register).
     */
    public function processRegistration(Request $request)
    {
        // 1. Validasi input
        $validator = Validator::make($request->all(), [
            'nama'      => 'required|string|max:255',
            'username'  => [
                'required',
                'string',
                'max:50',
                Rule::unique('Akun', 'username'),
            ],
            'email'     => [
                'required',
                'email',
                'max:255',
                Rule::unique('Akun', 'email'),
            ],
            'password'  => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        // 2. Jika validasi berhasil, simpan akun baru
        $akun = new Akun();
        $akun->nama     = $request->nama;
        $akun->username = $request->username;
        $akun->email    = $request->email;
        // Hash password
        $akun->password = Hash::make($request->password);
        // Set peran default
        $akun->peran    = 'petani';
        $akun->save();

        // 3. Redirect ke halaman login dengan pesan sukses
        return redirect()->route('login')
                         ->with('success', 'Registrasi berhasil! Silakan login.');
    }
}
