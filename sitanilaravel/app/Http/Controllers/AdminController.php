<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Atau App\Models\Akun (sesuaikan dengan model Anda)

class AdminController extends Controller
{
    /**
     * Menampilkan halaman profil admin beserta daftar petani.
     */
    public function showProfile(Request $request)
    {
        // 1. Cek user sudah login & peran = 'admin'
        $user = Auth::user();
        if (!$user || $user->peran !== 'admin') {
            // Jika bukan admin, redirect ke login atau halaman lain
            return redirect()->route('login')
                             ->withErrors(['login' => 'Akses hanya untuk Admin.']);
        }

        // 2. Ambil data petani (dengan optional pencarian 'cari')
        $keyword = $request->query('cari');
        $petani = User::where('peran', 'petani')
                      ->when($keyword, function($q) use ($keyword) {
                          $q->where('nama', 'like', "%{$keyword}%")
                            ->orWhere('username', 'like', "%{$keyword}%");
                      })
                      ->get();

        // 3. Kirim data ke view 'admin.profile':
        return view('admin.profile', [
            'adminData' => $user->only(['nama', 'email']),
            'petani'    => $petani,
            'keyword'   => $keyword,
        ]);
    }

    /**
     * Memproses perubahan profil admin (nama & email).
     */
    public function updateProfile(Request $request)
    {
        // 1. Cek peran admin
        $user = Auth::user();
        if (!$user || $user->peran !== 'admin') {
            return redirect()->route('login')
                             ->withErrors(['login' => 'Akses hanya untuk Admin.']);
        }

        // 2. Validasi input
        $request->validate([
            'nama'  => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        // 3. Simpan perubahan ke database
        $user->nama  = $request->nama;
        $user->email = $request->email;
        $user->save();

        // 4. Redirect kembali dengan flash message sukses
        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Menghapus akun petani berdasarkan ID.
     */
    public function destroyPetani($id)
    {
        // 1. Cek peran admin
        $user = Auth::user();
        if (!$user || $user->peran !== 'admin') {
            return redirect()->route('login')
                             ->withErrors(['login' => 'Akses hanya untuk Admin.']);
        }

        // 2. Cari akun petani berdasarkan kolom id_akun (sesuaikan nama kolom di tabel Anda)
        $petani = User::where('id_akun', $id)
                      ->where('peran', 'petani')
                      ->firstOrFail();

        // 3. Hapus record petani
        $petani->delete();

        // 4. Redirect ke profil admin dengan pesan sukses
        return redirect()->route('admin.profile')
                         ->with('success', 'Akun petani berhasil dihapus.');
    }
}
