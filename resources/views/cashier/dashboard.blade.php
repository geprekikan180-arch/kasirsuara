<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir Suara - Kasir Pintar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style> 
        body { font-family: 'Poppins', sans-serif; } 
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn { animation: fadeIn 0.3s ease-out forwards; }
        
        /* Animasi Gelombang Suara */
        .voice-wave { display: inline-flex; align-items: center; justify-content: center; height: 20px; }
        .voice-bar { background-color: #3b82f6; width: 4px; margin: 0 2px; border-radius: 2px; animation: sound 0ms -800ms linear infinite alternate; }
        @keyframes sound { 0% { height: 3px; opacity: .35; } 100% { height: 16px; opacity: 1; } }
    </style>
</head>
<body class="bg-gray-100 h-screen w-screen overflow-hidden flex">
    <div class="hidden md:flex flex-col w-64 bg-white h-full border-r border-gray-200 shrink-0">
        @include('components.sidebar')
    </div>
    
    <main class="flex-1 flex flex-col h-full min-w-0 relative">
        
        <div class="p-6 pb-2 shrink-0 z-20">
            <header class="bg-blue-600 p-4 flex rounded-xl justify-between items-center shadow-lg shadow-blue-600/20">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-blue-400 rounded-lg flex items-center justify-center text-white font-bold text-xl">
                        KP
                    </div>
                    <div class="hidden sm:block">
                        <h1 class="font-bold text-white text-lg leading-tight">Kasir</h1>
                        <p class="text-xs text-blue-100">{{ date('d M Y') }}</p>
                    </div>
                </div>

                <div class="flex gap-2 items-center">
                    <div class="relative w-48 md:w-80 transition-all">
                        <input type="text" id="searchInput" placeholder="Cari barang..." class="w-full pl-10 pr-4 py-2.5 rounded-full bg-white/10 text-white placeholder-blue-200 border border-transparent focus:bg-white focus:text-gray-800 focus:placeholder-gray-400 focus:ring-2 focus:ring-blue-400 transition-all text-sm">
                        <i class="fa-solid fa-search absolute left-4 top-3 text-blue-200"></i>
                    </div>

                    <button id="btn-mic" onclick="toggleVoiceMode()" class="w-10 h-10 bg-white/20 hover:bg-white text-white hover:text-blue-600 rounded-full flex items-center justify-center transition-all shadow-sm backdrop-blur-sm" title="Perintah Suara">
                        <i class="fa-solid fa-microphone"></i>
                    </button>
                </div>
            </header>

            <div class="mt-4 flex gap-3 overflow-x-auto no-scrollbar pb-2" id="category-buttons">
                <!-- Akan diisi oleh JavaScript -->
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-6 pt-0 no-scrollbar">
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 pb-20" id="product-list">
                @foreach($products as $product)
                <button onclick="addToCart({{ $product->id }}, '{{ $product->name }}', {{ $product->price }}, '{{ $product->code }}', '{{ $product->image }}')" 
                    class="bg-white p-2 rounded-lg shadow-sm hover:shadow-lg transition-all text-left group border border-transparent hover:border-blue-500 relative overflow-hidden flex flex-col h-full">
                    
                    <div class="absolute top-1 right-1 bg-gray-100 text-gray-500 text-[8px] font-bold px-1.5 py-0.5 rounded">
                        {{ $product->code }}
                    </div>

                    <div class="h-20 w-full bg-gray-50 rounded-lg mb-2 flex items-center justify-center overflow-hidden relative">
                        @if($product->image)
                             <img src="{{ $product->image }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                        @else
                             <i class="fa-solid fa-box-open text-3xl text-gray-300"></i>
                        @endif
                        <div class="absolute inset-0 bg-blue-600/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                            <div class="bg-blue-600 text-white w-8 h-8 rounded-full flex items-center justify-center shadow-lg scale-0 group-hover:scale-100 transition-transform">
                                <i class="fa-solid fa-plus"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex-1 flex flex-col justify-between">
                        <h3 class="font-bold text-gray-800 text-sm leading-tight line-clamp-2 mb-1 group-hover:text-blue-600 flex justify-between">
                            <span>{{ $product->name }}</span>
                            <span class="text-[15px] text-gray-500">{{ $product->stock }} {{ $product->unit }}</span>
                        </h3>
                        <p class="font-bold text-blue-600 text-sm">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                    </div>
                </button>
                @endforeach
            </div>
        </div>

        <div id="voice-overlay" class="hidden absolute bottom-6 left-1/2 transform -translate-x-1/2 bg-gray-900/90 backdrop-blur-md text-white px-6 py-4 rounded-2xl shadow-2xl z-50 flex-col items-center w-80 border border-gray-700 transition-all duration-300">
            <div class="flex items-center justify-between w-full mb-3 border-b border-gray-700 pb-2">
                <div class="flex items-center gap-2">
                    <div class="voice-wave">
                        <div class="voice-bar" style="animation-duration: 474ms;"></div>
                        <div class="voice-bar" style="animation-duration: 433ms;"></div>
                        <div class="voice-bar" style="animation-duration: 407ms;"></div>
                    </div>
                    <span class="text-xs font-bold text-blue-400 uppercase tracking-wider">Mendengarkan</span>
                </div>
                <button onclick="toggleVoiceMode()" class="text-xs text-gray-400 hover:text-white"><i class="fa-solid fa-times"></i></button>
            </div>
            <p id="voice-text" class="text-center text-lg font-medium text-white mb-1">...</p>
            <p class="text-[10px] text-gray-400 text-center">Contoh: "Tambah 2 Sapi", "Bayar"</p>
        </div>

    </main>

    <aside class="w-96 bg-white border-l border-gray-200 h-full flex flex-col shadow-2xl z-30 shrink-0">
        
        <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-yellow-100 text-yellow-600 flex items-center justify-center font-bold text-sm border-2 border-white shadow-sm">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div>
                    <h4 class="font-bold text-gray-800 text-sm">{{ Auth::user()->name }}</h4>
                    <div class="flex items-center gap-1">
                        <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                        <p class="text-[10px] text-gray-500 font-medium">Online</p>
                    </div>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-8 h-8 rounded-full hover:bg-red-50 text-gray-400 hover:text-red-500 transition flex items-center justify-center" title="Logout">
                    <i class="fa-solid fa-power-off"></i>
                </button>
            </form>
        </div>

        <div class="p-4 shrink-0">
            <div class="flex justify-between items-end">
                <div>
                    <h2 class="font-bold text-xl text-gray-800">Keranjang</h2>
                    <p class="text-xs text-gray-400">Order ID: <span class="font-mono text-gray-600">#TRX-{{ rand(1000,9999) }}</span></p>
                </div>
                <button onclick="cart = []; updateCartUI();" class="text-xs text-red-500 hover:text-red-700 font-medium">Hapus Semua</button>
            </div>
        </div>

        <div id="cart-items" class="flex-1 overflow-y-auto p-4 space-y-3 bg-white">
            <div class="h-full flex flex-col items-center justify-center text-gray-300 space-y-3">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center">
                    <i class="fa-solid fa-cart-shopping text-3xl opacity-50"></i>
                </div>
                <p class="text-sm">Belum ada pesanan</p>
            </div>
        </div>

        <div class="p-5 bg-gray-50 border-t border-gray-200 shrink-0">
            
            <div class="bg-white p-3 rounded-xl border border-gray-200 mb-4 shadow-sm">
                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1 block">Tunai Diterima</label>
                <div class="flex items-center gap-2">
                    <span class="text-gray-400 font-bold text-lg">Rp</span>
                    <input type="number" id="cashInput" class="w-full bg-transparent border-none focus:ring-0 text-xl font-bold text-gray-800 p-0 placeholder-gray-300" placeholder="0">
                </div>
            </div>

            <div class="space-y-3 mb-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-500 text-sm">Subtotal</span>
                    <span class="font-bold text-gray-700" id="cart-total-plain">Rp 0</span>
                </div>
                <div class="flex justify-between items-center pt-3 border-t border-gray-200">
                    <span class="text-lg font-bold text-gray-800">Total</span>
                    <span class="text-xl font-bold text-blue-600" id="cart-total">Rp 0</span>
                </div>
            </div>

            <button id="btn-pay" onclick="processPayment()" class="w-full bg-gray-800 hover:bg-gray-900 text-white py-4 rounded-xl font-bold shadow-lg shadow-gray-800/20 flex justify-between px-6 items-center group transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                <span>Bayar Sekarang</span>
                <div class="w-8 h-8 bg-white/10 rounded-full flex items-center justify-center group-hover:translate-x-1 transition-transform">
                    <i class="fa-solid fa-arrow-right text-sm"></i>
                </div>
            </button>
        </div>

    </aside>

    <script>
        const productsData = @json($products);
    </script>

    <script>
        // --- STATE GLOBAL ---
        let cart = [];
        let allProducts = @json($products); // Data produk asli
        let allCategories = []; // Kategori yang tersedia
        let selectedCategory = 'semua'; // Kategori yang dipilih
        let searchQuery = ''; // Query pencarian
        
        // --- 1. CORE FUNCTIONS (KERANJANG & FORMAT RUPIAH) ---
        
        const formatRupiah = (number) => {
            return new Intl.NumberFormat('id-ID', { 
                style: 'currency', currency: 'IDR', minimumFractionDigits: 0 
            }).format(number);
        }

        function addToCart(id, name, price, code, image = null) {
            const existingItem = cart.find(item => item.id === id);
            if (existingItem) {
                existingItem.qty += 1;
            } else {
                cart.push({ id, name, price, code, image, qty: 1 });
            }
            updateCartUI();
        }

        // Helper khusus Voice: Tambah Qty Spesifik
        function addToCartQty(id, qty) {
            const product = productsData.find(p => p.id === id);
            if(!product) return;

            const existingItem = cart.find(item => item.id === id);
            if (existingItem) {
                existingItem.qty += qty;
            } else {
                cart.push({ 
                    id: product.id, name: product.name, price: product.price, 
                    code: product.code, image: product.image, qty: qty 
                });
            }
            updateCartUI();
        }

        function updateQty(id, change) {
            const item = cart.find(i => i.id === id);
            if (item) {
                item.qty += change;
                if (item.qty <= 0) {
                    cart = cart.filter(i => i.id !== id);
                }
                updateCartUI();
            }
        }

        function updateCartUI() {
            const cartContainer = document.getElementById('cart-items');
            const totalLabel = document.getElementById('cart-total');
            const totalPlainLabel = document.getElementById('cart-total-plain');
            
            cartContainer.innerHTML = '';
            let grandTotal = 0;

            if (cart.length === 0) {
                cartContainer.innerHTML = `
                    <div class="h-full flex flex-col items-center justify-center text-gray-300 space-y-3">
                        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center">
                            <i class="fa-solid fa-cart-shopping text-3xl opacity-50"></i>
                        </div>
                        <p class="text-sm">Belum ada pesanan</p>
                    </div>`;
            }

            cart.forEach(item => {
                const totalItemPrice = item.price * item.qty;
                grandTotal += totalItemPrice;
                const imgDisplay = item.image ? `<img src="${item.image}" class="w-full h-full object-cover">` : `<div class="w-full h-full flex items-center justify-center bg-gray-200 text-xs text-gray-500"><i class="fa-solid fa-box"></i></div>`;

                cartContainer.innerHTML += `
                <div class="flex gap-3 items-start animate-fadeIn group">
                    <div class="w-14 h-14 bg-gray-100 rounded-lg overflow-hidden shrink-0 border border-gray-100">
                        ${imgDisplay}
                    </div>
                    <div class="flex-1 min-w-0 pt-0.5">
                        <div class="flex justify-between items-start">
                            <h4 class="font-bold text-gray-800 text-sm truncate pr-2">${item.name}</h4>
                            <p class="text-xs text-blue-600 font-bold whitespace-nowrap">${formatRupiah(item.price)}</p>
                        </div>
                        <p class="text-[10px] text-gray-400 mb-2">${item.code}</p>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 bg-gray-100 rounded-md p-0.5">
                                <button onclick="updateQty(${item.id}, -1)" class="w-5 h-5 rounded bg-white text-gray-600 hover:text-red-500 shadow-sm text-[10px] font-bold flex items-center justify-center"><i class="fa-solid fa-minus"></i></button>
                                <span class="text-xs font-bold w-6 text-center text-gray-700">${item.qty}</span>
                                <button onclick="updateQty(${item.id}, 1)" class="w-5 h-5 rounded bg-blue-600 text-white hover:bg-blue-700 shadow-sm text-[10px] font-bold flex items-center justify-center"><i class="fa-solid fa-plus"></i></button>
                            </div>
                            <span class="text-xs font-bold text-gray-700">${formatRupiah(totalItemPrice)}</span>
                        </div>
                    </div>
                </div>
                <hr class="border-dashed border-gray-100">`;
            });

            if(totalLabel) totalLabel.innerText = formatRupiah(grandTotal);
            if(totalPlainLabel) totalPlainLabel.innerText = formatRupiah(grandTotal);
        }

        function calculateTotal() {
            return cart.reduce((total, item) => total + (item.price * item.qty), 0);
        }

        function processPayment() {
            if (cart.length === 0) {
                alert("Keranjang masih kosong!");
                return;
            }
            let cashInput = document.getElementById('cashInput').value;
            let total = calculateTotal();

            if (!cashInput || parseInt(cashInput) < total) {
                alert("Uang pembayaran kurang! Total: " + formatRupiah(total));
                return;
            }

            let payBtn = document.getElementById('btn-pay');
            let originalContent = payBtn.innerHTML;
            payBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin animate-spin"></i> Memproses...';
            payBtn.disabled = true;

            fetch("{{ route('cashier.process') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    cart: cart,
                    total_price: total,
                    cash_paid: cashInput
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    let kembalian = cashInput - total;
                    alert("✅ TRANSAKSI BERHASIL!\n\nKembalian: " + formatRupiah(kembalian));
                    window.location.reload(); 
                } else {
                    alert("❌ Gagal: " + data.message);
                    payBtn.innerHTML = originalContent;
                    payBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert("Terjadi kesalahan sistem.");
                payBtn.innerHTML = originalContent;
                payBtn.disabled = false;
            });
        }

        // --- 2. FITUR VOICE COMMAND (LOGIC DIPERBAIKI) ---
        
        const btnMic = document.getElementById('btn-mic');
        const voiceOverlay = document.getElementById('voice-overlay');
        const voiceText = document.getElementById('voice-text');
        
        let recognition;
        let isVoiceActive = false;

        if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            recognition = new SpeechRecognition();
            recognition.lang = 'id-ID';
            recognition.interimResults = false;
            recognition.maxAlternatives = 1;

            recognition.onstart = function() {
                isVoiceActive = true;
                updateMicUI(true);
            };

            recognition.onend = function() {
                if (isVoiceActive) recognition.start();
                else updateMicUI(false);
            };

            recognition.onresult = function(event) {
                const transcript = event.results[0][0].transcript.toLowerCase();
                console.log("Suara didengar:", transcript); // Debugging di Console
                
                // Tampilkan teks sementara agar user tau sistem dengar apa
                voiceText.innerText = `"${transcript}"`;
                
                processVoiceCommand(transcript);

                setTimeout(() => {
                    if(isVoiceActive) voiceText.innerText = "...";
                }, 3000);
            };

            recognition.onerror = function(event) {
                console.log('Voice Error:', event.error);
                if(event.error === 'not-allowed') {
                    alert('Izinkan akses mikrofon untuk menggunakan fitur suara.');
                    isVoiceActive = false;
                    updateMicUI(false);
                }
            };
        } else {
            if(btnMic) btnMic.style.display = 'none';
        }

        function toggleVoiceMode() {
            if (!recognition) return;
            if (!isVoiceActive) {
                recognition.start();
            } else {
                isVoiceActive = false;
                recognition.stop();
            }
        }

        function updateMicUI(active) {
            if(!btnMic || !voiceOverlay) return;
            if (active) {
                btnMic.classList.remove('bg-white/20', 'text-white');
                btnMic.classList.add('bg-red-500', 'text-white', 'animate-pulse', 'ring-4', 'ring-red-200');
                voiceOverlay.classList.remove('hidden');
                voiceOverlay.classList.add('flex');
            } else {
                btnMic.classList.add('bg-white/20', 'text-white');
                btnMic.classList.remove('bg-red-500', 'text-white', 'animate-pulse', 'ring-4', 'ring-red-200');
                voiceOverlay.classList.add('hidden');
                voiceOverlay.classList.remove('flex');
                if(voiceText) voiceText.innerText = "...";
            }
        }

        // --- OTAK UTAMA: Parsing Perintah ---
        function processVoiceCommand(cmd) {
            // Bersihkan tanda baca
            cmd = cmd.replace(/[.,\/#!$%\^&\*;:{}=\-_`~()]/g,"");

            // A. Perintah SELESAI
            if (cmd.includes('selesai') || cmd.includes('matikan') || cmd.includes('tutup')) {
                toggleVoiceMode();
                return;
            }

            // B. Perintah BAYAR
            if (cmd.includes('bayar') || cmd.includes('proses')) {
                processPayment();
                return;
            }

            // C. Perintah KOSONGKAN
            if (cmd.includes('kosongkan') || cmd.includes('hapus semua')) {
                cart = [];
                updateCartUI();
                voiceText.innerText = "🗑️ Keranjang dikosongkan";
                return;
            }

            // D. Perintah UANG (FIX LOGIC)
            // Keyword diperbanyak: uang, tunai, cash, bayarnya, duit
            if (cmd.match(/uang|tunai|cash|bayarnya|duit/)) {
                // Hapus kata kunci agar sisa angkanya saja
                // Hapus juga kata 'rupiah' biar tidak bingung
                let moneyStr = cmd.replace(/uang|tunai|cash|bayarnya|duit|rupiah/g, '').trim();
                
                // Konversi ke Angka
                let nominal = parseIndonesianNumber(moneyStr);
                
                console.log("Input Uang:", moneyStr, "->", nominal); // Debugging

                if (nominal > 0) {
                    const cashInput = document.getElementById('cashInput');
                    if(cashInput) {
                        cashInput.value = nominal;
                        // Trigger event change manual (kadang diperlukan browser)
                        cashInput.dispatchEvent(new Event('change'));
                        voiceText.innerText = `💰 Uang: ${formatRupiah(nominal)}`;
                    }
                } else {
                    voiceText.innerText = `❓ Nominal tidak jelas: "${moneyStr}"`;
                }
                return;
            }

            // E. Perintah TAMBAH BARANG
            // Hapus kata kerja umum
            let cleanCmd = cmd.replace(/tambah|masukkan|beli|pesan|tolong/g, '').trim();
            
            // Cek apakah ada angka (Qty) di awal atau tengah kalimat
            let qty = 1;
            
            // Split kalimat jadi kata-kata
            let words = cleanCmd.split(' ');
            let wordsWithoutNumbers = [];

            words.forEach(w => {
                // Cek apakah kata ini adalah angka (misal: "satu", "2", "dua")
                let val = parseIndonesianNumber(w, true); // true = strict mode (hanya angka murni)
                
                // Jika ini angka valid dan kecil (biasanya qty barang < 100)
                if (val > 0 && val < 100) {
                    qty = val;
                } else {
                    // Jika bukan angka (berarti nama barang), masukkan ke array nama
                    // Kecuali kata 'buah', 'pcs', 'porsi' kita buang aja
                    if(!['buah','pcs','porsi','biji'].includes(w)) {
                        wordsWithoutNumbers.push(w);
                    }
                }
            });

            let productKeyword = wordsWithoutNumbers.join(' ').trim();

            if (productKeyword.length >= 2) {
                let foundProduct = allProducts.find(p => p.name.toLowerCase().includes(productKeyword));
                
                if (foundProduct) {
                    addToCartQty(foundProduct.id, qty);
                    voiceText.innerText = `✅ +${qty} ${foundProduct.name}`;
                } else {
                    voiceText.innerText = `❌ Tidak ketemu: "${productKeyword}"`;
                }
            }
        }

        // --- HELPER: Konversi Kata ke Angka (Versi Lebih Cerdas) ---
        function parseIndonesianNumber(text, strict = false) {
            if(!text) return 0;
            text = text.toLowerCase().trim();

            // Peta Kata Dasar
            const map = {
                'nol': 0, 'kosong': 0,
                'satu': 1, 'se': 1, // 'se' bisa seratus, seribu
                'dua': 2, 'tiga': 3, 'empat': 4, 
                'lima': 5, 'enam': 6, 'tujuh': 7, 'delapan': 8, 'sembilan': 9, 
                'sepuluh': 10, 'sebelas': 11,
                'seratus': 100, 'seribu': 1000, 'juta': 1000000, 'setengah': 0.5 // Jaga-jaga
            };

            // Jika input angka langsung ("50000")
            // Hapus titik/koma dulu misal "50.000"
            let cleanNum = text.replace(/[.,]/g, ''); 
            if (!isNaN(cleanNum) && cleanNum !== '') {
                return parseInt(cleanNum);
            }

            // Jika strict mode (hanya cek kata per kata, misal cari qty)
            if (strict) {
                return map[text] || 0;
            }

            // Parsing Kalimat Nominal Kompleks ("Lima puluh ribu")
            let total = 0;
            let temp = 0;
            let words = text.split(' ');
            
            for(let w of words) {
                if (map[w] !== undefined) {
                    let val = map[w];
                    
                    if (w === 'se' || w === 'satu') {
                        temp = 1;
                    } 
                    else if (w === 'seratus') {
                        temp = 100;
                    } 
                    else if (w === 'seribu') {
                        // Jika sebelumnya ada angka (misal "dua seribu" -> salah, harusnya "dua ribu")
                        // Tapi kalau "seribu" di awal -> total += 1000
                        total += (temp || 1) * 1000; 
                        temp = 0;
                    } 
                    else if (w === 'juta') {
                        total += (temp || 1) * 1000000;
                        temp = 0;
                    } 
                    else {
                        temp += val;
                    }
                } 
                else if (w === 'belas') {
                    temp += 10;
                } 
                else if (w === 'puluh') {
                    temp *= 10;
                } 
                else if (w === 'ratus') {
                    temp *= 100;
                } 
                else if (w === 'ribu') {
                    total += (temp || 1) * 1000;
                    temp = 0;
                }
            }
            return total + temp;
        }

        // --- 3. KATEGORI & LIVE SEARCH ---
        
        // Load kategori dari server
        async function loadCategories() {
            try {
                const response = await fetch("{{ route('cashier.api.categories') }}");
                const data = await response.json();
                if (data.status === 'success') {
                    allCategories = data.categories;
                    renderCategoryButtons();
                }
            } catch (error) {
                console.error('Error loading categories:', error);
            }
        }

        // Render tombol kategori
        function renderCategoryButtons() {
            const categoryContainer = document.getElementById('category-buttons');
            categoryContainer.innerHTML = '';

            // Tombol "Semua"
            const sembuaBtn = document.createElement('button');
            sembuaBtn.className = 'px-5 py-2 bg-blue-600 text-white rounded-full text-sm font-semibold shadow-md whitespace-nowrap transition hover:bg-blue-700';
            sembuaBtn.textContent = 'Semua';
            sembuaBtn.onclick = () => filterByCategory('semua');
            categoryContainer.appendChild(sembuaBtn);

            // Tombol kategori lainnya
            allCategories.forEach(category => {
                const btn = document.createElement('button');
                btn.className = 'px-5 py-2 bg-white text-gray-600 border border-gray-100 hover:border-blue-500 hover:text-blue-600 rounded-full text-sm font-medium whitespace-nowrap transition';
                btn.textContent = category.charAt(0).toUpperCase() + category.slice(1); // Capitalize
                btn.onclick = () => filterByCategory(category);
                categoryContainer.appendChild(btn);
            });

            // Update tombol aktif
            updateCategoryButtonStyles();
        }

        // Update styling kategori yang aktif
        function updateCategoryButtonStyles() {
            const buttons = document.querySelectorAll('#category-buttons button');
            buttons.forEach(btn => {
                const btnCategory = btn.textContent.toLowerCase().trim();
                if (btnCategory === 'semua' && selectedCategory === 'semua') {
                    btn.className = 'px-5 py-2 bg-blue-600 text-white rounded-full text-sm font-semibold shadow-md whitespace-nowrap transition hover:bg-blue-700';
                } else if (btnCategory === selectedCategory) {
                    btn.className = 'px-5 py-2 bg-blue-600 text-white rounded-full text-sm font-semibold shadow-md whitespace-nowrap transition hover:bg-blue-700';
                } else {
                    btn.className = 'px-5 py-2 bg-white text-gray-600 border border-gray-100 hover:border-blue-500 hover:text-blue-600 rounded-full text-sm font-medium whitespace-nowrap transition';
                }
            });
        }

        // Filter produk berdasarkan kategori
        function filterByCategory(category) {
            selectedCategory = category;
            updateCategoryButtonStyles();
            performSearch(); // Update hasil pencarian
        }

        // Live search - dipanggil saat user mengetik atau mengganti kategori
        function performSearch() {
            const searchInput = document.getElementById('searchInput');
            searchQuery = searchInput ? searchInput.value.trim().toLowerCase() : '';

            // mulai dari seluruh data (yang sudah di-render pada page load)
            let filtered = allProducts.slice();

            // filter kategori jika dipilih
            if (selectedCategory && selectedCategory !== 'semua') {
                filtered = filtered.filter(p => {
                    let catField = (p.category || '').toString().toLowerCase();
                    let matchColumn = catField === selectedCategory.toLowerCase();
                    let matchRelation = false;
                    if (p.categories && Array.isArray(p.categories)) {
                        matchRelation = p.categories.some(c => c.name && c.name.toLowerCase() === selectedCategory.toLowerCase());
                    }
                    return matchColumn || matchRelation;
                });
            }

            // filter teks pencarian jika ada
            if (searchQuery) {
                filtered = filtered.filter(p => {
                    const name = (p.name || '').toString().toLowerCase();
                    const code = (p.code || '').toString().toLowerCase();
                    const cat = (p.category || '').toString().toLowerCase();
                    let catRel = '';
                    if (p.categories && Array.isArray(p.categories)) {
                        catRel = p.categories.map(c => c.name.toLowerCase()).join(' ');
                    }
                    return name.includes(searchQuery) || code.includes(searchQuery) || cat.includes(searchQuery) || catRel.includes(searchQuery);
                });
            }

            renderProducts(filtered);
        }

        // Render daftar produk di modal
        function renderProducts(products) {
            const productList = document.getElementById('product-list');
            productList.innerHTML = '';

            if (products.length === 0) {
                productList.innerHTML = `
                    <div class="col-span-full flex flex-col items-center justify-center py-12">
                        <i class="fa-solid fa-inbox text-4xl text-gray-300 mb-3"></i>
                        <p class="text-gray-400 text-sm">Tidak ada produk yang sesuai</p>
                    </div>`;
                return;
            }

            // helper untuk memilih warna tailwind dari nama kategori
        function jsCategoryColor(name) {
            const colors = ['red','yellow','green','blue','indigo','purple','pink','teal','orange','gray'];
            let hash = 0;
            for (let i = 0; i < name.length; i++) {
                hash = name.charCodeAt(i) + ((hash << 5) - hash);
            }
            const idx = Math.abs(hash) % colors.length;
            return colors[idx];
        }

        products.forEach(product => {
                const button = document.createElement('button');
                button.type = 'button';
                button.onclick = () => addToCart(product.id, product.name, product.price, product.code, product.image);
                button.className = 'bg-white p-2 rounded-lg shadow-sm hover:shadow-lg transition-all text-left group border border-transparent hover:border-blue-500 relative overflow-hidden flex flex-col h-full';
                
                const imgDisplay = product.image 
                    ? `<img src="${product.image}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">`
                    : `<i class="fa-solid fa-box-open text-3xl text-gray-300"></i>`;

                button.innerHTML = `
                    

                    <div class="h-20 w-full bg-gray-50 rounded-lg mb-2 flex items-center justify-center overflow-hidden relative">
                        ${imgDisplay}
                        <div class="absolute inset-0 bg-blue-600/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                            <div class="bg-blue-600 text-white w-8 h-8 rounded-full flex items-center justify-center shadow-lg scale-0 group-hover:scale-100 transition-transform">
                                <i class="fa-solid fa-plus"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex-1 flex flex-col justify-between">
                        <h3 class="font-bold text-gray-800 text-sm leading-tight line-clamp-2 mb-1 group-hover:text-blue-600 flex justify-between">
                            <span>${product.name}</span>
                            <span class="text-[10px] text-gray-500 pr-2">${product.stock} ${product.unit}</span>
                        </h3>
                        <div class="mb-1">
                            ${(product.categories || []).map(c => {
                                const col = jsCategoryColor(c.name || c);
                                return `<span class="inline-block px-1 py-0.5 rounded-full text-[8px] font-semibold bg-${col}-100 text-${col}-700 mr-1">${c.name || c}</span>`;
                            }).join('')}
                        </div>
                        <p class="font-bold text-blue-600 text-sm">Rp ${new Intl.NumberFormat('id-ID').format(product.price)}</p>
                    </div>
                `;

                productList.appendChild(button);
            });
        }

        // --- SETUP EVENT LISTENERS ---
        document.addEventListener('DOMContentLoaded', function() {
            // Load kategori saat halaman buka
            loadCategories();

            // Render produk awal
            renderProducts(allProducts);

            // Live search saat user mengetik
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.addEventListener('input', performSearch);
            }
        });
    </script>

</body>
</html>