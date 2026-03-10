<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Barang - Kasir Pintar</title>
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

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                
                <div class="bg-blue-600 text-white px-6 py-4 flex justify-between items-center">
                    <h2 class="text-xl font-bold">Daftar Barang</h2>
                    
                </div>

                <!-- FILTER SECTION -->
                <div class="p-4 border-b border-gray-100 bg-gray-50">
                    <form method="GET" action="{{ route('owner.products') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                        <div>
                            <label class="text-xs font-semibold text-gray-700 block mb-1">Kategori</label>
                            <select name="category" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="text-xs font-semibold text-gray-700 block mb-1">Status Stok</label>
                            <select name="stock_status" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                                <option value="">Semua Status</option>
                                <option value="available" {{ request('stock_status') == 'available' ? 'selected' : '' }}>Tersedia (>10)</option>
                                <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Hampir Habis (1-10)</option>
                                <option value="out" {{ request('stock_status') == 'out' ? 'selected' : '' }}>Habis (0)</option>
                            </select>
                        </div>

                        <div>
                            <label class="text-xs font-semibold text-gray-700 block mb-1">Kondisi Barang</label>
                            <select name="condition" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                                <option value="">Semua Kondisi</option>
                                <option value="good" {{ request('condition') == 'good' ? 'selected' : '' }}>Baik</option>
                                <option value="damaged" {{ request('condition') == 'damaged' ? 'selected' : '' }}>Rusak</option>
                                <option value="expired" {{ request('condition') == 'expired' ? 'selected' : '' }}>Basi</option>
                            </select>
                        </div>

                        <div class="flex items-end gap-2">
                            <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-semibold text-sm transition">
                                <i class="fa-solid fa-filter mr-1"></i> Filter
                            </button>
                            <a href="{{ route('owner.products') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 py-2 px-3 rounded-lg text-sm transition" title="Reset Filter">
                                <i class="fa-solid fa-times"></i>
                            </a>
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 font-semibold text-gray-700">No.</th>
                                <th class="px-6 py-4 font-semibold text-gray-700">Kode Barang</th>
                                <th class="px-6 py-4 font-semibold text-gray-700">Nama Barang</th>
                                <th class="px-6 py-4 font-semibold text-gray-700">Kategori</th>
                                <th class="px-6 py-4 font-semibold text-gray-700">Harga Jual</th>
                                <th class="px-6 py-4 font-semibold text-gray-700 text-center">Stok</th>
                                <th class="px-6 py-4 font-semibold text-gray-700">Status</th>
                                <th class="px-6 py-4 font-semibold text-gray-700">Masalah Minggu Ini</th>
                                <th class="px-6 py-4 font-semibold text-gray-700">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $index => $product)
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">{{ ($products->currentPage() - 1) * $products->perPage() + $loop->iteration }}.</td>
                                <td class="px-6 py-4 font-semibold text-gray-800">{{ $product->code ?? '-' }}</td>
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-800">{{ $product->name }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        // fungsi sederhana untuk memilih warna berdasarkan nama kategori
                                        if(!function_exists('categoryColor')) {
                                            function categoryColor($name) {
                                                $colors = ['red','yellow','green','blue','indigo','purple','pink','teal','orange','gray'];
                                                $idx = abs(crc32($name)) % count($colors);
                                                return $colors[$idx];
                                            }
                                        }
                                    @endphp
                                    @if($product->categories->isNotEmpty())
                                        @foreach($product->categories as $cat)
                                            @php $c = categoryColor($cat->name); @endphp
                                            <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold bg-{{ $c }}-100 text-{{ $c }}-800">{{ $cat->name }}</span>
                                        @endforeach
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-semibold text-gray-800">Rp {{ number_format($product->price ?? 0, 0, ',', '.') }},00</td>
                                <td class="px-6 py-4">
                                    <div class="text-center text-gray-500">
                                        <span class="font-bold text-gray-800">
                                            @if(($product->stock ?? 0) > 20)
                                        <span class="inline-block px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">
                                            {{ $product->stock }}
                                        </span>
                                    @elseif(($product->stock ?? 0) > 0)
                                        <span class="inline-block px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-semibold">
                                            {{ $product->stock }}
                                        </span>
                                    @else
                                        <span class="inline-block px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold">
                                            {{ $product->stock }}!
                                        </span>
                                    @endif
                                        </span>
                                        {{ $product->unit }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if(($product->stock ?? 0) > 10)
                                        <span class="inline-block px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">
                                            Tersedia
                                        </span>
                                    @elseif(($product->stock ?? 0) > 0)
                                        <span class="inline-block px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-semibold">
                                            Menipis
                                        </span>
                                    @else
                                        <span class="inline-block px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold">
                                            Habis
                                        </span>
                                    @endif
                                </td>
                                {{-- <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-800">{{ $product->current_condition }}</div>
                                </td> --}}
                                <td class="px-6 py-4">
                                    @php
                                        $d = $product->weekly_damaged ?? 0;
                                        $e = $product->weekly_expired ?? 0;
                                    @endphp
                                    @if($d > 0 && $e > 0)
                                        <span class="text-red-600 text-sm font-semibold">{{ $d }} rusak, {{ $e }} basi</span>
                                    @elseif($d > 0)
                                        <span class="text-red-600 text-sm font-semibold">{{ $d }} rusak</span>
                                    @elseif($e > 0)
                                        <span class="text-red-600 text-sm font-semibold">{{ $e }} basi</span>
                                    @else
                                        <span class="text-green-600 text-sm font-semibold">0 bermasalah</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex gap-2">
                                        <button type="button" class="js-toggle-edit text-blue-600 hover:text-blue-800 hover:bg-blue-50 p-2 rounded-lg transition-colors" data-target="edit-row-{{ $product->id }}" title="Edit">
                                            <i class="fa-solid fa-edit"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr id="edit-row-{{ $product->id }}" class="bg-blue-50 hidden">
                                <td colspan="9" class="px-6 py-4">
                                    <form method="POST" action="{{ route('owner.products.update', $product->id) }}" class="grid grid-cols-1 md:grid-cols-8 gap-3 items-end">
                                        @csrf
                                        @method('PUT')

                                        <div>
                                            <input name="code" value="{{ $product->code ?? '' }}" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm" />
                                        </div>

                                        <div >
                                            <input name="name" value="{{ $product->name }}" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm" />
                                        </div>

                                        <div>
                                            <div class="relative">
                                                <button type="button" class="category-dropdown-btn w-full px-3 py-2 rounded-lg border border-gray-300 text-sm text-left bg-white flex justify-between items-center" data-target="category-dropdown-{{ $product->id }}">
                                                    <span id="selected-categories-{{ $product->id }}">Pilih Kategori</span>
                                                    <i class="fa-solid fa-chevron-down"></i>
                                                </button>
                                                <div id="category-dropdown-{{ $product->id }}" class="category-dropdown absolute top-full left-0 w-full bg-white border border-gray-300 rounded-lg shadow-lg z-10 hidden max-h-48 overflow-y-auto">
                                                    @foreach($categories as $cat)
                                                        <label class="flex items-center px-3 py-2 hover:bg-gray-100 cursor-pointer">
                                                            <input type="checkbox" name="categories[]" value="{{ $cat }}" {{ $product->categories->pluck('name')->contains($cat) ? 'checked' : '' }} class="mr-2">
                                                            {{ $cat }}
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <input name="price" value="{{ $product->price ?? 0 }}" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm" />
                                        </div>

                                        <div>
                                            <input name="stock" value="{{ $product->stock ?? 0 }}" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm" />
                                        </div>

                                        <div>
                                            <select name="unit" class="w-full px-2 py-2 rounded-lg border border-gray-300 text-sm">
                                                @foreach($units as $u)
                                                    <option value="{{ $u }}" {{ ($product->unit ?? 'pcs') == $u ? 'selected' : '' }}>{{ strtoupper($u) }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div>
                                            <select name="current_condition" class="px-2 py-2 rounded-lg border border-gray-300 text-sm">
                                                <option value="good" {{ ($product->current_condition ?? 'good') == 'good' ? 'selected' : '' }}>Baik</option>
                                                <option value="damaged" {{ ($product->current_condition ?? '') == 'damaged' ? 'selected' : '' }}>Rusak</option>
                                                <option value="expired" {{ ($product->current_condition ?? '') == 'expired' ? 'selected' : '' }}>Basi</option>
                                            </select>
                                        </div>

                                        <div class="flex gap-2 md:justify-end">
                                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg text-sm font-semibold">Simpan</button>
                                            <button type="button" data-target="edit-row-{{ $product->id }}" class="js-cancel-edit bg-gray-300 hover:bg-gray-400 text-gray-800 py-2 px-4 rounded-lg text-sm">Batal</button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                            @continue
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <i class="fa-solid fa-box text-4xl mb-3 text-gray-300"></i>
                                        <p class="text-lg font-semibold">Belum ada barang</p>
                                        <p class="text-sm">Coba ubah filter atau mulai tambahkan barang di gudang Anda</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($products->count() > 0)
                <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
                    <p class="text-sm text-gray-600">
                        Menampilkan <span class="font-semibold">{{ ($products->currentPage() - 1) * $products->perPage() + 1 }}</span> 
                        hingga <span class="font-semibold">{{ min($products->currentPage() * $products->perPage(), $products->total()) }}</span> 
                        dari <span class="font-semibold">{{ $products->total() }}</span> barang
                    </p>
                    <div class="flex gap-2">
                        @if ($products->onFirstPage())
                            <button class="px-4 py-2 text-gray-400 cursor-not-allowed" disabled>
                                <i class="fa-solid fa-chevron-left"></i>
                            </button>
                        @else
                            <a href="{{ $products->previousPageUrl() }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                                <i class="fa-solid fa-chevron-left"></i>
                            </a>
                        @endif

                        @foreach ($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                            @if ($page == $products->currentPage())
                                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold">{{ $page }}</button>
                            @else
                                <a href="{{ $url }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">{{ $page }}</a>
                            @endif
                        @endforeach

                        @if ($products->hasMorePages())
                            <a href="{{ $products->nextPageUrl() }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
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

        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function(){
            document.querySelectorAll('.js-toggle-edit').forEach(function(btn){
                btn.addEventListener('click', function(){
                    var target = this.getAttribute('data-target');
                    var row = document.getElementById(target);
                    if(!row) return;
                    row.classList.toggle('hidden');
                    if(!row.classList.contains('hidden')) row.scrollIntoView({behavior: 'smooth', block: 'nearest'});
                });
            });

            document.querySelectorAll('.js-cancel-edit').forEach(function(btn){
                btn.addEventListener('click', function(){
                    var target = this.getAttribute('data-target');
                    var row = document.getElementById(target);
                    if(!row) return;
                    row.classList.add('hidden');
                });
            });

            // Dropdown kategori
            document.querySelectorAll('.category-dropdown-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var target = this.getAttribute('data-target');
                    var dropdown = document.getElementById(target);
                    if (!dropdown) return;
                    dropdown.classList.toggle('hidden');
                });
            });

            // Update selected text
            document.querySelectorAll('input[name="categories[]"]').forEach(function(checkbox) {
                checkbox.addEventListener('change', function() {
                    var productId = this.closest('.relative').querySelector('.category-dropdown-btn').getAttribute('data-target').replace('category-dropdown-', '');
                    updateSelectedCategories(productId);
                });
            });
        });

        function updateSelectedCategories(productId) {
            var checkboxes = document.querySelectorAll('#category-dropdown-' + productId + ' input[name="categories[]"]:checked');
            var selected = Array.from(checkboxes).map(cb => cb.value);
            var span = document.getElementById('selected-categories-' + productId);
            if (selected.length > 0) {
                span.textContent = selected.join(', ');
            } else {
                span.textContent = 'Pilih Kategori';
            }
        }
    </script>

</body>
</html>
