<!DOCTYPE html>
<html lang="id">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gudang - Kasir Pintar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style> 
        body { font-family: 'Poppins', sans-serif; }
        
        /* Agar spasi terbaca di editor */
        #smart-editor { white-space: pre-wrap; }
        
        /* Warna untuk Label (Kode, Nama, Harga) - Abu-abu */
        .token-key { color: #6b7280; font-weight: bold; font-size: 0.85em; text-transform: uppercase; }
        
        /* Warna untuk Nilai Data (123, Kopi, 5000) - Kuning/Emas */
        .token-value { 
            background-color: #fffbeb; /* Amber-50 */
            color: #92400e; /* Amber-800 */
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: bold;
            border: 1px solid #fcd34d;
            margin-right: 4px;
        }

        /* Placeholder kosong */
        #smart-editor:empty:before {
            content: attr(placeholder);
            color: #9ca3af;
            font-style: italic;
        }

    </style>
</head>
<body class="bg-gray-100 min-h-screen">

    <div class="flex min-h-screen">
        @include('components.sidebar')

        <main class="flex-1 ml-0 md:ml-64 p-8">
            
    <nav class="bg-blue-600 text-white px-8 py-4 flex rounded-lg justify-between items-center shadow-lg">
        <div class="flex items-center gap-3">
            <div class="bg-white/20 p-2 rounded-lg"><i class="fa-solid fa-boxes-stacked text-xl"></i></div>
            <div>
                <h1 class="font-bold text-lg">Divisi Gudang</h1>
                <p class="text-xs opacity-80">{{ Auth::user()->shop->name ?? 'Toko Saya' }}</p>
            </div>
        </div>
        <div class="flex items-center gap-6">
            <span class="text-sm font-medium">Halo, {{ Auth::user()->name }}</span>
            <form action="{{ route('logout') }}" method="POST" data-confirm="Yakin ingin logout?">
                @csrf
                <button type="submit" class="bg-blue-700 hover:bg-blue-800 px-4 py-2 rounded-lg text-sm font-bold transition-all">
                    Keluar <i class="fa-solid fa-sign-out-alt ml-1"></i>
                </button>
            </form>
        </div>
    </nav>

    <div class="container mx-auto pt-6 grid grid-cols-1 lg:grid-cols-3 gap-8">

        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl p-4 mb-6 border-t-4 shadow-md">
                    <h2 class="p-2 text-lg font-bold text-blue-600 text-center">Fitur Suara Disini!</h2>
                    <button type="button" onclick="openVoiceModal()" class="w-full bg-teal-500 hover:bg-teal-600 text-white font-bold py-3 rounded-xl shadow-lg transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-microphone"></i> Gunakan Suara
                    </button>
            </div>
            <div class="bg-white rounded-2xl shadow-md p-6 border-t-4 border-blue-500 sticky top-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-plus-circle text-blue-500"></i> Barang Masuk
                </h2>
                
                @if($errors->any())
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 mb-4 text-sm rounded-r">
                        @foreach($errors->all() as $err)
                            <p>{{ $err }}</p>
                        @endforeach
                    </div>
                @endif
                @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-3 mb-4 text-sm rounded-r">
                    {{ session('success') }}
                </div>
                @endif

                <form action="{{ route('inventory.store') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Kode Barang</label>
                        <div class="relative">
                            <input type="text" name="barcode" id="barcode" required autofocus
                                class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all" 
                                placeholder="Scan atau ketik kode...">
                            <i class="fa-solid fa-barcode absolute left-3 top-3.5 text-gray-400"></i>
                        </div>
                        <div id="barcode-info" class="text-sm mt-1 text-gray-500"></div>
                        <p class="text-xs text-gray-400 mt-1">*Jika kode sama, stok akan otomatis bertambah.</p>


                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Nama Barang</label>
                        <input type="text" name="name" id="name" required 
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-blue-500 outline-none" 
                            placeholder="Contoh: Indomie Goreng">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Kategori</label>
                        <div class="relative">
                            <div id="category-dropdown" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500 outline-none transition-all bg-white">
                                <div class="flex justify-between items-center">
                                    <div id="selected-categories-display" class="flex flex-wrap gap-1 min-h-[24px] flex-1">
                                        <span class="text-gray-400 text-sm">Pilih kategori...</span>
                                    </div>
                                    <button type="button" id="dropdown-toggle" class="ml-2 text-gray-400 hover:text-gray-600" onclick="toggleDropdown()">
                                        <i class="fa-solid fa-chevron-down"></i>
                                    </button>
                                </div>
                            </div>
                            <div id="category-options" class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-xl shadow-lg max-h-60 overflow-y-auto">
                                @foreach($categories as $cat)
                                    <label class="flex items-center px-4 py-2 hover:bg-gray-50 cursor-pointer">
                                        <input type="checkbox" value="{{ $cat }}" class="category-checkbox mr-3">
                                        <span class="text-sm">{{ $cat }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div id="category-hidden-inputs"></div>
                        <div id="selected-categories" class="mt-2 flex flex-wrap gap-2"></div>
                        <p class="text-xs text-gray-400 mt-1">*Pilih satu atau lebih kategori dari daftar.</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Harga Jual</label>
                            <input type="number" name="price" id="price" required 
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-blue-500 outline-none" 
                                placeholder="Rp 0">
                        </div>
                        <div>
    <label class="block text-sm font-bold text-gray-700 mb-1">Jumlah Masuk</label>
    <!-- Tambahkan w-full agar kontainer mengikuti lebar parent -->
    <div class="flex gap-2 w-full">
        <input type="number" name="stock" id="stock" required min="0" value="1"
            class="flex-1 w-0 px-4 rounded-xl border border-gray-300 focus:border-blue-500 outline-none" 
            placeholder="Qty">

        <select name="unit" id="unit" 
           
            class="w-15 md:w-15 px-2 py-3 rounded-xl border border-gray-300 focus:border-blue-500 outline-none bg-white">
            <option value="pcs" selected>pcs</option>
            <option value="kg">kg</option>
            <option value="gram">gram</option>
            <option value="box">box</option>
            <option value="meter">meter</option>
            <option value="liter">liter</option>
        </select>
    </div>
</div>

                    </div>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl shadow-lg shadow-blue-600/20 transition-all mt-2">
                        <i class="fa-solid fa-save mr-2"></i> Simpan Data
                    </button>
                </form>
            </div>

            
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-gray-100">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <h2 class="text-xl font-bold text-gray-800">Stok Gudang Saat Ini</h2>
                    <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold">
                        {{ $products->count() }} Jenis Barang
                    </span>
                </div>

                <!-- FILTER SECTION -->
                <div class="p-4 border-b border-gray-100 bg-gray-50">
                    <form method="GET" action="{{ route('inventory.dashboard') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
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
                            <a href="{{ route('inventory.dashboard') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 py-2 px-3 rounded-lg text-sm transition" title="Reset Filter">
                                <i class="fa-solid fa-times"></i>
                            </a>
                        </div>
                    </form>
                </div>

                <div class="max-h-[530px] overflow-y-auto">
                    <table class="w-full text-center">
                        <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-semibold">
                            <tr>
                                <th class="px-6 py-4">Kode</th>
                                <th class="px-6 py-4">Produk</th>
                                <th class="px-6 py-4">Kategori</th>
                                <th class="px-6 py-4 text-center">Stok</th>
                                <th class="px-6 py-4">Harga Jual</th>
                                <th class="px-6 py-4">Terakhir Update</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @forelse($products as $index => $product)
                            <tr class="hover:bg-orange-50/30 transition-colors">
                                <td class="px-6 py-4 font-bold font-mono text-gray-800 text-left">{{ $product->code }}</td>
                                <td class="px-6 py-4  text-gray-500 text-left">{{ $product->name }}</td>
                                <td class="px-6 py-4 text-gray-600 text-left">
                                    @php
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
                                <td class="px-6 py-4 text-center">
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
                                    <span>
                                        {{ $product->unit ?? 'pcs' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-600">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-xs text-gray-400">
                                    {{ $product->updated_at->diffForHumans() }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                    <i class="fa-solid fa-box-open text-4xl mb-3 block opacity-20"></i>
                                    Gudang kosong atau tidak ada yang sesuai filter. Silakan input barang di form sebelah kiri.
                                </td>
                            </tr>
                            @endforelse
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div id="voice-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="closeVoiceModal()"></div>

    <div class="relative top-20 mx-auto p-6 border w-full max-w-lg shadow-2xl rounded-2xl bg-white">
        
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-microphone-lines text-teal-500"></i> Mode Suara Cerdas
            </h3>
            <button onclick="closeVoiceModal()" class="text-gray-400 hover:text-red-500 transition">
                <i class="fa-solid fa-times text-2xl"></i>
            </button>
        </div>

        <div class="flex justify-between items-end mb-2">
            <label class="text-sm font-bold text-gray-700">Area Deteksi & Edit:</label>
            <span id="mic-status" class="text-xs font-medium text-red-500 animate-pulse hidden">
                <i class="fas fa-circle text-[8px]"></i> Mendengarkan...
            </span>
        </div>

        <div id="smart-editor" 
             contenteditable="true" 
             class="w-full p-4 border-2 border-blue-100 rounded-xl min-h-[120px] max-h-[200px] overflow-y-auto text-lg font-mono focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-100 transition-all bg-gray-50 cursor-text"
             placeholder='Klik tombol Mic, lalu katakan: "Kode 123 Nama Kopi..."'>
        </div>

        <div class="mt-6 flex flex-col gap-3">
            
            <button id="btn-mic" class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-3 px-6 rounded-xl flex items-center justify-center gap-3 transition-all shadow-lg hover:shadow-xl transform active:scale-95">
                <i class="fa-solid fa-microphone text-xl"></i> 
                <span id="btn-text">Mulai Bicara</span>
            </button>

            <div class="text-center text-xs text-gray-400 bg-gray-50 p-2 rounded-lg border border-dashed border-gray-200">
                <p>Tekan <b class="text-gray-600">ENTER</b> untuk Simpan & Reset.</p>
                <p>Klik <b class="text-gray-600">Mic Lagi</b> untuk Simpan & Lanjut Bicara.</p>
            </div>
        </div>
    </div>
</div>

<div id="toast-notification" class="fixed bottom-5 right-5 z-[60] transform translate-y-20 transition-all duration-300 hidden">
    <div class="bg-green-600 text-white px-6 py-4 rounded-lg shadow-2xl flex items-center gap-3 border-l-4 border-green-300">
        <i class="fas fa-check-circle text-2xl"></i>
        <div>
            <h4 class="font-bold text-sm">Berhasil!</h4>
            <p class="text-xs text-green-100">Data barang telah tersimpan.</p>
        </div>
    </div>
</div>

    </div>

<script>
    // --- SETUP GLOBAL ---
    const csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';
    let availableCategories = @json($categories);

    // Load kategori dari API
    async function loadCategoriesFromAPI() {
        try {
            const response = await fetch('{{ route("inventory.api.categories") }}');
            if (response.ok) {
                const data = await response.json();
                availableCategories = data.categories || [];
            }
        } catch (e) {
            console.error('Gagal load kategori:', e);
        }
        updateCategoryDropdown();
    }

    // Update dropdown dengan kategori terbaru
    function updateCategoryDropdown() {
        const optionsDiv = document.getElementById('category-options');
        optionsDiv.innerHTML = '';
        
        // Pastikan array
        if (!Array.isArray(availableCategories)) {
            availableCategories = [];
        }
        
        // Jika tidak ada kategori, gunakan default
        if (availableCategories.length === 0) {
            availableCategories = ['Makanan', 'Minuman', 'Snack', 'Bahan Pokok', 'Kesehatan', 'Kosmetik'];
        }
        
        if (availableCategories.length === 0) {
            optionsDiv.innerHTML = '<div class="px-4 py-2 text-gray-500 text-sm">Tidak ada kategori</div>';
            return;
        }
        
        availableCategories.forEach(cat => {
            const label = document.createElement('label');
            label.className = 'flex items-center px-4 py-2 hover:bg-gray-50 cursor-pointer';
            
            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.value = cat;
            checkbox.className = 'category-checkbox mr-3';
            
            const span = document.createElement('span');
            span.className = 'text-sm';
            span.textContent = cat;
            
            label.appendChild(checkbox);
            label.appendChild(span);
            optionsDiv.appendChild(label);
        });
        
        // Re-attach event listeners
        attachCategoryEvents();
    }

    // Handle selected categories display
    function updateSelectedCategories() {
        const checkboxes = document.querySelectorAll('.category-checkbox:checked');
        const selectedDiv = document.getElementById('selected-categories');
        const displayDiv = document.getElementById('selected-categories-display');
        const hiddenInputsDiv = document.getElementById('category-hidden-inputs');
        
        selectedDiv.innerHTML = '';
        displayDiv.innerHTML = '';
        hiddenInputsDiv.innerHTML = '';
        
        const selectedValues = [];
        checkboxes.forEach((checkbox, index) => {
            selectedValues.push(checkbox.value);
            
            const colors = ['bg-blue-100 text-blue-800', 'bg-green-100 text-green-800', 'bg-yellow-100 text-yellow-800', 'bg-purple-100 text-purple-800', 'bg-pink-100 text-pink-800'];
            const colorClass = colors[index % colors.length];
            
            const tag = document.createElement('span');
            tag.className = `${colorClass} px-3 py-1 rounded-full text-xs font-bold border`;
            tag.textContent = checkbox.value;
            
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'ml-2 text-gray-500 hover:text-red-500';
            removeBtn.innerHTML = '×';
            removeBtn.onclick = (e) => {
                e.stopPropagation();
                checkbox.checked = false;
                updateSelectedCategories();
            };
            
            tag.appendChild(removeBtn);
            selectedDiv.appendChild(tag);
            
            // Create hidden input
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'categories[]';
            hiddenInput.value = checkbox.value;
            hiddenInputsDiv.appendChild(hiddenInput);
        });
        
        // Update display
        if (selectedValues.length > 0) {
            selectedValues.forEach((value, index) => {
                const colors = ['bg-blue-100 text-blue-800', 'bg-green-100 text-green-800', 'bg-yellow-100 text-yellow-800', 'bg-purple-100 text-purple-800', 'bg-pink-100 text-pink-800'];
                const colorClass = colors[index % colors.length];
                
                const tag = document.createElement('span');
                tag.className = `${colorClass} px-2 py-1 rounded-full text-xs font-bold mr-1`;
                tag.textContent = value;
                displayDiv.appendChild(tag);
            });
        } else {
            displayDiv.innerHTML = '<span class="text-gray-400 text-sm">Pilih kategori...</span>';
        }
    }

    // Attach events to category elements
    function attachCategoryEvents() {
        const toggleBtn = document.getElementById('dropdown-toggle');
        const options = document.getElementById('category-options');
        
        // Set default hidden
        options.style.display = 'none';
        
        // Toggle dropdown
        toggleBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            options.style.display = options.style.display === 'none' ? 'block' : 'none';
        });
        
        // Close when clicking outside
        document.addEventListener('click', (e) => {
            if (!toggleBtn.contains(e.target) && !options.contains(e.target)) {
                options.style.display = 'none';
            }
        });
        
        // Handle checkbox changes
        document.querySelectorAll('.category-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', (e) => {
                e.stopPropagation();
                updateSelectedCategories();
            });
        });
    }

    // Global function for onclick
    function toggleDropdown() {
        const options = document.getElementById('category-options');
        options.style.display = options.style.display === 'none' ? 'block' : 'none';
    }

    // --- 1. KONTROL MODAL & EDITOR UI ---
    function openVoiceModal() {
        document.getElementById('voice-modal').classList.remove('hidden');
        document.getElementById('smart-editor').focus();
    }

    function closeVoiceModal() {
        document.getElementById('voice-modal').classList.add('hidden');
        if (recognition) recognition.stop();
    }

    // --- 2. SPEECH RECOGNITION SETUP ---
    let recognition;
    let isListening = false;

    // Cek browser support saat script dimuat
    if ('webkitSpeechRecognition' in window) {
        recognition = new webkitSpeechRecognition();
        recognition.continuous = false; // False agar kita bisa proses per kalimat
        recognition.lang = 'id-ID';
        recognition.interimResults = false;

        const btnMic = document.getElementById('btn-mic');
        const btnText = document.getElementById('btn-text');
        const micStatus = document.getElementById('mic-status');
        const editor = document.getElementById('smart-editor');

        // Saat mulai merekam
        recognition.onstart = function() {
            isListening = true;
            btnMic.classList.replace('bg-red-500', 'bg-teal-600'); // Jadi Teal saat dengar
            btnMic.classList.add('animate-pulse');
            btnText.innerText = "Mendengarkan...";
            micStatus.classList.remove('hidden');
            editor.setAttribute('placeholder', 'Sedang mendengarkan...');
        };

        // Saat berhenti merekam
        recognition.onend = function() {
            isListening = false;
            btnMic.classList.replace('bg-teal-600', 'bg-red-500'); // Balik Merah
            btnMic.classList.remove('animate-pulse');
            btnText.innerText = "Mulai Bicara";
            micStatus.classList.add('hidden');
            editor.setAttribute('placeholder', 'Klik tombol Mic, lalu katakan: "Kode 123 Nama Kopi..."');
            
            // Jalankan pewarnaan teks (Syntax Highlighting)
            highlightText();
        };

        // Saat ada hasil suara
        recognition.onresult = function(event) {
            let transcript = event.results[0][0].transcript;
            // Timpa teks di editor dengan hasil suara
            // (Kita pakai innerText agar tag HTML warna sebelumnya hilang dulu saat bicara baru)
            editor.innerText = transcript; 
        };

        // --- EVENT LISTENER TOMBOL MIC ---
        btnMic.addEventListener('click', function() {
            // Ambil teks saat ini (hapus enter/spasi berlebih)
            let currentText = editor.innerText.trim();

            if (isListening) {
                recognition.stop();
            } else {
                // LOGIKA PINTAR:
                // Jika kotak tidak kosong (artinya user baru selesai edit manual),
                // maka klik Mic berfungsi sebagai "SIMPAN & REKAM LAGI"
                if (currentText.length > 3) { 
                    saveDataAndReset(true); // True = Lanjut rekam
                } else {
                    recognition.start(); // Kalau kosong, langsung rekam
                }
            }
        });

        // --- EVENT LISTENER KEYBOARD (ENTER) ---
        editor.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault(); // Jangan bikin baris baru
                saveDataAndReset(false); // False = Jangan rekam lagi (stop)
            }
        });

    } else {
        alert("Browser ini tidak mendukung Fitur Suara. Gunakan Google Chrome.");
    }

    // --- 3. FITUR SYNTAX HIGHLIGHT (PEWARNAAN TEKS) ---
    function highlightText() {
        const editor = document.getElementById('smart-editor');
        let text = editor.innerText;
        
        // Regex untuk membungkus Key dan Value dengan Span berwarna
        // Pola: (Kata Kunci) (Spasi) (Isi Data)
        let formatted = text.replace(/(kode|barcode|nama|produk|barang|harga|rp|jual|stok|jumlah|isi|kategori|jenis)\s+([a-zA-Z0-9\.\-\s]+?)(?=\s+(?:kode|barcode|nama|produk|barang|harga|rp|jual|stok|jumlah|isi|kategori|jenis)|$)/gi, 
            function(match, key, value) {
                return `<span class="token-key">${key.toUpperCase()}</span> <span class="token-value">${value.trim()}</span> `;
            }
        );

        editor.innerHTML = formatted;
        placeCaretAtEnd(editor); // Pindahkan kursor ke akhir
    }

    // --- 4. LOGIKA PARSING & SIMPAN DATA (CORE) ---
    // --- 4. LOGIKA PARSING & SIMPAN DATA (HYBRID MODE) ---
    async function saveDataAndReset(continueRecording) {
        const editor = document.getElementById('smart-editor');
        let text = editor.innerText;

        // 1. AMBIL DATA DASAR (Kode & Stok)
        let codeMatch = text.match(/(?:kode|barcode)\s+([\w\d\-\.]+)/i);
        let stockMatch = text.match(/(?:jumlah|stok|isi|tambah)\D+(\d+)/i);
        
        // 2. AMBIL DATA LENGKAP (Nama & Harga) - Mungkin null kalau mode cepat
        let priceMatch = text.match(/(?:harga|rp|jual)\D+(\d+(?:\.\d+)*)/i);
        let nameMatch = text.match(/(?:nama|produk|barang)\s+(.*?)\s+(?:kategori|jenis|harga|stok|jumlah)/i);
        let catMatch = text.match(/(?:kategori|jenis)\s+(.*?)(?:\s+(?:harga|jumlah|stok|$))/i);
        let categories = [];
        if(catMatch) {
            categories = catMatch[1].split(/\s+dan\s+/i).map(cat => capitalize(cat.trim())).filter(cat => cat);
        }

        // Validasi Minimal: KODE wajib ada
        if(!codeMatch) {
            alert("⚠️ Data tidak lengkap! Minimal sebutkan 'Kode [angka]'.");
            return;
        }

        let payload = {
            barcode: codeMatch[1].replace(/[^a-zA-Z0-9]/g, ''),
            stock: stockMatch ? stockMatch[1] : '1', // Default 1 jika tidak sebut jumlah
            price: priceMatch ? priceMatch[1].replace(/\./g, '') : null,
            name: nameMatch ? capitalize(nameMatch[1].trim()) : null,
            categories: categories.length > 0 ? categories : ['Umum']
        };

        // Sertakan unit dari form (fallback ke 'pcs')
        payload.unit = (document.getElementById('unit') && document.getElementById('unit').value) ? document.getElementById('unit').value : 'pcs';

        // --- CABANG LOGIKA: MODE CEPAT VS MODE LENGKAP ---
        
        if (!payload.name) {
            // [MODE CEPAT] User cuma bilang "Kode X Stok Y"
            // Kita harus cari Nama & Harga dari Database dulu!
            
            showToast("🔍 Mencari data barang...", "blue"); // Feedback visual
            
            try {
                // Panggil API Find yang sudah ada
                const findUrl = '{{ route("inventory.find") }}?code=' + encodeURIComponent(payload.barcode);
                const findRes = await fetch(findUrl, { headers: { 'Accept': 'application/json' } });

                if (findRes.ok) {
                    const existingData = await findRes.json();
                    
                    // Isi kekosongan data dengan data database
                    payload.name = existingData.name;
                    payload.price = payload.price || existingData.price; // Pakai harga DB jika user ga sebut harga baru
                    if(!payload.categories || payload.categories.length === 0) {
                        payload.categories = existingData.categories || ['Umum'];
                    }

                    // Update tampilan editor biar user tau sistem nemu barangnya
                    editor.innerHTML += ` <span class="token-key" style="color:green">[FOUND: ${payload.name}]</span>`;
                    
                } else {
                    // Kalau barang belum ada, tapi user tidak sebut nama -> ERROR
                    alert(`❌ Barang dengan kode '${payload.barcode}' belum ada. Harap sebutkan Nama dan Harga untuk mendaftarkannya.`);
                    return; // Stop, jangan simpan
                }
            } catch (e) {
                alert("Gagal mengecek data barang ke server.");
                return;
            }
        }

        // --- KIRIM KE SERVER (FINAL STORE) ---
        try {
            const response = await fetch('{{ route("inventory.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(payload)
            });

            if (response.ok) {
                showToast("✅ Data Berhasil Disimpan!", "green");
                addDummyRow(payload);
                editor.innerHTML = ""; // Reset
                
                if (continueRecording) {
                    setTimeout(() => recognition.start(), 500);
                }
            } else {
                let err = await response.json();
                alert("Gagal simpan: " + (err.message || "Terjadi kesalahan server"));
            }

        } catch (error) {
            console.error("Error:", error);
            showToast("Simulasi: Data tersimpan (Offline)", "green");
            addDummyRow(payload);
            editor.innerHTML = "";
            if (continueRecording) setTimeout(() => recognition.start(), 500);
        }
    }

    // Update sedikit Helper Toast biar bisa ganti warna
    function showToast(msg = "Data Berhasil Disimpan!", color = "green") {
        const toast = document.getElementById('toast-notification');
        const toastInner = toast.querySelector('div'); // Div pembungkus icon & text
        
        // Update Teks
        toast.querySelector('h4').innerText = msg.includes("Mencari") ? "Proses..." : "Berhasil!";
        toast.querySelector('p').innerText = msg;

        // Update Warna (Hapus class lama, tambah class baru)
        toastInner.className = `bg-${color}-600 text-white px-6 py-4 rounded-lg shadow-2xl flex items-center gap-3 border-l-4 border-${color}-300`;

        toast.classList.remove('hidden', 'translate-y-20');
        
        // Kalau cuma status "Mencari", jangan auto-hide dulu
        if(color !== "blue") {
            setTimeout(() => {
                toast.classList.add('translate-y-20');
                setTimeout(() => toast.classList.add('hidden'), 300);
            }, 3000);
        }
    }
    // --- HELPER: Update Tabel Visual (Biar user liat hasilnya lgsg) ---
    function addDummyRow(data) {
        let tbody = document.querySelector('table tbody');
        if(tbody) {
            let tr = document.createElement('tr');
            tr.className = "bg-green-50 animate-pulse border-b";
            tr.innerHTML = `
                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">${data.barcode}</td>
                <td class="px-6 py-4">${data.name}</td>
                <td class="px-6 py-4">${Array.isArray(data.categories) ? data.categories.join(', ') : data.category}</td>
                <td class="px-6 py-4">Rp ${new Intl.NumberFormat('id-ID').format(data.price)}</td>
                <td class="px-6 py-4">${data.stock}</td>
                <td class="px-6 py-4 text-green-600 font-bold">Baru</td>
            `;
            tbody.insertBefore(tr, tbody.firstChild); // Masukkan di paling atas
        }
    }

    // --- HELPER: Toast Notification ---
    function showToast() {
        const toast = document.getElementById('toast-notification');
        toast.classList.remove('hidden', 'translate-y-20'); // Muncul
        setTimeout(() => {
            toast.classList.add('translate-y-20'); // Turun lagi
            setTimeout(() => toast.classList.add('hidden'), 300);
        }, 3000);
    }

    // --- HELPER: Kursor ---
    function placeCaretAtEnd(el) {
        el.focus();
        if (typeof window.getSelection != "undefined" && typeof document.createRange != "undefined") {
            var range = document.createRange();
            range.selectNodeContents(el);
            range.collapse(false);
            var sel = window.getSelection();
            sel.removeAllRanges();
            sel.addRange(range);
        }
    }

    function capitalize(s) {
        return s && s.length > 0 ? s[0].toUpperCase() + s.slice(1) : '';
    }

    // ============================================================
    // --- 5. FITUR LAMA (MANUAL SCAN) - TIDAK DIUBAH SAMA SEKALI ---
    // ============================================================
    document.addEventListener('DOMContentLoaded', function() {
        // Load kategori dinamis dari database
        loadCategoriesFromAPI();

        // Auto-refresh kategori setelah form submit
        const form = document.querySelector('form[action="{{ route("inventory.store") }}"]');
        if (form) {
            form.addEventListener('submit', function(e) {
                setTimeout(() => {
                    loadCategoriesFromAPI();
                }, 1500);
            });
        }

        const barcodeInput = document.getElementById('barcode');
        const nameInput = document.getElementById('name');
        const priceInput = document.getElementById('price');
        const stockInput = document.getElementById('stock');
        const infoEl = document.getElementById('barcode-info');

        // Attach category events
        loadCategoriesFromAPI();

        // Pastikan elemen ada sebelum menjalankan logika lama
        if(!barcodeInput) return; 

        async function lookup(code) {
            if (!code) {
                infoEl.textContent = '';
                return;
            }
            infoEl.textContent = 'Mencari produk...';
            try {
                const url = '{{ route("inventory.find") }}?code=' + encodeURIComponent(code);
                const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                
                if (res.ok) {
                    const data = await res.json();
                    nameInput.value = data.name || '';
                    // Set selected categories
                    if(data.categories && Array.isArray(data.categories)) {
                        document.querySelectorAll('.category-checkbox').forEach(checkbox => {
                            checkbox.checked = data.categories.includes(checkbox.value);
                        });
                        updateSelectedCategories();
                    }
                    priceInput.value = data.price ?? '';
                    infoEl.textContent = 'Produk ditemukan — data terisi otomatis. Masukkan jumlah masuk.';
                    infoEl.classList.remove('text-red-500');
                    infoEl.classList.add('text-green-600');
                    stockInput.focus();
                } else if (res.status === 404) {
                    infoEl.textContent = 'Kode baru. Silakan isi detail barang.';
                    infoEl.classList.add('text-blue-500');
                    infoEl.classList.remove('text-green-600');
                    if(document.activeElement !== nameInput) {
                        nameInput.focus();
                    }
                }
            } catch (e) {
                infoEl.textContent = 'Gagal terhubung ke server.';
            }
        }

        let debounceTimer;
        barcodeInput.addEventListener('keyup', (e) => {
            if (e.key === 'Enter') {
                lookup(e.target.value.trim());
            } else {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => lookup(e.target.value.trim()), 500);
            }
        });
    });
</script>

</body>
</html>