@extends('layouts.dashboard')

@section('content')
  <div class="max-w-6xl mx-auto bg-white p-6 rounded-xl shadow">
    <h1 class="text-2xl font-bold mb-6 text-lime-800">Riwayat Semua Deteksi Gambar</h1>

    {{-- Flash message sukses --}}
    @if(session('success'))
      <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4" role="alert">
        {{ session('success') }}
      </div>
    @endif

    <table class="min-w-full border border-gray-300 rounded-lg overflow-hidden text-sm">
      <thead class="bg-lime-700 text-white">
        <tr>
          <th class="py-2 px-4 text-left">Waktu</th>
          <th class="py-2 px-4 text-left">Pengguna</th>
          <th class="py-2 px-4 text-left">Gambar</th>
          <th class="py-2 px-4 text-left">Label</th>
          <th class="py-2 px-4 text-left">Rekomendasi</th>
          <th class="py-2 px-4 text-left">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($riwayat as $deteksi)
          <tr class="border-b hover:bg-lime-50">
            <td class="py-2 px-4">{{ $deteksi->tanggal }}</td>
            <td class="py-2 px-4">{{ $deteksi->user->nama ?? '-' }}</td>
            <td class="py-2 px-4">
              @if($deteksi->gambar_url && file_exists(public_path('uploads/' . $deteksi->gambar_url)))
                <img src="{{ asset('uploads/' . $deteksi->gambar_url) }}"
                     alt="Gambar" class="h-16 rounded shadow">
              @else
                <span class="text-gray-500 italic">Tidak ada gambar</span>
              @endif
            </td>
            <td class="py-2 px-4 font-semibold text-green-700">{{ $deteksi->hasil_deteksi }}</td>
            <td class="py-2 px-4">{{ $deteksi->rekomendasi }}</td>
            <td class="py-2 px-4">
              <form method="POST"
                    action="{{ route('dashboard.destroy', $deteksi->id_deteksi) }}"
                    onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 transition">
                  Hapus
                </button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="py-4 px-4 text-center text-gray-500">
              Belum ada data deteksi.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-6 text-center">
    <form id="logout-form" action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit"
                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded shadow">
            Logout
        </button>
    </form>
    <form id="profile-form" action="{{ route('admin.profile') }}" method="GET" class="inline-block ml-2">
        <button type="submit"
                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded shadow">
          Profile
        </button>
    </form>
    <form id="profile-form" action="{{ route('admin.trend') }}" method="GET" class="inline-block ml-2">
        <button type="submit"
                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded shadow">
          Trend
        </button>
    </form>
  </div>
@endsection
