<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DeteksiPenyakit;

class PetaniController extends Controller
{
    /**
     * Menampilkan Riwayat Deteksi milik Petani (Dashboard Petani).
     * Route: GET /dashboard-petani (dashboard petani).
     */
    public function index()
    {
        $user = Auth::user();
        // Pastikan user sudah login dan benar perannya 'petani'
        if (!$user || $user->peran !== 'petani') {
            Auth::logout();
            return redirect()->route('login')
                             ->withErrors(['login' => 'Anda harus login sebagai Petani.']);
        }

        // Ambil semua data DeteksiPenyakit milik petani ini
        $riwayat = DeteksiPenyakit::where('id_akun', $user->id_akun)
                    ->orderBy('tanggal', 'desc')
                    ->get();

        // Render view resources/views/petani/dashboard.blade.php
        return view('petani.dashboard', compact('riwayat'));
    }

    /**
     * Menampilkan form deteksi (GET /petani/deteksi).
     */
    public function showDeteksiForm()
    {
        $user = Auth::user();
        if (!$user || $user->peran !== 'petani') {
            Auth::logout();
            return redirect()->route('login')
                             ->withErrors(['login' => 'Anda harus login sebagai Petani.']);
        }

        return view('petani.deteksi');
    }

    /**
     * Memproses upload & deteksi (POST /petani/deteksi).
     */
    public function processDeteksi(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->peran !== 'petani') {
            Auth::logout();
            return response()->json(['error' => 'Unauthorized.'], 403);
        }

        $request->validate([
            'gambar' => 'required|image|max:5120',
        ]);

        // Pastikan folder uploads ada
        $uploadDir = public_path('uploads');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Siapkan nama file unik
        $file      = $request->file('gambar');
        $original  = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $original  = preg_replace('/[^a-zA-Z0-9_-]/', '', $original);
        $ext       = $file->getClientOriginalExtension();
        $filename  = $original . '_' . time() . '.' . $ext;
        $filePath  = $uploadDir . '/' . $filename;
        $relative  = 'uploads/' . $filename;

        $file->move($uploadDir, $filename);

        // Panggil skrip Python dengan mematikan log TensorFlow
        $pythonBinary = 'python';       // Ganti 'python3' di Linux bila perlu
        $prefixEnv     = 'set TF_CPP_MIN_LOG_LEVEL=3 && ';
        // di Linux: $prefixEnv = 'TF_CPP_MIN_LOG_LEVEL=3 ';

        $absolutePath = escapeshellarg($filePath);
        $command      = $prefixEnv
                    . $pythonBinary . " " . base_path('predict.py')
                    . " $absolutePath 2>&1";

        $rawOutput = shell_exec($command);

        // (Opsional) Simpan debug log
        file_put_contents(storage_path('logs/deteksi_debug.log'),
            "CMD: $command\nOutput:\n$rawOutput\n\n", FILE_APPEND
        );

        // Ambil label dan rekomendasi
        $deteksiLabel = trim($rawOutput);

        $rekomendasiPenanganan = [
            "Healthy"    => "Tanaman dalam kondisi sehat. Lanjutkan perawatan rutin seperti penyiraman dan pemupukan.",
            "Leaf Curl"  => "Gunakan insektisida sistemik untuk mengatasi serangan kutu daun atau thrips. Pangkas daun yang terinfeksi.",
            "Leaf Spot"  => "Gunakan fungisida berbasis tembaga. Jaga kelembaban daun tetap rendah dengan penyiraman di pangkal.",
            "White Fly"  => "Semprot dengan sabun insektisida atau neem oil. Gunakan perangkap kuning lengket.",
            "Yellowish"  => "Periksa pH tanah dan tingkat nutrisi. Tambahkan pupuk NPK seimbang dan perbaiki drainase.",
        ];

        if ($deteksiLabel === "" || strtoupper($deteksiLabel) === "UNKNOWN") {
            // Bila tidak ada output dari Python atau label 'Unknown'
            $rekomendasi = "";
        } else {
            // Ambil rekomendasi berdasarkan key yang persis sama
            $rekomendasi = $rekomendasiPenanganan[$deteksiLabel]
                            ?? "Rekomendasi belum tersedia.";
        }

        // Simpan hasil ke database
        DeteksiPenyakit::create([
            'id_akun'       => $user->id_akun,
            'gambar_url'    => $filename,
            'hasil_deteksi' => $deteksiLabel,
            'rekomendasi'   => $rekomendasi,
            'tanggal'       => now()->format('Y-m-d H:i:s'),
        ]);

        // Kirim response ke frontend
        $responseText = $deteksiLabel;
        if ($rekomendasi !== "") {
            $responseText .= "\nRekomendasi: $rekomendasi";
        }

        return response($responseText, 200)
            ->header('Content-Type', 'text/plain; charset=utf-8');
    }


    /**
     * Hapus satu entri DeteksiPenyakit milik Petani (DELETE /dashboard-petani/{id}).
     */
    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user || $user->peran !== 'petani') {
            Auth::logout();
            return redirect()->route('login')
                             ->withErrors(['login' => 'Anda harus login sebagai Petani.']);
        }

        $deteksi = DeteksiPenyakit::where('id_deteksi', $id)
                    ->where('id_akun', $user->id_akun)
                    ->firstOrFail();

        $deteksi->delete();

        return redirect()->route('dashboard.petani')
                         ->with('success', 'Riwayat deteksi berhasil dihapus.');
    }
}
