{{-- resources/views/petani/dashboard.blade.php --}}
@extends('layouts.dashboard')

@section('content')
  <div class="max-w-6xl mx-auto bg-white p-6 rounded-xl shadow mt-10 text-gray-800">
    <h1 class="text-2xl font-bold mb-6 text-lime-800">Riwayat Deteksi Gambar Saya</h1>

    @if(session('success'))
      <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4" role="alert">
        {{ session('success') }}
      </div>
    @endif

    <table class="min-w-full border border-gray-300 rounded-lg overflow-hidden text-sm">
      <thead class="bg-lime-700 text-white">
        <tr>
          <th class="py-2 px-4 text-left">Waktu</th>
          <th class="py-2 px-4 text-left">Gambar</th>
          <th class="py-2 px-4 text-left">Label</th>
          <th class="py-2 px-4 text-left">Rekomendasi</th>
          <th class="py-2 px-4 text-left">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($riwayat as $deteksi)
          <tr class="border-b hover:bg-lime-50" id="row-{{ $deteksi->id_deteksi }}">
            <td class="py-2 px-4">{{ $deteksi->tanggal }}</td>
            <td class="py-2 px-4">
              @if($deteksi->gambar_url && file_exists(public_path('uploads/'.$deteksi->gambar_url)))
                <img src="{{ asset('uploads/'.$deteksi->gambar_url) }}" alt="Gambar" class="h-16 rounded shadow">
              @else
                <span class="text-gray-500 italic">Tidak ada gambar</span>
              @endif
            </td>
            <td class="py-2 px-4 font-semibold text-green-700">{{ $deteksi->hasil_deteksi }}</td>
            <td class="py-2 px-4">{{ $deteksi->rekomendasi }}</td>
            <td class="py-2 px-4">
              {{-- Tombol Hapus (hanya hide baris di frontend) --}}
              <button
                type="button"
                class="btn-hapus bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 transition"
                data-id="{{ $deteksi->id_deteksi }}"
              >
                Hapus
              </button>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="py-4 px-4 text-center text-gray-500">
              Belum ada data deteksi.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>

    <div class="mt-6 flex justify-between items-center">
      <a href="{{ route('petani.deteksi.form') }}"
         class="bg-lime-600 hover:bg-lime-700 text-white font-semibold py-2 px-4 rounded-lg transition">
        Deteksi Gambar Baru
      </a>
      <a href="{{ route('petani.profile') }}"
         class="bg-lime-600 hover:bg-lime-700 text-white font-semibold py-2 px-4 rounded-lg transition">
        Profil Saya
      </a>

      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button
          type="submit"
          class="bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded-lg transition">
          Logout
        </button>
      </form>
    </div>
  </div>

  {{-- Skrip JavaScript untuk hide row dengan localStorage --}}
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      // Fungsi untuk mengambil array ID yang disembunyikan dari localStorage
      function getHiddenRows() {
        const stored = localStorage.getItem('hiddenDeteksiRows');
        return stored ? JSON.parse(stored) : [];
      }

      // Fungsi untuk menyimpan array ID yang disembunyikan ke localStorage
      function setHiddenRows(array) {
        localStorage.setItem('hiddenDeteksiRows', JSON.stringify(array));
      }

      // Atur ulang tampilan: sembunyikan baris berdasarkan localStorage
      function applyHiddenRows() {
        const hiddenRows = getHiddenRows();
        hiddenRows.forEach(function(id) {
          const row = document.getElementById('row-' + id);
          if (row) {
            row.style.display = 'none';
          }
        });
      }

      // Pada load pertama, terapkan hide sesuai localStorage
      applyHiddenRows();

      // Tangani event click tombol Hapus
      const buttons = document.querySelectorAll('.btn-hapus');
      buttons.forEach(function(btn) {
        btn.addEventListener('click', function () {
          const idDeteksi = btn.getAttribute('data-id');
          if (confirm('Yakin ingin menghapus riwayat ini dari tampilan?')) {
            // Ambil daftar ID yang disembunyikan, tambahkan id baru
            let hiddenRows = getHiddenRows();
            if (!hiddenRows.includes(idDeteksi)) {
              hiddenRows.push(idDeteksi);
              setHiddenRows(hiddenRows);
            }
            // Sembunyikan baris di tampilan
            const row = document.getElementById('row-' + idDeteksi);
            if (row) {
              row.style.display = 'none';
            }
          }
        });
      });
    });
  </script>
@endsection
