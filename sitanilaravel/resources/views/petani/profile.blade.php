@extends('layouts.dashboard')

@section('content')
  <div class="bg-green-50 min-h-screen py-10 px-6">
    <div class="max-w-2xl mx-auto bg-white p-6 rounded-xl shadow space-y-6">

      {{-- Flash message sukses --}}
      @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg" role="alert">
          {{ session('success') }}
        </div>
      @endif

      {{-- Validasi error --}}
      @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
          <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <h2 class="text-xl font-bold text-lime-700">Profil Anda</h2>

      <form action="{{ route('petani.profile.update') }}" method="POST" class="space-y-4">
        @csrf

        <div>
          <label class="block text-sm font-medium text-gray-700">Nama</label>
          <input type="text"
                 name="nama"
                 value="{{ old('nama', $userData['nama']) }}"
                 required
                 class="w-full border border-gray-300 px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-lime-600" />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Email</label>
          <input type="email"
                 name="email"
                 value="{{ old('email', $userData['email']) }}"
                 required
                 class="w-full border border-gray-300 px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-lime-600" />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Username</label>
          <input type="text"
                 value="{{ $userData['username'] }}"
                 class="w-full border px-3 py-2 rounded bg-gray-100 cursor-not-allowed"
                 readonly />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Peran</label>
          <input type="text"
                 value="{{ $userData['peran'] }}"
                 class="w-full border px-3 py-2 rounded bg-gray-100 cursor-not-allowed"
                 readonly />
        </div>

        <div class="flex gap-2">
          <button type="submit"
                  class="bg-lime-600 hover:bg-lime-700 text-white font-semibold px-4 py-2 rounded-lg transition">
            Simpan Perubahan
          </button>

          <a href="{{ route('dashboard.petani') }}"
             class="inline-block bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold px-4 py-2 rounded shadow">
            Kembali ke Dashboard
          </a>
        </div>
      </form>
    </div>
  </div>
@endsection
