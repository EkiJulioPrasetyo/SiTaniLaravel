{{-- resources/views/admin/trend.blade.php --}}
@extends('layouts.dashboard')

@section('content')
  <div class="max-w-6xl mx-auto py-10 px-6">
    <h1 class="text-3xl font-bold text-lime-700 mb-8">Tren Penyakit</h1>

    {{-- 1) TREND MINGGU INI (Tabel) --}}
    <div class="mb-12">
      <h2 class="text-2xl font-semibold text-gray-800 mb-4">Top Penyakit Minggu Ini</h2>
      <table class="w-full text-sm border border-gray-200 rounded-lg overflow-hidden mb-6">
        <thead class="bg-lime-700 text-white">
          <tr>
            <th class="px-4 py-2 text-left">Rank</th>
            <th class="px-4 py-2 text-left">Penyakit</th>
            <th class="px-4 py-2 text-left">Jumlah Deteksi</th>
          </tr>
        </thead>
        <tbody id="weekly-trends-body">
          {{-- Data awal render dari controller --}}
          @forelse ($weeklyTrends as $index => $item)
            <tr class="@if($index % 2 == 0) bg-gray-50 @endif hover:bg-lime-50">
              <td class="px-4 py-2">{{ $index + 1 }}</td>
              <td class="px-4 py-2">{{ $item->hasil_deteksi }}</td>
              <td class="px-4 py-2">{{ $item->total }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="3" class="px-4 py-4 text-center text-gray-500">
                Belum ada deteksi di minggu ini.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- 2) ANALISIS BULANAN (Tabel + Grafik) --}}
    <div class="mb-8">
      <h2 class="text-2xl font-semibold text-gray-800 mb-4">Analisis Bulanan (12 Bulan Terakhir)</h2>
      <div class="overflow-x-auto mb-6">
        <table class="w-full text-sm border border-gray-200 rounded-lg overflow-hidden">
          <thead class="bg-lime-700 text-white">
            <tr>
              <th class="px-4 py-2 text-left">Bulan</th>
              @foreach ($labelUnik as $label)
                <th class="px-4 py-2 text-left">{{ $label }}</th>
              @endforeach
            </tr>
          </thead>
          <tbody id="monthly-analysis-body">
            @if ($bulanUnik->isEmpty())
              <tr>
                <td colspan="{{ $labelUnik->count() + 1 }}" class="px-4 py-4 text-center text-gray-500">
                  Belum ada data deteksi.
                </td>
              </tr>
            @else
              @foreach ($bulanUnik as $bulan)
                <tr class="@if($loop->even) bg-gray-50 @endif hover:bg-lime-50">
                  <td class="px-4 py-2">
                    {{ \Carbon\Carbon::createFromFormat('Y-m', $bulan)->isoFormat('MMMM YYYY') }}
                  </td>
                  @foreach ($labelUnik as $label)
                    <td class="px-4 py-2 text-center">
                      {{ $matrix[$bulan][$label] }}
                    </td>
                  @endforeach
                </tr>
              @endforeach
            @endif
          </tbody>
        </table>
      </div>

      {{-- 2a) CANVAS untuk Chart.js --}}
      <div class="bg-white p-6 rounded-lg shadow">
        <canvas id="monthlyChart" height="200"></canvas>
      </div>
    </div>
  </div>
@endsection

@section('scripts')
  {{-- Sisipkan CDN Chart.js --}}
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  {{-- Script AJAX Polling + Pembaruan Chart --}}
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      // Sediakan array bulanUnik dan labelUnik awal dari server (PHP → JS)
      const bulanUnikInitial = @json($bulanUnik);
      const labelUnikInitial = @json($labelUnik);
      const matrixInitial   = @json($matrix);

      // 1) Buat dulu konfigurasi data untuk Chart.js
      //    - labels: nama-nama bulan (format "Juni 2024", dst.)
      //    - datasets: satu dataset per label (color diacak)
      const bulanLabelsFormatted = bulanUnikInitial.map(b => {
        // Format “YYYY-MM” → “Bulan YYYY” (contoh: “2024-06” → “Juni 2024”)
        const dateObj = new Date(b + '-01');
        return dateObj.toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });
      });

      // Fungsi bantu untuk generate warna random (digunakan tiap kali buat dataset baru)
      function getRandomColor() {
        // return string hex warna acak, misalnya "#A1B2C3"
        const letters = '0123456789ABCDEF';
        let color = '#';
        for (let i = 0; i < 6; i++) {
          color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
      }

      // Buat array dataset awal
      const datasetsInitial = labelUnikInitial.map(label => {
        // Ambil data per bulan untuk label ini: jika tidak ada, default 0
        const dataArr = bulanUnikInitial.map(b => {
          return (matrixInitial[b] && matrixInitial[b][label])
                   ? matrixInitial[b][label]
                   : 0;
        });
        return {
          label: label,
          data: dataArr,
          backgroundColor: getRandomColor(),
        };
      });

      // Konfigurasi Chart.js
      const ctx = document.getElementById('monthlyChart').getContext('2d');
      const monthlyChart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: bulanLabelsFormatted,
          datasets: datasetsInitial
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            title: {
              display: true,
              text: 'Jumlah Deteksi per Penyakit per Bulan'
            },
            tooltip: {
              mode: 'index',
              intersect: false,
            },
            legend: {
              position: 'bottom',
            }
          },
          scales: {
            x: {
              stacked: false, // jika ingin side-by-side bars
              ticks: { font: { size: 12 } }
            },
            y: {
              beginAtZero: true,
              ticks: { stepSize: 1, font: { size: 12 } },
              title: {
                display: true,
                text: 'Jumlah Deteksi'
              }
            }
          }
        }
      });

      // ----------------------------------------------------------------
      // Fungsi‐fungsi AJAX Polling untuk tabel & Chart (tepat sama seperti sebelumnya,
      // hanya saja setelah data diterima, kita juga update 'monthlyChart.data' → 'monthlyChart.update()')
      // ----------------------------------------------------------------
      async function fetchWeeklyTrends() {
        try {
          const res = await fetch('{{ route('admin.trend.data') }}');
          const data = await res.json();
          const tbody = document.querySelector('#weekly-trends-body');
          tbody.innerHTML = '';

          if (!data.weeklyTrends || data.weeklyTrends.length === 0) {
            const tr = document.createElement('tr');
            tr.innerHTML = `
              <td colspan="3" class="px-4 py-4 text-center text-gray-500">
                Belum ada deteksi di minggu ini.
              </td>
            `;
            tbody.appendChild(tr);
            return;
          }

          data.weeklyTrends.forEach((item, idx) => {
            const tr = document.createElement('tr');
            tr.className = idx % 2 === 0 ? 'bg-gray-50 hover:bg-lime-50' : 'hover:bg-lime-50';
            tr.innerHTML = `
              <td class="px-4 py-2">${idx + 1}</td>
              <td class="px-4 py-2">${item.hasil_deteksi}</td>
              <td class="px-4 py-2">${item.total}</td>
            `;
            tbody.appendChild(tr);
          });
        } catch (error) {
          console.error('Gagal mengambil weekly trends:', error);
        }
      }

      async function fetchMonthlyAnalysis() {
        try {
          const res = await fetch('{{ route('admin.trend.data') }}');
          const data = await res.json();
          const tbody = document.querySelector('#monthly-analysis-body');
          tbody.innerHTML = '';

          const bulanUnik    = data.bulanUnik;
          const labelUnik    = data.labelUnik;
          const matrix       = data.matrix;

          // Render ulang tabel HTML bulanan
          if (!bulanUnik || bulanUnik.length === 0) {
            const tr = document.createElement('tr');
            tr.innerHTML = `
              <td colspan="${labelUnik.length + 1}" class="px-4 py-4 text-center text-gray-500">
                Belum ada data deteksi.
              </td>
            `;
            tbody.appendChild(tr);
          } else {
            bulanUnik.forEach((bulan, index) => {
              const tr = document.createElement('tr');
              tr.className = index % 2 === 0 ? 'bg-gray-50 hover:bg-lime-50' : 'hover:bg-lime-50';

              const dateObj = new Date(bulan + '-01');
              const bulanFormatted = dateObj.toLocaleDateString('id-ID', {
                year: 'numeric',
                month: 'long'
              });

              let rowHtml = `<td class="px-4 py-2">${bulanFormatted}</td>`;
              labelUnik.forEach(label => {
                const count = (matrix[bulan] && matrix[bulan][label]) ? matrix[bulan][label] : 0;
                rowHtml += `<td class="px-4 py-2 text-center">${count}</td>`;
              });
              tr.innerHTML = rowHtml;
              tbody.appendChild(tr);
            });
          }

          // ----------------------------------------------------------------
          // Update data Chart.js
          // ----------------------------------------------------------------

          // 1) Hitung labels (nama bulan) baru
          const bulanLabels = bulanUnik.map(b => {
            const dateObj = new Date(b + '-01');
            return dateObj.toLocaleDateString('id-ID', { year: 'numeric', month: 'long' });
          });

          // 2) Hitung data untuk setiap dataset (label) baru
          //    Kita perlu membangun array of arrays: satu array per label
          //    Format: dataPerLabel[labelIndex] = [jumlahPadaBulan1, jumlahPadaBulan2, ...]
          const dataPerLabel = labelUnik.map(label => {
            return bulanUnik.map(b => {
              return (matrix[b] && matrix[b][label]) ? matrix[b][label] : 0;
            });
          });

          // 3) Jika jumlah label berubah (label baru muncul atau lama hilang), kita reset seluruh datasets
          if (labelUnik.length !== monthlyChart.data.datasets.length
              || bulanUnik.length !== monthlyChart.data.labels.length) {
            // Build dataset baru lengkap
            const newDatasets = labelUnik.map((label, idx) => {
              return {
                label: label,
                data: dataPerLabel[idx],
                backgroundColor: getRandomColor()
              };
            });

            monthlyChart.data.labels   = bulanLabels;
            monthlyChart.data.datasets = newDatasets;
          } else {
            // Hanya update data (label bulan dan nilai setiap dataset)
            monthlyChart.data.labels = bulanLabels;
            monthlyChart.data.datasets.forEach((ds, idx) => {
              ds.data = dataPerLabel[idx];
            });
          }

          monthlyChart.update(); // Render ulang chart dengan data baru

        } catch (error) {
          console.error('Gagal mengambil monthly analysis:', error);
        }
      }

      // Panggil keduanya secara paralel
      async function fetchTrendData() {
        await Promise.all([
          fetchWeeklyTrends(),
          fetchMonthlyAnalysis()
        ]);
      }

      // Render awal
      fetchTrendData();

      // Polling tiap 60 detik
      setInterval(fetchTrendData, 60000);
    });
  </script>
@endsection
