<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Bulanan - Kasir Pintar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Poppins', sans-serif; } </style>
</head>
<body class="bg-gray-50">

    <div class="flex min-h-screen">
        @include('components.sidebar')

        <main class="flex-1 ml-0 md:ml-64 p-8">
            
            @include('components.search-panel')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 ">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-bold mb-1">Total Pendapatan (Periode ini)</p>
                        <h2 class="text-3xl font-black text-blue-600">Rp {{ number_format($totalPendapatanBulanIni, 0, ',', '.') }},00</h2>
                    </div>
                    <div class="bg-blue-100 p-4 rounded-full text-blue-600">
                        <i class="fa-solid fa-money-bill-wave text-2xl"></i>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-bold mb-1">Total Transaksi</p>
                        <h2 class="text-3xl font-black text-gray-800">{{ $totalTransaksiBulanIni }} <span class="text-base font-normal text-gray-400">transaksi</span></h2>
                    </div>
                    <div class="bg-orange-100 p-4 rounded-full text-orange-600">
                        <i class="fa-solid fa-receipt text-2xl"></i>
                    </div>
                </div>
            </div>
            <div class="mx-auto mb-16">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-blue-600 text-white px-6 py-4 flex justify-between items-center">
                        <h1 class="text-xl font-bold">
                            Rincian Harian Periode 
                        </h1>
                        <span>
                            <p>{{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} - {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}</p>
                        </span>
                    </div>

                    <!-- FILTER SECTION -->
                    <div class="p-4 border-b border-gray-100 bg-gray-50">
                        <form action="{{ route('owner.report') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="text-xs font-semibold text-gray-700 block mb-2">Dari Tanggal</label>
                                <input type="date" name="start_date" value="{{ request('start_date', $startDate ?? '') }}" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                            </div>

                            <div>
                                <label class="text-xs font-semibold text-gray-700 block mb-2">Sampai Tanggal</label>
                                <input type="date" name="end_date" value="{{ request('end_date', $endDate ?? '') }}" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                            </div>

                            <div class="flex items-end gap-2">
                                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-semibold text-sm transition">
                                    <i class="fa-solid fa-filter"></i> Filter
                                </button>
                                <button type="button" onclick="window.print()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 py-2 px-3 rounded-lg text-sm transition">
                                    <i class="fa-solid fa-print"></i>
                                </button>
                            </div>
                        </form>
                    </div>

                    @forelse($laporanHarian as $row)
                        <div class="group border-2 border-gray-100 bg-white hover:border-black transition-all cursor-pointer p-4 relative m-0"
                             onclick="toggleDetails('details-{{ str_replace('-', '_', $row->date) }}', this)">
                            
                            <div class="absolute top-6 right-6 text-xl transition-colors">
                                <i class="fa-solid fa-arrow-down-long trx-toggle-icon text-gray-300 transition-colors duration-300" id="icon-{{ str_replace('-', '_', $row->date) }}"></i>
                            </div>

                            <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
                                <div>
                                    <h3 class="font-bold text-xl tracking-tight">
                                        {{ \Carbon\Carbon::parse($row->date)->translatedFormat('l, d F Y') }}
                                    </h3>
                                    <p class="text-sm text-gray-500 mt-1 font-mono">
                                        <span class="text-xs uppercase tracking-widest text-gray-400">{{ $row->total_transaksi }} transaksi</span>
                                    </p>
                                </div>
                                
                                <div class="text-left md:text-right pr-12">
                                    <p class="text-xl font-extrabold text-teal-500">Rp {{ number_format($row->total_pendapatan, 0, ',', '.') }}</p>
                                </div>
                            </div>

                            <div id="details-{{ str_replace('-', '_', $row->date) }}" class="hidden mt-8 pt-8 border-t-2 border-dashed border-gray-200">
                                @if(isset($detailTransaksiPerHari[$row->date]) && count($detailTransaksiPerHari[$row->date]) > 0)
                                    @foreach($detailTransaksiPerHari[$row->date] as $trx)
                                        <div class="mb-6 pb-6 border-b border-gray-200 last:border-b-0">
                                            <div class="mb-3">
                                                <h3 class="font-bold text-base text-teal-500 tracking-tight">#TRX-{{ $trx->id }}</h3>
                                                <p class="text-sm text-gray-500 mt-1 font-mono">
                                                    <span class="text-xs uppercase tracking-widest text-gray-400">Kasir: {{ $trx->user->name ?? 'User Terhapus' }}</span>
                                                </p>
                                            </div>
                                            <p class="text-xs text-gray-400"><i class="fa-solid fa-list mr-2 text-gray-400"></i>Rincian Item :</p>
                                            <table class="w-full text-sm table-fixed mb-4">
                                                <colgroup>
                                                    <col style="width:60%">
                                                    <col style="width:20%">
                                                    <col style="width:20%">
                                                </colgroup>
                                                @foreach($trx->details as $detail)
                                                <tr class="border-b border-gray-50 last:border-none">
                                                    <td class="py-2 align-middle font-bold text-gray-800">- {{ $detail->product->name ?? 'Produk Terhapus' }}</td>
                                                    <td class="py-2 text-center align-middle text-gray-500">{{ number_format($detail->product->price, 0, ',', '.') }} x{{ $detail->quantity }}</td>
                                                    <td class="py-2 text-right align-middle font-mono text-gray-600">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                                </tr>
                                                @endforeach
                                            </table>

                                            <div class="flex justify-end items-center gap-6 text-sm font-mono text-gray-500 bg-blue-50 p-4 rounded-lg">
                                                <div>
                                                    <span class="block text-xs uppercase text-gray-400">Tunai</span>
                                                    <span class="font-bold text-blue-900">@if($trx->cash_paid !== null)Rp {{ number_format($trx->cash_paid, 0, ',', '.') }}@else-@endif</span>
                                                </div>
                                                <div>
                                                    <span class="block text-xs uppercase text-blue-50">.</span>
                                                    <span class="font-bold text-blue-900">-</span>
                                                </div>
                                                <div>
                                                    <span class="block text-xs uppercase text-gray-400">Total</span>
                                                    <span class="font-bold text-blue-900">@if($trx->total_amount !== null)Rp {{ number_format($trx->total_amount, 0, ',', '.') }}@else-@endif</span>
                                                </div>
                                                <div class="h-8 w-px bg-gray-300"></div>
                                                <div>
                                                    <span class="block text-xs uppercase text-gray-400">Kembali</span>
                                                    <span class="font-bold text-blue-900">@if($trx->change !== null)Rp {{ number_format($trx->change, 0, ',', '.') }}@else-@endif</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-gray-500 text-sm">Tidak ada data transaksi</p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-20 border-2 border-dashed border-gray-200">
                            <p class="text-xl font-bold text-gray-400">Tidak ada transaksi pada periode ini.</p>
                        </div>
                    @endforelse

                    @if($laporanHarian->count() > 0)
                        <div class="px-6 py-4 border-t border-gray-100 bg-blue-50">
                            <div class="flex justify-between items-center">
                                <p class="text-sm font-bold text-gray-800">TOTAL</p>
                                <div class="text-right">
                                    <p class="text-xs text-gray-600 mb-1">{{ $totalTransaksiBulanIni }} transaksi</p>
                                    <p class="text-2xl font-extrabold text-blue-600">Rp {{ number_format($totalPendapatanBulanIni, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

        </main>
    </div>

    <script>
        function toggleDetails(id, cardElement) {
            const detailSection = document.getElementById(id);
            // Toggle visibility
            detailSection.classList.toggle('hidden');

            // Icon di dalam card (jika ada)
            const icon = cardElement.querySelector('.trx-toggle-icon');

            // Kalau dibuka -> arah ke atas dan warna hitam
            if (!detailSection.classList.contains('hidden')) {
                cardElement.classList.add('border-black');
                cardElement.classList.remove('border-gray-100');

                if (icon) {
                    icon.classList.remove('fa-arrow-down-long', 'text-gray-300');
                    icon.classList.add('fa-arrow-up-long', 'text-black');
                }
            } else {
                // Kalau ditutup -> arah ke bawah dan warna abu
                cardElement.classList.remove('border-black');
                cardElement.classList.add('border-gray-100');

                if (icon) {
                    icon.classList.remove('fa-arrow-up-long', 'text-black');
                    icon.classList.add('fa-arrow-down-long', 'text-gray-300');
                }
            }
        }

        // Sync icon states on page load (jika ada yang sudah dibuka)
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.group').forEach(card => {
                const detailSection = card.querySelector('[id^="details-"]');
                const icon = card.querySelector('.trx-toggle-icon');
                if (!detailSection || !icon) return;

                if (!detailSection.classList.contains('hidden')) {
                    icon.classList.remove('fa-arrow-down-long', 'text-gray-300');
                    icon.classList.add('fa-arrow-up-long', 'text-black');
                    card.classList.add('border-black');
                    card.classList.remove('border-gray-100');
                } else {
                    icon.classList.remove('fa-arrow-up-long', 'text-black');
                    icon.classList.add('fa-arrow-down-long', 'text-gray-300');
                }
            });
        });
    </script>

</body>
</html>