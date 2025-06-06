@extends('layouts.dashboard')

@section('content')
  <div class="min-h-screen flex items-center justify-center">
    <div class="bg-white bg-opacity-70 backdrop-blur-lg p-8 rounded-xl shadow-lg max-w-md text-center">
      <h1 class="text-4xl font-bold text-lime-700 mb-6">Selamat Datang, Petani!</h1>
      <p class="text-gray-700 mb-8">
        Silakan <strong>Login</strong> untuk melihat riwayat deteksi gambar Anda.
      </p>

      <div class="flex flex-col space-y-4">
        <a href="{{ route('login') }}"
           class="w-full inline-block bg-lime-600 hover:bg-lime-700 text-white font-semibold py-3 px-6 rounded-lg transition">
          Login
        </a>
        <a href="{{ route('welcome') }}"
           class="w-full inline-block bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-3 px-6 rounded-lg transition">
          Kembali ke Home
        </a>
      </div>
    </div>
  </div>
@endsection
