{{-- resources/views/auth/reset-password.blade.php --}}
@extends('layouts.dashboard')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-cover bg-center bg-no-repeat"
     style="background-image: url('/Foto/Petani.png')">
  <div class="bg-white bg-opacity-60 backdrop-blur-lg p-8 rounded-lg shadow-xl w-full max-w-md">
    <h2 class="text-2xl font-bold text-center text-lime-700 mb-6">Reset Password</h2>

    @if (session('success'))
      <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
      </div>
    @endif

    @if ($errors->any())
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <ul class="list-disc list-inside">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('password.reset') }}" method="POST" class="space-y-5">
      @csrf

      <div>
        <label for="username" class="block text-gray-700 font-semibold mb-1">Username</label>
        <input
          type="text"
          name="username"
          id="username"
          value="{{ old('username') }}"
          required
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-600"
        />
      </div>

      <div>
        <label for="email" class="block text-gray-700 font-semibold mb-1">Email</label>
        <input
          type="email"
          name="email"
          id="email"
          value="{{ old('email') }}"
          required
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-600"
        />
      </div>

      <div>
        <label for="password_baru" class="block text-gray-700 font-semibold mb-1">Password Baru</label>
        <input
          type="password"
          name="password_baru"
          id="password_baru"
          required
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-600"
        />
      </div>

      <div>
        <label for="password_baru_confirmation" class="block text-gray-700 font-semibold mb-1">Konfirmasi Password</label>
        <input
          type="password"
          name="password_baru_confirmation"
          id="password_baru_confirmation"
          required
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-600"
        />
      </div>

      <button
        type="submit"
        class="w-full bg-lime-600 hover:bg-lime-700 text-white font-semibold py-2 px-4 rounded-lg transition"
      >
        Reset Password
      </button>
    </form>

    <div class="mt-6 text-center">
      <a
        href="{{ route('login') }}"
        class="block text-center bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-lg transition"
      >
        Kembali
      </a>
    </div>
  </div>
</div>
@endsection
