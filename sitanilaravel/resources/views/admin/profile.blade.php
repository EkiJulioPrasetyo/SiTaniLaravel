@extends('layouts.dashboard')

@section('content')
  <div class="bg-green-50 min-h-screen py-10 px-6">
    <div class="max-w-5xl mx-auto space-y-10">
      {{-- Pesan sukses / error --}}
      @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4" role="alert">
          {{ session('success') }}
        </div>
      @endif

      @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
          <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      {{-- 1) FORM UBAH PROFILE ADMIN --}}
      <div class="bg-white p-6 rounded-xl shadow">
        <h2 class="text-xl font-bold text-lime-700 mb-4">Profil Admin</h2>
        <form action="{{ route('admin.profile.update') }}" method="POST" class="space-y-4">
          @csrf

          <div>
            <label class="block text-sm font-medium text-gray-700">Nama</label>
            <input type="text"
                   name="nama"
                   value="{{ old('nama', $adminData['nama']) }}"
                   class="w-full border border-gray-300 px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-lime-600"
                   required />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email"
                   name="email"
                   value="{{ old('email', $adminData['email']) }}"
                   class="w-full border border-gray-300 px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-lime-600"
                   required />
          </div>

          <button type="submit"
                  class="bg-lime-600 hover:bg-lime-700 text-white font-semibold px-4 py-2 rounded-lg transition">
            Simpan Perubahan
          </button>
        </form>
      </div>

      {{-- 2) DAFTAR AKUN PETANI + SEARCH FORM --}}
      <div class="bg-white p-6 rounded-xl shadow">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-xl font-bold text-lime-700">Daftar Akun Petani</h2>

          <form action="{{ route('admin.profile') }}" method="GET" class="flex gap-2">
            <input type="text"
                   name="cari"
                   placeholder="Cari nama/username..."
                   value="{{ old('cari', $keyword) }}"
                   class="border border-gray-300 px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-lime-600" />
            <button type="submit"
                    class="bg-lime-600 hover:bg-lime-700 text-white px-4 py-2 rounded transition">
              Cari
            </button>
          </form>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-sm border border-gray-200">
            <thead class="bg-lime-700 text-white">
              <tr>
                <th class="px-4 py-2 text-left">Nama</th>
                <th class="px-4 py-2 text-left">Username</th>
                <th class="px-4 py-2 text-left">Email</th>
                <th class="px-4 py-2 text-left">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($petani as $p)
                <tr class="border-t">
                  <td class="px-4 py-2">{{ $p->nama }}</td>
                  <td class="px-4 py-2">{{ $p->username }}</td>
                  <td class="px-4 py-2">{{ $p->email }}</td>
                  <td class="px-4 py-2">
                    {{-- Tombol Hapus --}}
                    <form action="{{ route('admin.petani.destroy', $p->id_akun) }}"
                          method="POST"
                          onsubmit="return confirm('Yakin ingin menghapus akun ini?')"
                          class="inline-block">
                      @csrf
                      @method('DELETE')
                      <button type="submit"
                              class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded transition">
                        Hapus
                      </button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="text-center py-4 text-gray-500">
                    Tidak ada data ditemukan.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      {{-- 3) Tombol Logout & Profil --}}
      <div class="mt-6 text-center">
        {{-- Logout --}}
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="inline-block">
          @csrf
          <button type="submit"
                  class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded shadow">
            Logout
          </button>
        </form>
         {{-- Link Kembali ke Dashboard --}}
        <a href="{{ route('dashboard') }}"
        class="inline-block bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded shadow ml-2">
        Kembali
        </a>
      </div>
    </div>
  </div>
@endsection
