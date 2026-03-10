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
                <!-- HASIL BARANG -->
                <div class="mb-12">
                    <div class="flex items-center gap-3 mb-4">
                        <i class="fa-solid fa-box text-blue-600 text-2xl"></i>
                        <h2 class="text-2xl font-bold text-gray-800">Barang ({{ count($results['products']) }})</h2>
                    </div>
                    
                    @if(count($results['products']) > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($results['products'] as $product)
                                <div class="bg-white rounded-lg shadow p-4 border border-gray-100 hover:shadow-md transition">
                                    <div class="flex justify-between items-start mb-2">
                                        <h3 class="font-bold text-gray-800 flex-1">{{ $product->name }}</h3>
                                        <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded">{{ $product->code }}</span>
                                    </div>
                                    @php
                                        if(!function_exists('categoryColor')) {
                                            function categoryColor($name) {
                                                $colors = ['red','yellow','green','blue','indigo','purple','pink','teal','orange','gray'];
                                                $idx = abs(crc32($name)) % count($colors);
                                                return $colors[$idx];
                                            }
                                        }
                                    @endphp
                                    <p class="text-sm text-gray-600 mb-2">Kategori: 
                                        @if(isset($product->categories) && count($product->categories)>0)
                                            @foreach($product->categories as $cat)
                                                @php $c = categoryColor($cat->name); @endphp
                                                <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold bg-{{ $c }}-100 text-{{ $c }}-800">{{ $cat->name }}</span>
                                            @endforeach
                                        @elseif(!empty($product->category))
                                            {{ $product->category }}
                                        @else
                                            -
                                        @endif
                                    </p>
                                    <div class="flex justify-between items-center mb-3">
                                        <span class="font-semibold text-lg">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                        <span class="text-sm font-bold {{ $product->stock > 10 ? 'text-green-600' : ($product->stock > 0 ? 'text-yellow-600' : 'text-red-600') }}">
                                            Stok: {{ $product->stock }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="bg-gray-100 rounded-lg p-6 text-center text-gray-600">
                            <i class="fa-solid fa-box-open text-3xl mb-2 opacity-50"></i>
                            <p>Tidak ada barang yang ditemukan</p>
                        </div>
                    @endif
                </div>

                <!-- HASIL KARYAWAN -->
                <div class="mb-12">
                    <div class="flex items-center gap-3 mb-4">
                        <i class="fa-solid fa-users text-green-600 text-2xl"></i>
                        <h2 class="text-2xl font-bold text-gray-800">Karyawan ({{ count($results['users']) }})</h2>
                    </div>

                    @if(count($results['users']) > 0)
                        <div class="bg-white rounded-lg shadow overflow-hidden border border-gray-100">
                            <table class="w-full">
                                <thead class="bg-blue-500 border-b border-gray-200 text-white">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-sm font-semibold ">No.</th>
                                        <th class="px-6 py-3 text-left text-sm font-semibold ">Nama</th>
                                        <th class="px-6 py-3 text-left text-sm font-semibold">Role</th>
                                        <th class="px-6 py-3 text-left text-sm font-semibold ">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($results['users'] as $user)
                                        </tr>
                                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                                            <td class="px-6 py-3 font-semibold text-gray-800">{{ $loop->iteration }}.</td>
                                            <td class="px-6 py-3 font-semibold text-gray-800">{{ $user->name }}</td>
                                            <td class="px-6 py-3 text-sm">
                                                <span class="px-3 py-1 rounded-full text-xs font-semibold
                                                    @if($user->role === 'cashier') bg-purple-100 text-purple-700
                                                    @elseif($user->role === 'inventory') bg-orange-100 text-orange-700
                                                    @else bg-gray-100 text-gray-700
                                                    @endif">
                                                    {{ ucfirst($user->role) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-3">
                                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $user->is_frozen ? 'bg-red-100 text-red-600 font-semibold' : 'bg-green-100 text-green-600 font-semibold' }}">
                                                    {{ $user->is_frozen ? 'Nonaktif' : 'Aktif' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="bg-gray-100 rounded-lg p-6 text-center text-gray-600">
                            <i class="fa-solid fa-user-slash text-3xl mb-2 opacity-50"></i>
                            <p>Tidak ada karyawan yang ditemukan</p>
                        </div>
                    @endif
                </div>

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

                                    <div id="details-{{ $trx->id }}" class="hidden mt-4 bg-gray-50 rounded p-3 text-sm">
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
