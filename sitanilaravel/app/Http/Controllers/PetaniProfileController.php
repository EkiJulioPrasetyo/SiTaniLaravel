<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // atau App\Models\Akun jika Anda memakai model Akun

class PetaniProfileController extends Controller
{
    /**
     * Menampilkan halaman Profil Petani (GET /petani/profile)
     */
    public function show(Request $request)
    {
        // 1) Pastikan user sudah login
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')
                             ->withErrors(['login' => 'Silakan login terlebih dahulu.']);
        }

        // 2) Pastikan peran = 'petani'
        if ($user->peran !== 'petani') {
            abort(403, 'Akses hanya untuk Petani.');
        }

        // 3) Render view dan kirim data user
        return view('petani.profile', [
            'userData' => [
                'nama'     => $user->nama,
                'email'    => $user->email,
                'username' => $user->username,
                'peran'    => $user->peran,
            ],
        ]);
    }

    /**
     * Memproses update Profil Petani (POST /petani/profile)
     */
    public function update(Request $request)
    {
        // 1) Pastikan user sudah login
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')
                             ->withErrors(['login' => 'Silakan login terlebih dahulu.']);
        }

        // 2) Pastikan peran = 'petani'
        if ($user->peran !== 'petani') {
            abort(403, 'Akses hanya untuk Petani.');
        }

        // 3) Validasi input
        $request->validate([
            'nama'  => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        // 4) Update ke database
        $akun = User::findOrFail($user->id_akun);
        $akun->nama  = $request->nama;
        $akun->email = $request->email;
        $akun->save();

        // 5) Redirect kembali dengan flash message
        return redirect()->route('petani.profile')
                         ->with('success', 'Profil berhasil diperbarui.');
    }
}
