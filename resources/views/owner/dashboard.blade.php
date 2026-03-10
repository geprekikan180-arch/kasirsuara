<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Toko - Kasir Pintar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Poppins', sans-serif; } </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-50">

    <div class="flex min-h-screen">
        @include('components.sidebar')

        <main class="flex-1 ml-0 md:ml-64 p-8 pt-6 pb-4">
            
            @include('components.search-panel')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8 ">
                
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-blue-600 text-white text-center py-3 font-bold">Ringkasan</div>
                    <div class="p-6 space-y-6">
                        {{-- <div>
                                <p class="text-justify text-xs text-gray-500 font-bold">Jumlah karyawan :</p>
                                <h3 class="text-2xl font-black text-gray-800">{{ $data['jumlah_karyawan'] }} <span class="text-sm text-gray-400 font-normal">karyawan</span></h3>
                        </div> --}}
                        <div>
                            <p class="text-xs text-gray-500 font-bold">Jumlah barang terjual :</p>
                            <h3 class="text-2xl font-black text-teal-500">{{ $data['jumlah_terjual'] }} <span class="text-sm text-gray-400 font-normal">item</span></h3>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-bold">Barang paling laris :</p>
                            <h3 class="text-2xl font-black text-teal-500 truncate">{{ $data['barang_terlaris'] }}</h3>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-bold">Masa toko bergabung :</p>
                            <h3 class="text-2xl font-black text-teal-500">{{ $data['lama_gabung'] }} <span class="text-sm text-gray-400 font-normal">Hari</span></h3>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-bold">Total Pendapatan :</p>
                            <h3 class="text-2xl font-black text-teal-500">Rp {{ $data['pendapatan'] }},00</h3>
                        </div>
                    </div>
                </div>

                <div class="bg-white h-90 rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
                    <a href="{{ route('employees.index') }}">
                        <div class="bg-blue-600 text-white text-center py-3 font-bold">Daftar Karyawan</div>
                    </a>
                    <div class="flex items-center">
                        <div>
                            <p class="pt-6 pl-4 text-gray-500 text-xs font-bold">Total karyawan :</p>
                            <h2 class="pl-4 text-3xl font-black text-gray-800">{{ $data['jumlah_karyawan'] }}<span class="text-sm font-medium text-gray-400 pl-2">Orang</span></h2>
                        </div>
                    </div>
                    <div class="p-4 flex-1 overflow-y-auto max-h-[400px]">
                        <ul class="space-y-3">
                            @forelse($data['karyawan'] as $kry)
                                <li class="flex justify-between items-center border-b border-gray-50 pb-2">
                                    <span class="font-bold text-gray-800">{{ $kry->name }}</span>
                                    <span >
                                    @switch($kry->role ?? 'cashier')
                                        @case('cashier')
                                            <span class="inline-block px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-semibold">Kasir</span>
                                            @break
                                        @default
                                            <span class="inline-block px-3 py-1 bg-orange-100 text-orange-700 rounded-full text-xs font-semibold">Gudang</span>
                                    @endswitch
                                    </span>
                                </li>
                            @empty
                                <li class="text-center text-gray-400 py-4">Belum ada karyawan.</li>
                            @endforelse
                        </ul>
                    </div>
                     
                        
                </div>

            <div class="space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden"> <div class="bg-blue-600 text-white text-center py-3 font-bold">Diagram Penjualan (7 Hari Terakhir)</div>
                    <div class="p-4 min-h-80 w-full">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
                <script>
                    const ctx = document.getElementById('salesChart').getContext('2d');
                    // Ambil data dari Controller Laravel
                    const labels = @json($data['chart_dates']);
                    const dataValues = @json($data['chart_totals']);
                
                    new Chart(ctx, {
                        type: 'bar', // Tipe grafik: 'bar' (batang), 'line' (garis), 'pie' (donat)
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Pendapatan (Rp)',
                                data: dataValues,
                                backgroundColor: 'rgba(20, 184, 166, 1)', // Warna biru Tailwind (blue-600)
                                borderColor: 'rgba(13, 148, 136, 1)',
                                borderWidth: 1,
                                borderRadius: 5, // Sudut batang agak membulat
                                barThickness: 30, // Ketebalan batang
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false // Sembunyikan legenda agar lebih bersih
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            // Format Rupiah di tooltip saat mouse hover
                                            let value = context.raw;
                                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                        }
                                    }
                                }
                            },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                display: true,
                                borderDash: [2, 2], // Garis putus-putus
                                color: '#f3f4f6'
                            },
                            ticks: {
                                // Format angka sumbu Y jadi K (ribuan) biar ringkas
                                callback: function(value) {
                                    return (value / 1000) + 'k';
                                },
                                font: {
                                    family: "'Poppins', sans-serif",
                                    size: 10
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false // Hilangkan garis vertikal biar bersih
                            },
                            ticks: {
                                font: {
                                    family: "'Poppins', sans-serif",
                                    size: 10
                                }
                            }
                        }
                        }
                    }
                });
            </script>
                    
            </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden relative">
                    {{-- <div class="absolute top-0 right-0 w-4 h-4 bg-red-500 rounded-full border-2 border-white animate-pulse"></div> --}}
                    <div class="overflow-x-auto">
                        <table class="w-full text-center">
                            <thead class="bg-blue-600 text-white">    
                                <tr>
                                    <th class="py-3 px-2 text-sm">No.</th>
                                    <th class="py-3 px-2 text-sm">Kode Barang</th>
                                    <th class="py-3 px-2 text-sm">Nama Barang</th>
                                    <th class="py-3 px-2 text-sm">Kondisi</th>
                                    <th class="py-3 px-2 text-sm">Sisa Stok</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm font-semibold text-gray-700">
                                
                        @foreach($data['stok_menipis'] as $index => $item)
                            @php
                                // Default Hijau (Diatas atau sama dengan 15)
                                $bgClass = 'bg-green-100 text-green-800'; 

                                // Jika kurang dari 15 (tapi lebih dari/sama dengan 5) -> Kuning
                                if ($item->stock < 15) {
                                    $bgClass = 'bg-yellow-100 text-yellow-800';
                                }
                            
                                // Jika kurang dari 5 -> Merah (Prioritas tertinggi)
                                if ($item->stock <= 5) {
                                    $bgClass = 'bg-red-100 text-red-800';
                                }
                                
                            @endphp

                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-3.5">{{ $index + 1 }}.</td>
                                <td class="py-3.5 font-mono text-gray-500">{{ $item->code }}</td>
                                <td class="py-3.5 font-bold">{{ $item->name }}</td>                                
                                <td class="px-6 py-4">
                                    @switch($product->current_condition ?? 'good')
                                        @case('good')
                                            <span class="inline-block px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Baik</span>
                                            @break
                                        @case('damaged')
                                            <span class="inline-block px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold">Rusak</span>
                                            @break
                                        @default
                                            <span class="inline-block px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-semibold">Basi</span>
                                    @endswitch
                                </td>
                                <td class="py-3.5">
                                    <div class="{{ $bgClass }} py-1 px-3 rounded-full inline-block text-xs font-bold shadow-sm">
                                        {{ $item->stock  }} {{ $item->unit }}
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-blue-600 text-white text-center py-3 font-bold">Kelola Kategori</div>
                    <div class="p-6">
                        <form action="{{ route('owner.categories.store') }}" method="POST" class="mb-4">
                            @csrf
                            <div class="flex gap-2">
                                <input type="text" name="name" placeholder="Nama kategori baru" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg" required>
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Tambah</button>
                            </div>
                        </form>
                        <div class="max-h-40 overflow-y-auto">
                            <div class="space-y-1">
                                @forelse($categories as $category)
                                    <div class="flex justify-between items-center bg-gray-50 p-3 rounded-lg">
                                        <span class="text-sm font-semibold text-gray-700">{{ $category->name }}</span>
                                        <form action="{{ route('owner.categories.destroy', $category->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Hapus kategori ini?')">Hapus</button>
                                        </form>
                                    </div>
                                @empty
                                    <p class="text-gray-500 text-center py-4">Belum ada kategori</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </main>
    </div>

</body>
</html>