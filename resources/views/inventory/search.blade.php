<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Pencarian Gudang - Kasir Pintar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Poppins', sans-serif; } </style>
</head>
<body class="bg-gray-100 min-h-screen">

    <div class="flex min-h-screen">
        @include('components.sidebar')

        <main class="flex-1 ml-0 md:ml-64 p-8">
            
    <nav class="bg-orange-600 text-white px-8 py-4 flex justify-between items-center shadow-lg mb-8 rounded-lg">
        <div class="flex items-center gap-3">
            <div class="bg-white/20 p-2 rounded-lg"><i class="fa-solid fa-search text-xl"></i></div>
            <div>
                <h1 class="font-bold text-lg">Hasil Pencarian Gudang</h1>
                <p class="text-xs opacity-80">Mencari: "{{ $searchQuery }}"</p>
            </div>
        </div>
    </nav>

            @if(empty($searchQuery))
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 text-center">
                    <i class="fa-solid fa-search text-4xl text-yellow-600 mb-3"></i>
                    <p class="text-yellow-800 font-semibold">Gunakan kolom pencarian untuk mencari barang di gudang</p>
                </div>
            @else
                <!-- HASIL BARANG -->
                @if(count($results['products']) > 0)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="bg-orange-600 text-white px-6 py-4">
                            <h2 class="text-xl font-bold flex items-center gap-2">
                                <i class="fa-solid fa-box"></i>
                                Hasil Pencarian Barang ({{ count($results['products']) }})
                            </h2>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="px-6 py-4 font-semibold text-gray-700">Kode</th>
                                        <th class="px-6 py-4 font-semibold text-gray-700">Nama Barang</th>
                                        <th class="px-6 py-4 font-semibold text-gray-700">Kategori</th>
                                        <th class="px-6 py-4 font-semibold text-gray-700">Harga</th>
                                        <th class="px-6 py-4 font-semibold text-gray-700">Stok</th>
                                        <th class="px-6 py-4 font-semibold text-gray-700">Status Kondisi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($results['products'] as $product)
                                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 font-semibold text-orange-600">{{ $product->code ?? '-' }}</td>
                                        <td class="px-6 py-4 font-semibold text-gray-800">{{ $product->name }}</td>
                                        @php
                                            if(!function_exists('categoryColor')) {
                                                function categoryColor($name) {
                                                    $colors = ['red','yellow','green','blue','indigo','purple','pink','teal','orange','gray'];
                                                    $idx = abs(crc32($name)) % count($colors);
                                                    return $colors[$idx];
                                                }
                                            }
                                        @endphp
                                        <td class="px-6 py-4 text-gray-600">
                                            @if(isset($product->categories) && count($product->categories) > 0)
                                                @foreach($product->categories as $cat)
                                                    @php $c = categoryColor($cat->name); @endphp
                                                    <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold bg-{{ $c }}-100 text-{{ $c }}-800">{{ $cat->name }}</span>
                                                @endforeach
                                            @elseif(!empty($product->category))
                                                {{ $product->category }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 font-semibold">Rp {{ number_format($product->price ?? 0, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4">
                                            <div class="text-center">
                                                <span class="font-bold {{ $product->stock > 10 ? 'text-green-600' : ($product->stock > 0 ? 'text-yellow-600' : 'text-red-600') }}">
                                                    {{ $product->stock ?? 0 }}
                                                </span>
                                                <div class="text-xs text-gray-500">pcs</div>
                                            </div>
                                        </td>
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
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <div class="bg-gray-100 rounded-lg p-12 text-center">
                        <i class="fa-solid fa-inbox text-5xl text-gray-400 mb-4"></i>
                        <p class="text-gray-600 text-lg font-semibold">Tidak ada barang yang ditemukan</p>
                        <p class="text-gray-500 text-sm mt-2">Coba gunakan kata kunci lain untuk mencari barang</p>
                    </div>
                @endif
            @endif

        </main>
    </div>

</body>
</html>
