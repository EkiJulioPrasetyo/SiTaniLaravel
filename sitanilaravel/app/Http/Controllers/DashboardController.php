<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DeteksiPenyakit;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class DashboardController extends Controller
{
    // Tampilkan riwayat deteksi (Admin)
    public function index()
    {
        $user = Auth::user();
        if (!$user || $user->peran !== 'admin') {
            Auth::logout();
            return redirect()->route('login')
                             ->withErrors(['login' => 'Anda harus login sebagai Admin.']);
        }

        // Ambil semua data + eager‐load user
        $riwayat = DeteksiPenyakit::with('user')
                    ->orderBy('tanggal', 'desc')
                    ->get();

        return view('admin.dashboard', compact('riwayat'));
    }

    // Hapus satu entri (Admin)
    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user || $user->peran !== 'admin') {
            Auth::logout();
            return redirect()->route('login')
                             ->withErrors(['login' => 'Anda harus login sebagai Admin.']);
        }

        $deteksi = DeteksiPenyakit::findOrFail($id);
        $deteksi->delete();

        return redirect()->route('dashboard')
                         ->with('success', 'Entri berhasil dihapus.');
    }

    public function trend()
    {
        $user = Auth::user();
        if (!$user || $user->peran !== 'admin') {
            abort(403, 'Akses hanya untuk Admin.');
        }

        $today = Carbon::now();

        // 1) WEEKLY TRENDS: rentang Senin s/d Minggu minggu ini
        $startOfWeek = $today->copy()->startOfWeek(Carbon::MONDAY)->startOfDay();
        $endOfWeek   = $startOfWeek->copy()->addDays(6)->endOfDay();

        $weeklyTrends = DeteksiPenyakit::select('hasil_deteksi', DB::raw('COUNT(*) as total'))
            ->whereBetween('tanggal', [$startOfWeek, $endOfWeek])
            ->groupBy('hasil_deteksi')
            ->orderByDesc('total')
            ->get();

        // 2) ANALISIS BULANAN: 12 bulan terakhir
        $oneYearAgo = $today->copy()->subYear()->startOfMonth();

        $monthlyData = DeteksiPenyakit::select(
                DB::raw("DATE_FORMAT(tanggal, '%Y-%m') as periode"),
                'hasil_deteksi',
                DB::raw('COUNT(*) as total')
            )
            ->where('tanggal', '>=', $oneYearAgo)
            ->groupBy('periode', 'hasil_deteksi')
            ->orderBy('periode', 'asc')
            ->get();

        $bulanUnik = $monthlyData->pluck('periode')->unique()->sort()->values();
        $labelUnik = $monthlyData->pluck('hasil_deteksi')->unique()->values();

        // Susun matrix[periode][label] = total
        $matrix = [];
        foreach ($bulanUnik as $bulan) {
            foreach ($labelUnik as $label) {
                $matrix[$bulan][$label] = 0;
            }
        }
        foreach ($monthlyData as $row) {
            $matrix[$row->periode][$row->hasil_deteksi] = $row->total;
        }

        return view('admin.trend', [
            'weeklyTrends' => $weeklyTrends,
            'bulanUnik'    => $bulanUnik,
            'labelUnik'    => $labelUnik,
            'matrix'       => $matrix,
        ]);
    }

    /**
     * Endpoint JSON untuk AJAX polling.
     * Hanya admin juga.
     */
    public function trendDataJson()
    {
        $user = Auth::user();
        if (!$user || $user->peran !== 'admin') {
            abort(403, 'Akses hanya untuk Admin.');
        }

        $today = Carbon::now();

        $startOfWeek = $today->copy()->startOfWeek(Carbon::MONDAY)->startOfDay();
        $endOfWeek   = $startOfWeek->copy()->addDays(6)->endOfDay();

        $weeklyTrends = DeteksiPenyakit::select('hasil_deteksi', DB::raw('COUNT(*) as total'))
            ->whereBetween('tanggal', [$startOfWeek, $endOfWeek])
            ->groupBy('hasil_deteksi')
            ->orderByDesc('total')
            ->get();

        $oneYearAgo = $today->copy()->subYear()->startOfMonth();
        $monthlyData = DeteksiPenyakit::select(
                DB::raw("DATE_FORMAT(tanggal, '%Y-%m') as periode"),
                'hasil_deteksi',
                DB::raw('COUNT(*) as total')
            )
            ->where('tanggal', '>=', $oneYearAgo)
            ->groupBy('periode', 'hasil_deteksi')
            ->orderBy('periode', 'asc')
            ->get();

        $bulanUnik = $monthlyData->pluck('periode')->unique()->sort()->values();
        $labelUnik = $monthlyData->pluck('hasil_deteksi')->unique()->values();

        $matrix = [];
        foreach ($bulanUnik as $bulan) {
            foreach ($labelUnik as $label) {
                $matrix[$bulan][$label] = 0;
            }
        }
        foreach ($monthlyData as $row) {
            $matrix[$row->periode][$row->hasil_deteksi] = $row->total;
        }

        return response()->json([
            'weeklyTrends' => $weeklyTrends,
            'bulanUnik'    => $bulanUnik,
            'labelUnik'    => $labelUnik,
            'matrix'       => $matrix,
        ]);
    }
}
