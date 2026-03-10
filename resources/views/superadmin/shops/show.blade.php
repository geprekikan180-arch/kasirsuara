@extends('layouts.admin')

@section('title', 'Detail Toko - ' . $shop->name)

@section('content')
    <style>
        html {
            scroll-behavior: smooth;
        }
    </style>
    <div class="mb-6">
        <a href="{{ route('shops.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium mb-4 inline-flex items-center">
            <i class="fa-solid fa-arrow-left mr-2"></i> Kembali ke Daftar Toko
        </a>
        <h2 class="text-2xl font-bold text-gray-800">Detail Toko</h2>
        <p class="text-gray-500 text-sm">Informasi lengkap mengenai toko dan performa penjualannya.</p>
    </div>

    <!-- ===== SECTION 1: PROFIL TOKO (ATAS) ===== -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <div class="p-8">
            <div class="flex flex-col md:flex-row gap-8">
                <!-- Foto Profil -->
                <div class="shrink-0">
                    @if($shop->logo)
                        <img src="{{ asset('storage/shops/'.$shop->logo) }}" alt="{{ $shop->name }}" 
                             class="w-32 h-32 rounded-2xl object-cover border-4 border-gray-100 shadow-md">
                    @else
                        <div class="w-32 h-32 rounded-2xl bg-linear-to-br from-blue-100 to-purple-100 flex items-center justify-center border-4 border-gray-100 shadow-md">
                            <i class="fa-solid fa-store text-4xl text-gray-400"></i>
                        </div>
                    @endif
                </div>

                <!-- Informasi Toko -->
                <div class="flex-1">
                    <div class="mb-6">
                        <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ $shop->name }}</h1>
                        <p class="text-gray-500">
                            <i class="fa-solid fa-map-pin mr-2 text-blue-600"></i>
                            {{ $shop->address ?? 'Alamat tidak tersedia' }}
                        </p>
                    </div>

                    <!-- Status & Tanggal -->
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                        <div>
                            <p class="text-xs text-gray-400 font-medium uppercase tracking-wide mb-2">Status</p>
                            @if($shop->status == 'active')
                                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-semibold">
                                    <i class="fa-solid fa-check-circle mr-1"></i> Aktif
                                </span>
                            @else
                                <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm font-semibold">
                                    <i class="fa-solid fa-times-circle mr-1"></i> Dibekukan
                                </span>
                            @endif
                        </div>

                        <div>
                            <p class="text-xs text-gray-400 font-medium uppercase tracking-wide mb-2">Tanggal Bergabung</p>
                            <p class="text-sm font-semibold text-gray-800">
                                {{ $shop->created_at->format('d M Y') }}
                            </p>
                            <p class="text-xs text-gray-400">
                                {{ $shop->created_at->diffForHumans() }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-400 font-medium uppercase tracking-wide mb-2">Total Karyawan</p>
                            <p class="text-sm font-semibold text-gray-800">
                                {{ $shop->users()->where('role', '!=', 'owner')->count() }} orang
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- ===== SECTION 2: GRAFIK PERTUMBUHAN (TENGAH) ===== -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 mb-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="text-lg font-bold text-gray-800">Pertumbuhan Toko</h3>
                <p class="text-xs text-gray-400 mt-1">6 bulan terakhir</p>
            </div>
            <span class="text-xs text-gray-400 bg-gray-50 px-3 py-1 rounded-full">6 Bulan</span>
        </div>

        <div class="relative h-80 w-full">
            <canvas id="shopGrowthChart"></canvas>
        </div>
    </div>

    

    <!-- ===== SECTION 3: DAFTAR KARYAWAN (BAWAH) ===== -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6" id="karyawan-section">
        <div class="p-8 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-800">Daftar Karyawan</h3>
            <p class="text-xs text-gray-400 mt-1">Nama, username, dan role karyawan toko</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-8 py-4 font-semibold">No</th>
                        <th class="px-8 py-4 font-semibold">Nama Karyawan</th>
                        <th class="px-8 py-4 font-semibold">Username</th>
                        <th class="px-8 py-4 font-semibold">Posisi</th>
                        <th class="px-8 py-4 font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($employees as $employee)
                    <tr class="group hover:bg-blue-50/50 transition-colors">
                        <td class="px-8 py-4 text-gray-500">{{ ($employees->currentPage() - 1) * 5 + $loop->iteration }}</td>
                        <td class="px-8 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">
                                    {{ substr($employee->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-gray-800 font-medium">{{ $employee->name }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-4 text-gray-600">{{ $employee->username }}</td>
                        <td class="px-8 py-4">
                            @php
                                $roleLabels = [
                                    'owner' => ['label' => 'Pemilik', 'bg' => 'bg-purple-100', 'text' => 'text-purple-700'],
                                    'cashier' => ['label' => 'Kasir', 'bg' => 'bg-blue-100', 'text' => 'text-blue-700'],
                                    'inventory' => ['label' => 'Gudang', 'bg' => 'bg-green-100', 'text' => 'text-green-700'],
                                ];
                                $roles = $roleLabels[$employee->role] ?? ['label' => ucfirst($employee->role), 'bg' => 'bg-gray-100', 'text' => 'text-gray-700'];
                            @endphp
                            <span class="px-3 py-1 {{ $roles['bg'] }} {{ $roles['text'] }} rounded-full text-xs font-semibold">
                                {{ $roles['label'] }}
                            </span>
                        </td>
                        <td class="px-8 py-4">
                            @if($employee->is_frozen)
                                    <span class="text-xs text-red-600 bg-red-100 px-2 py-1 rounded border border-red-200">
                                        <i class="fa-solid fa-ban"></i> Dibekukan
                                    </span>
                                @else
                                    <span class="text-xs text-green-600 bg-green-100 px-2 py-1 rounded border border-green-200">
                                        <i class="fa-solid fa-check"></i> Aktif
                                    </span>
                                @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-10 text-center text-gray-400">
                            <i class="fa-solid fa-users text-3xl mb-2"></i>
                            <p>Belum ada karyawan terdaftar.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($employees->hasPages())
        <div class="px-8 py-6 bg-gray-50 border-t border-gray-100">
            <div class="flex justify-center">
                {{ $employees->links() }}
            </div>
        </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Tambah fragment #karyawan-section pada pagination link agar smooth scroll
            const paginationLinks = document.querySelectorAll('.pagination a');
            paginationLinks.forEach(link => {
                if (!link.href.includes('#')) {
                    link.href += '#karyawan-section';
                }
            });

            // Jika ada hash #karyawan-section di URL, scroll ke section tersebut
            if (window.location.hash === '#karyawan-section') {
                const section = document.getElementById('karyawan-section');
                if (section) {
                    section.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }

            // ===== CHART PERTUMBUHAN TOKO =====
            const ctx = document.getElementById('shopGrowthChart').getContext('2d');
            
            // Data dari Controller
            const labels = @json($chartLabels);
            const transactionData = @json($transactionCounts);
            const revenueData = @json($revenueCounts);
            const productData = @json($productCounts);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Jumlah Transaksi',
                            data: transactionData,
                            borderColor: '#3B82F6', // Blue
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            borderWidth: 2.5,
                            pointBackgroundColor: '#3B82F6',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            fill: true,
                            tension: 0.4,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Omset (Rp)',
                            data: revenueData,
                            borderColor: '#10B981', // Green
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            borderWidth: 2.5,
                            pointBackgroundColor: '#10B981',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            fill: true,
                            tension: 0.4,
                            yAxisID: 'y1'
                        },
                        {
                            label: 'Produk Terjual',
                            data: productData,
                            borderColor: '#F59E0B', // Amber
                            backgroundColor: 'rgba(245, 158, 11, 0.1)',
                            borderWidth: 2.5,
                            pointBackgroundColor: '#F59E0B',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            fill: true,
                            tension: 0.4,
                            yAxisID: 'y'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                padding: 15,
                                font: { size: 12 },
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        },
                        tooltip: {
                            backgroundColor: '#1F2937',
                            padding: 12,
                            titleFont: { size: 13, weight: 'bold' },
                            bodyFont: { size: 12 },
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.dataset.label === 'Omset (Rp)') {
                                        const value = context.parsed.y;
                                        if (value >= 1000000) {
                                            label += 'Rp ' + (value / 1000000).toFixed(1) + ' Juta';
                                        } else if (value >= 1000) {
                                            label += 'Rp ' + (value / 1000).toFixed(1) + ' Ribu';
                                        } else {
                                            label += 'Rp ' + value.toLocaleString('id-ID');
                                        }
                                    } else {
                                        label += context.parsed.y;
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            beginAtZero: true,
                            grid: {
                                borderDash: [2, 4],
                                color: '#E5E7EB'
                            },
                            ticks: {
                                precision: 0
                            },
                            title: {
                                display: true,
                                text: 'Transaksi & Produk'
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            beginAtZero: true,
                            grid: {
                                drawOnChartArea: false,
                            },
                            ticks: {
                                callback: function(value) {
                                    if (value >= 1000000) {
                                        return 'Rp ' + (value / 1000000).toFixed(0) + 'Jt';
                                    } else if (value >= 1000) {
                                        return 'Rp ' + (value / 1000).toFixed(0) + 'K';
                                    } else {
                                        return 'Rp ' + value;
                                    }
                                }
                            },
                            title: {
                                display: true,
                                text: 'Omset (Rp)'
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
