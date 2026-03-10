@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="flex justify-between items-end mb-6">
        <div>
            <h2 class="text-3xl font-bold text-gray-800 tracking-tight">Selamat Datang, Admin! 👋</h2>
            <p class="text-gray-500 mt-2">Ringkasan aktivitas platform hari ini.</p>
        </div>
        <div class="hidden md:block">
            <span class="text-sm font-medium text-gray-400">{{ now()->translatedFormat('d F Y') }}</span>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-10">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-800">Pertumbuhan Mitra Toko</h3>
            <span class="text-xs text-gray-400 bg-gray-50 px-2 py-1 rounded">6 Bulan Terakhir</span>
        </div>
        <div class="relative h-64 w-full">
            <canvas id="growthChart"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-400 font-medium mb-1">Total Mitra Toko</p>
                    <h3 class="text-4xl font-bold text-gray-800">{{ number_format($totalShops, 0, ',', '.') }}</h3>
                </div>
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-xl">
                    <i class="fa-solid fa-store"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs {{ $newShopsCount > 0 ? 'text-green-500' : 'text-gray-400' }} font-medium">
                @if($newShopsCount > 0)
                    <i class="fa-solid fa-arrow-up mr-1"></i> +{{ $newShopsCount }} Toko baru (30 hari)
                @else
                    <i class="fa-solid fa-minus mr-1"></i> Tidak ada toko baru (30 hari)
                @endif
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-400 font-medium mb-1">Total Pengguna</p>
                    <h3 class="text-4xl font-bold text-gray-800">{{ number_format($totalUsers, 0, ',', '.') }}</h3>
                </div>
                <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center text-xl">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs text-gray-400">
                Termasuk Owner & Karyawan
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-400 font-medium mb-1">Pesan Masuk</p>
                    <h3 class="text-4xl font-bold text-gray-800">{{ $unreadMessages }}</h3>
                </div>
                <div class="w-12 h-12 bg-yellow-50 text-yellow-600 rounded-xl flex items-center justify-center text-xl">
                    <i class="fa-solid fa-envelope"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs {{ $unreadMessages > 0 ? 'text-red-500 font-bold' : 'text-green-500' }}">
                @if($unreadMessages > 0)
                    Perlu dibalas segera
                @else
                    Semua pesan terbaca
                @endif
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-bold text-gray-800">Pendaftaran Toko Terbaru</h3>
            <a href="{{ route('shops.index') }}" class="text-sm text-blue-600 font-medium hover:underline">Lihat Semua</a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-gray-400 text-sm border-b border-gray-100">
                        <th class="py-4 font-medium">No</th>
                        <th class="py-4 font-medium">Nama Toko</th>
                        <th class="py-4 font-medium">Tanggal Gabung</th>
                        <th class="py-4 font-medium">Status</th>
                        <th class="py-4 font-medium text-right">Akses</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @forelse($latestShops as $shop)
                    <tr class="group hover:bg-gray-50 transition-colors">
                        <td class="py-4">{{ $loop->iteration }}</td>
                        <td class="py-4 font-semibold text-gray-800">
                            {{ $shop->name ?? 'Tanpa Nama' }}
                            <div class="text-xs text-gray-400 font-normal">{{ $shop->address ?? 'Alamat tidak tersedia' }}</div>
                        </td>
                        <td class="py-4 text-gray-500">
                            {{ \Carbon\Carbon::parse($shop->created_at)->format('d M Y') }}
                        </td>
                        <td class="py-4">
                            @if($shop->status === 'active')
                                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Aktif</span>
                            @else
                                <span class="px-3 py-1 bg-red-100 text-red-600 rounded-full text-xs font-semibold">Dibekukan</span>
                            @endif
                        </td>
                        <td class="py-4 text-right">
                            <a href="{{ route('shops.show', $shop->id) }}" class="text-gray-400 hover:text-blue-600 px-2 transition-colors" title="Lihat Detail">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-8 text-center text-gray-400">Belum ada data toko.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('growthChart').getContext('2d');
            
            // Data dari Controller
            const labels = @json($chartLabels);
            const dataValues = @json($chartData);

            // Membuat Gradient warna agar mirip gambar referensi
            let gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(59, 130, 246, 0.5)'); // Warna Biru Pudar atas
            gradient.addColorStop(1, 'rgba(59, 130, 246, 0.0)'); // Transparan bawah

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Toko Baru Terdaftar',
                        data: dataValues,
                        borderColor: '#3B82F6', // Blue-500
                        backgroundColor: gradient,
                        borderWidth: 2,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#3B82F6',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.4 // Membuat garis melengkung (smooth)
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false // Sembunyikan legenda jika judul sudah jelas
                        },
                        tooltip: {
                            backgroundColor: '#1F2937',
                            padding: 12,
                            titleFont: { size: 13 },
                            bodyFont: { size: 14 }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                borderDash: [2, 4],
                                color: '#E5E7EB'
                            },
                            ticks: {
                                precision: 0 // Agar tidak ada angka desimal (0.5 toko)
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection