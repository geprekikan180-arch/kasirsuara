<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat - Kasir Suara Pintar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style> 
        body { font-family: 'Poppins', sans-serif; }
        /* Animasi halus untuk accordion detail */
        .details { transition: all 0.3s ease-in-out; }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex min-h-screen">
        @include('components.sidebar')
    <main class="flex-1 ml-0 md:ml-64 p-8">

        @include('components.search-panel')
        @php use Carbon\Carbon; @endphp
        
        

    <div class="mx-auto mb-16 ">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-blue-600 text-white px-6 py-4 flex justify-between items-center">
            <h1 class="text-xl font-bold">
                Riwayat Transaksi.
            </h1>
                    @if(request('start_date') || request('end_date'))
                        <div class="text-sm">
                            <span>{{ request('start_date') ? Carbon::parse(request('start_date'))->translatedFormat('d F Y') : 'awal' }}' - '
                                {{ request('end_date') ? Carbon::parse(request('end_date'))->translatedFormat('d F Y') : 'akhir' }}</span>
                        </div>
                    @endif
            </div>
            <form method="GET" action="{{ Auth::user()->role === 'owner' ? route('owner.transactions') : route('cashier.transactions') }}" class="p-4 border-b border-gray-100 bg-gray-50 grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="text-xs font-semibold text-gray-700 block mb-2">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="text-gray-700 w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-700 block mb-2">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="text-gray-700 w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                </div>
    
                @if(Auth::user()->role === 'owner' && isset($users) && count($users) > 0)
                <div>
                    <label class="text-xs font-semibold text-gray-700 block mb-2">Kasir</label>
                    <select name="user_id" class="text-gray-700 w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                        <option value="">Semua Kasir</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
    
                <div class="flex items-end gap-2 mb-1">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-semibold text-sm transition">
                         <i class="fa-solid fa-filter"></i>  Filter
                    </button>
                    <a href="{{ Auth::user()->role === 'owner' ? route('owner.transactions') : route('cashier.transactions') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 py-2 px-3 rounded-lg text-sm transition">
                        <i class="fa-solid fa-times"></i>
                    </a>
                </div>
            </form>
        
        
        @forelse($transactions as $trx)
        <div class="group border-2 border-gray-100 bg-white hover:border-black transition-all cursor-pointer p-4 relative"
             onclick="toggleDetails('details-{{ $trx->id }}', this)">
            
            <div class="absolute top-6 right-6 text-2xl transition-colors">
                <i class="fa-solid fa-arrow-down-long trx-toggle-icon text-gray-300 transition-colors duration-300"></i>
            </div>

            <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
                <div>
                    <h3 class="font-bold text-xl tracking-tight">#TRX-{{ $trx->id }}</h3>
                    <p class="text-sm text-gray-500 mt-1 font-mono">
                        {{ $trx->created_at->format('d M Y • H:i') }} <br>
                        <span class="text-xs uppercase tracking-widest text-gray-400">Kasir: {{ $trx->user->name ?? 'User Terhapus' }}</span>
                    </p>
                </div>
                
                <div class="text-left md:text-right pr-12">
                    <p class="text-xl font-extrabold">Rp {{ number_format($trx->total_amount, 0, ',', '.') }}</p>
                    <span class="inline-block bg-green-50 text-green-700 text-xs font-bold px-2 py-1 mt-1 uppercase tracking-widest">Selesai</span>
                </div>
            </div>

            <div id="details-{{ $trx->id }}" class="hidden mt-8 pt-8 border-t-2 border-dashed border-gray-200">
                <p class="text-xs font-bold uppercase tracking-widest text-blue-400 mb-4">Rincian Item</p>
                
                <table class="w-full text-sm table-fixed">
                    <colgroup>
                        <col style="width:60%">
                        <col style="width:20%">
                        <col style="width:20%">
                    </colgroup>
                    @foreach($trx->details as $detail)
                    <tr class="border-b border-gray-50 last:border-none">
                        <td class="py-2 align-middle font-bold text-gray-800">- {{ $detail->product->name }}</td>
                        <td class="py-2 text-center align-middle text-gray-500">{{ number_format($detail->product->price, 0, ',', '.') }} x{{ $detail->quantity }}</td>
                        <td class="py-2 text-right align-middle font-mono text-gray-600">Rp {{ number_format($detail->product->price * $detail->quantity, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </table>

                <div class="mt-6 flex justify-end items-center gap-6 text-sm font-mono text-gray-500 bg-blue-50 p-4">
                    <div>
                        <span class="block text-xs uppercase text-gray-400">Tunai</span>
                        <span class="font-bold text-blue-900">@if($trx->cash_paid !== null)Rp {{ number_format($trx->cash_paid, 0, ',', '.') }}@else-@endif</span>
                    </div>
                    <div class="h-8 w-px bg-gray-300"></div>
                    <div>
                        <span class="block text-xs uppercase text-gray-400">Kembali</span>
                        <span class="font-bold text-blue-900">@if($trx->change !== null)Rp {{ number_format($trx->change, 0, ',', '.') }}@else-@endif</span>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-20 border-2 border-dashed border-gray-200">
            <p class="text-xl font-bold text-gray-400">Belum ada riwayat transaksi hari ini.</p>
        </div>
        @endforelse

                    @if($transactions->count() > 0)
                    <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
                        <p class="text-sm text-gray-600">
                            Menampilkan <span class="font-semibold">{{ ($transactions->currentPage() - 1) * $transactions->perPage() + 1 }}</span> 
                            hingga <span class="font-semibold">{{ min($transactions->currentPage() * $transactions->perPage(), $transactions->total()) }}</span> 
                            dari <span class="font-semibold">{{ $transactions->total() }}</span> transaksi
                        </p>
                        <div class="flex gap-2">
                            @if ($transactions->onFirstPage())
                                <button class="px-4 py-2 text-gray-400 cursor-not-allowed" disabled>
                                    <i class="fa-solid fa-chevron-left"></i>
                                </button>
                            @else
                                <a href="{{ $transactions->previousPageUrl() }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                                    <i class="fa-solid fa-chevron-left"></i>
                                </a>
                            @endif
    
                            @foreach ($transactions->getUrlRange(1, $transactions->lastPage()) as $page => $url)
                                @if ($page == $transactions->currentPage())
                                    <button class="px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold">{{ $page }}</button>
                                @else
                                    <a href="{{ $url }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">{{ $page }}</a>
                                @endif
                            @endforeach
    
                            @if ($transactions->hasMorePages())
                                <a href="{{ $transactions->nextPageUrl() }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                                    <i class="fa-solid fa-chevron-right"></i>
                                </a>
                            @else
                                <button class="px-4 py-2 text-gray-400 cursor-not-allowed" disabled>
                                    <i class="fa-solid fa-chevron-right"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                    @endif
    </div>
    {{-- <div class="mt-12 ">
        {{ $transactions->links() }}
    </div> --}}
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