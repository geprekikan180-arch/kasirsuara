<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Pencarian - Kasir Pintar</title>
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

            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Hasil Pencarian</h1>
                <p class="text-gray-600">Mencari: <span class="font-semibold text-blue-600">"{{ $searchQuery }}"</span></p>
            </div>

            @if(empty($searchQuery))
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 text-center">
                    <i class="fa-solid fa-search text-4xl text-yellow-600 mb-3"></i>
                    <p class="text-yellow-800 font-semibold">Gunakan kolom pencarian untuk mencari barang, karyawan, atau transaksi</p>
                </div>
            @else
                


                <!-- HASIL TRANSAKSI -->
                <div class="mb-12">
                    <div class="flex items-center gap-3 mb-4">
                        <i class="fa-solid fa-receipt text-purple-600 text-2xl"></i>
                        <h2 class="text-2xl font-bold text-gray-800">Transaksi ({{ count($results['transactions']) }})</h2>
                    </div>
                    
                    @if(count($results['transactions']) > 0)
                        <div class="space-y-3">
                            @foreach($results['transactions'] as $trx)
                                <div class="bg-white rounded-lg shadow p-4 border border-gray-100 hover:shadow-md transition">
                                    <div class="flex justify-between items-start">
                                        <div class="flex items-start gap-3">
                                            <button type="button" aria-expanded="false" aria-controls="details-{{ $trx->id }}" onclick="toggleDetails('details-{{ $trx->id }}', 'icon-{{ $trx->id }}')" class="text-blue-400 hover:text-blue-600 transition-transform duration-200" id="btn-{{ $trx->id }}">
                                                <i class="fa-solid fa-chevron-down transition-transform duration-200" id="icon-{{ $trx->id }}"></i>
                                            </button>
                                            <div>
                                                <h3 class="font-bold text-gray-800">#TRX-{{ $trx->id }}</h3>
                                                <p class="text-sm text-gray-600">
                                                    {{ $trx->created_at->format('d M Y • H:i') }} 
                                                    <span class="text-xs">(Kasir: {{ $trx->user->name ?? 'User Terhapus' }})</span>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-2xl font-bold text-blue-600">Rp {{ number_format($trx->total_amount, 0, ',', '.') }}</p>
                                            <p class="text-xs text-gray-500">{{ count($trx->details) }} item</p>
                                        </div>
                                    </div>

                                    <div id="details-{{ $trx->id }}" class="hidden mt-4  rounded p-3 text-sm">
                                        <h4 class="font-semibold text-gray-700 mb-2"><i class="fa-solid fa-list mr-2 text-gray-600"></i>Rincian Transaksi</h4>
                                        @if(count($trx->details) > 0)
                                            <div class="space-y-2">
                                                @foreach($trx->details as $detail)
                                                    <div class="flex justify-between items-center bg-white p-2 rounded border border-gray-200">
                                                        <div class="text-gray-700">
                                                            <div class="font-semibold">{{ $detail->product->name ?? 'Produk Terhapus' }}</div>
                                                            <div class="text-xs text-gray-500">Qty: {{ $detail->quantity }} • Harga: Rp {{ number_format($detail->price, 0, ',', '.') }}</div>
                                                        </div>
                                                        <div class="font-semibold text-gray-800">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</div>
                                                    </div>
                                                    @endforeach
                                                    <div class="bg-blue-50 pt-2 pb-2">
                                                    <div class=" grid grid-cols-1 md:grid-cols-2 ">
                                                        <p class=" pl-2 text-gray-600">Bayar:</p>
                                                        <p class="text-right pr-2.5  text-blue-600 font-bold ">Rp  {{ number_format($trx->cash_paid, 0, ',', '.') }}</p>
                                                        <p class=" pl-2 text-gray-600">total:</p>
                                                        <p class="text-right pr-2.5  text-blue-600 font-bold ">Rp  {{ number_format($trx->total_amount, 0, ',', '.') }}</p>
                                                    </div>
                                                    </div>
                                                    <div class="border-t grid grid-cols-1 md:grid-cols-2 border-gray-500 pt-2 mt-2">
                                                        <p class=" pl-2 text-gray-600">kembalian: </p>
                                                        <p class=" pr-2.5 text-right text-sm text-blue-600 font-bold ">Rp {{ number_format($trx->change, 0, ',', '.') }}</p>
                                                    </div>
                                            </div>
                                        @else
                                            <p class="text-gray-500">Tidak ada detail transaksi</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                            
                        </div>
                    @else
                        <div class="bg-gray-100 rounded-lg p-6 text-center text-gray-600">
                            <i class="fa-solid fa-inbox text-3xl mb-2 opacity-50"></i>
                            <p>Tidak ada transaksi yang ditemukan</p>
                        </div>
                    @endif
                </div>

            @endif

        </main>
    </div>
</div>

    <script>
        function toggleDetails(detailsId, iconId) {
            const detailsEl = document.getElementById(detailsId);
            const iconEl = document.getElementById(iconId);
            const btnEl = document.getElementById('btn-' + detailsId.split('-').pop());
            if (!detailsEl) return;
            detailsEl.classList.toggle('hidden');
            if (iconEl) iconEl.classList.toggle('rotate-180');
            if (btnEl) btnEl.setAttribute('aria-expanded', String(!detailsEl.classList.contains('hidden')));
        }
    </script>

</body>
</html>
