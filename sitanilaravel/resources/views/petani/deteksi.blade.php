{{-- resources/views/petani/deteksi.blade.php --}}
@extends('layouts.dashboard')

@section('content')
  <div class="min-h-screen flex flex-col items-center justify-start px-4 py-8">
    {{-- Navbar (opsional, bisa di‐extend dari layout) --}}
    <nav class="fixed top-0 left-0 w-full bg-gray-100 shadow-sm flex justify-between items-center px-6 py-4 z-50">
      <div class="flex items-center gap-2">
        <img src="{{ asset('Foto/Logo.png') }}" alt="Logo" class="h-10">
        <span class="font-semibold text-gray-700">{{ Auth::user()->nama }}</span>
      </div>
      <div>
        <form action="{{ route('logout') }}" method="POST">
          @csrf
          <button type="submit"
                  class="bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded-lg transition">
            Logout
          </button>
        </form>
      </div>
    </nav>

    {{-- Kontainer utama --}}
    <div class="mt-20 w-full max-w-6xl bg-white/10 backdrop-blur-md rounded-lg shadow-xl p-8 flex flex-col md:flex-row gap-8">
      <div class="md:w-1/2 w-full text-center space-y-6">
        {{-- Hasil preview gambar --}}
        <div id="preview"></div>

        {{-- Form Upload --}}
        <form id="formUpload" action="{{ route('petani.deteksi.process') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
          @csrf

          <input
            type="file"
            name="gambar"
            accept="image/*"
            required
            class="hidden"
            id="uploadInput"
            onchange="handleFile(this)"
          />

          <div class="flex justify-center gap-4">
            <button
              type="button"
              onclick="document.getElementById('uploadInput').click()"
              class="bg-lime-700 text-white font-semibold px-6 py-3 rounded-lg shadow-md hover:bg-lime-800 transition"
            >
              Pilih Gambar
            </button>
            <button
              type="submit"
              name="upload_deteksi"
              id="extraBtn"
              class="bg-lime-700 text-white font-semibold px-6 py-3 rounded-lg shadow-md hover:bg-lime-800 hidden transition"
            >
              Upload & Deteksi
            </button>
          </div>

          <div class="mt-10">
            <a href="{{ route('dashboard.petani') }}"
               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded shadow inline-block">
              Kembali ke Riwayat Saya
            </a>
          </div>
        </form>
      </div>

      <div class="md:w-1/2 w-full space-y-6 text-left">
        {{-- Indikator Loading --}}
        <div id="loading" class="hidden text-green-100 text-sm flex items-center gap-2">
          <svg class="animate-spin h-5 w-5 text-green-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 00-8 8h4z"></path>
          </svg>
          <span>Memproses gambar...</span>
        </div>
        {{-- Hasil deteksi akan ditampilkan di sini --}}
        <div id="hasil" class="text-xl font-bold text-white whitespace-pre-line"></div>
      </div>
    </div>
  </div>

  <script>
    // Fungsi menampilkan preview ketika user memilih file
    function handleFile(input) {
      const file = input.files[0];
      const preview = document.getElementById("preview");
      const extraBtn = document.getElementById("extraBtn");

      if (file && file.type.startsWith("image/")) {
        const reader = new FileReader();
        reader.onload = function (e) {
          preview.innerHTML = `
            <img src="${e.target.result}" alt="Preview"
                 class="mx-auto max-w-xs rounded-xl shadow-lg border border-white/20" />
          `;
          extraBtn.classList.remove("hidden");
        };
        reader.readAsDataURL(file);
      } else {
        preview.innerHTML = "<p class='text-red-200 font-semibold'>File bukan gambar!</p>";
        extraBtn.classList.add("hidden");
      }
    }

    // AJAX form submission (tanpa reload halaman)
    const form = document.getElementById('formUpload');
    const hasilDiv = document.getElementById('hasil');
    const loadingDiv = document.getElementById('loading');

    form.addEventListener('submit', async function (e) {
      e.preventDefault();
      loadingDiv.classList.remove("hidden");
      hasilDiv.innerText = "";

      const formData = new FormData(form);
      formData.append("upload_deteksi", "1");

      try {
        const response = await fetch("{{ route('petani.deteksi.process') }}", {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}",
          },
          body: formData
        });

        if (!response.ok) throw new Error("Gagal menghubungi server!");

        const hasil = await response.text();
        loadingDiv.classList.add("hidden");
        hasilDiv.innerText = "Hasil Deteksi:\n" + hasil;
      } catch (error) {
        console.error("Upload gagal:", error);
        alert("Upload gagal. Coba lagi!");
        loadingDiv.classList.add("hidden");
      }
    });
  </script>
@endsection
