@extends('layouts.dashboard')

@section('content')
  <div class="bg-white bg-opacity-60 backdrop-blur-lg p-8 rounded-lg shadow-xl w-full max-w-md mx-auto mt-24">
    <h2 class="text-2xl font-bold text-center text-lime-700 mb-6">Login SiTani</h2>

    @if ($errors->any())
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <strong class="font-bold">Login Gagal!</strong>
        <ul class="mt-1 list-disc list-inside text-sm">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('login.process') }}" method="POST" class="space-y-5">
      @csrf

      {{-- Pilih Peran --}}
      <div>
        <label class="block text-gray-700 font-semibold mb-1">Login Sebagai</label>
        <div class="flex items-center space-x-4">
          <label class="inline-flex items-center cursor-pointer">
            <input type="radio"
                   class="sr-only peer"
                   name="peran"
                   value="admin"
                   {{ old('peran')=='admin' ? 'checked' : '' }}
                   required>
            <div class="w-20 bg-gray-200 peer-checked:bg-lime-600 text-center py-2 rounded-lg text-sm font-semibold text-gray-700 peer-checked:text-white">
              Admin
            </div>
          </label>
          <label class="inline-flex items-center cursor-pointer">
            <input type="radio"
                   class="sr-only peer"
                   name="peran"
                   value="petani"
                   {{ old('peran')=='petani' ? 'checked' : '' }}>
            <div class="w-20 bg-gray-200 peer-checked:bg-lime-600 text-center py-2 rounded-lg text-sm font-semibold text-gray-700 peer-checked:text-white">
              Petani
            </div>
          </label>
        </div>
      </div>

      {{-- Username --}}
      <div>
        <label for="username" class="block text-gray-700 font-semibold mb-1">Username</label>
        <input type="text"
               name="username"
               id="username"
               value="{{ old('username') }}"
               required
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-600">
      </div>

      {{-- Password --}}
      <div>
        <label for="password" class="block text-gray-700 font-semibold mb-1">Password</label>
        <input type="password"
               name="password"
               id="password"
               required
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-600">
      </div>

      <button type="submit"
              class="w-full bg-lime-600 hover:bg-lime-700 text-white font-semibold py-2 px-4 rounded-lg transition">
        Login
      </button>

      <div class="mt-6 text-center">
        <a href="{{ route('welcome') }}"
           class="w-full block text-center bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-lg transition">
          Kembali
        </a>
      </div>
    </form>

    <div class="mt-4 text-sm text-center">
      <p class="text-gray-500">Belum punya akun?
        <a href="register" class="text-lime-600 hover:underline font-medium">Daftar di sini</a>
      </p>
      <p class="text-gray-500 mt-2">
        <a href="{{ route('password.request') }}" class="text-blue-600 hover:underline font-medium">
          Lupa Password?
        </a>
      </p>
    </div>
  </div>
@endsection
