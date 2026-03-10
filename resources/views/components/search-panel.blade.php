<header class="flex justify-between items-center mb-10 bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
    <div class="relative flex-1 max-w-md">
        @php
            $user = Auth::user();
            $role = $user->role;
            
            // Tentukan route search berdasarkan role
            $searchRoute = match($role) {
                'owner' => route('owner.search'),
                'cashier' => route('cashier.search'),
                'inventory' => route('inventory.search'),
                'superadmin' => route('superadmin.search'),
                default => '#'
            };
            
            $searchPlaceholder = match($role) {
                'owner' => 'Cari barang, karyawan, laporan, transaksi...',
                'cashier' => 'Cari produk, transaksi...',
                'inventory' => 'Cari barang di gudang...',
                'superadmin' => 'Cari toko, pengguna...',
                default => 'Cari...'
            };
        @endphp
        
        <form id="globalSearchForm" method="GET" action="{{ $searchRoute }}" class="flex items-center relative w-full">
            <input type="text" 
                   id="globalSearchInput"
                   name="search"
                   placeholder="{{ $searchPlaceholder }}" 
                   value="{{ request()->get('search', '') }}"
                   class="w-full pl-4 pr-16 py-3 rounded-full border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all shadow-inner text-sm">
            
            <button type="submit" class="absolute right-10 text-gray-400 hover:text-blue-600 transition-colors px-1" title="Cari">
                <i class="fa-solid fa-search"></i>
            </button>

            <button type="button" id="btnSearchMic" onclick="startGlobalVoiceSearch()" class="absolute right-3 text-gray-400 hover:text-purple-600 transition-colors px-1 border-l border-gray-200 pl-2" title="Pencarian Suara">
                <i class="fa-solid fa-microphone"></i>
            </button>
        </form>
    </div>
    
    <div class="flex items-center text-gray-500 ml-10">
        <form action="{{ route('logout') }}" method="POST" data-confirm="Yakin ingin logout?">
    @csrf
    <button type="submit" onclick="localStorage.removeItem('appMode')" class="flex items-center gap-3 px-6 py-3 text-red-400 hover:text-red-600 w-full font-medium transition-colors">
        <i class="fa-solid fa-sign-out-alt"></i> Keluar
    </button>
    </form>
    </div>
    
</header>

<script>
    function startGlobalVoiceSearch() {
        const micBtn = document.getElementById('btnSearchMic');
        const searchInput = document.getElementById('globalSearchInput');
        const searchForm = document.getElementById('globalSearchForm');

        if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            const recognition = new SpeechRecognition();
            
            recognition.lang = 'id-ID'; 
            recognition.interimResults = false;
            recognition.maxAlternatives = 1;

            recognition.onstart = function() {
                micBtn.innerHTML = '<i class="fa-solid fa-microphone text-red-500 animate-pulse"></i>';
                searchInput.placeholder = 'Katakan "Cari kode 1"...';
            };

            recognition.onresult = function(event) {
                const transcript = event.results[0][0].transcript;
                
                // 1. Bersihkan tanda baca dan jadikan huruf kecil
                let cleanText = transcript.replace(/[.,\/#!$%\^&\*;:{}=\-_`~()]/g,"").trim().toLowerCase();
                
                // 2. LOGIKA KECERDASAN BUATAN (Ekstrak Kata Kunci)
                let keyword = cleanText;

                // Skenario A: "Cari barang kode 1", "Cari kode 123"
                if (cleanText.includes('kode')) {
                    // Ambil semua kata SETELAH kata "kode"
                    keyword = cleanText.replace(/.*kode\s+/g, '').trim();
                }
                // Skenario B: "Cari karyawan bernama Budi", "Cari user admin"
                else if (cleanText.includes('karyawan') || cleanText.includes('user')) {
                    // Ambil kata SETELAH karyawan/user
                    keyword = cleanText.replace(/.*(karyawan bernama|karyawan|user bernama|user)\s+/g, '').trim();
                }
                // Skenario C: "Cari barang bernama sapi", "Cari produk indomie"
                else if (cleanText.includes('barang') || cleanText.includes('produk')) {
                    // Ambil kata SETELAH barang/produk
                    keyword = cleanText.replace(/.*(barang bernama|barang|produk bernama|produk)\s+/g, '').trim();
                }
                // Skenario D: "Cari transaksi 15", "Cari struk nomor 10"
                else if (cleanText.includes('transaksi') || cleanText.includes('struk')) {
                    keyword = cleanText.replace(/.*(transaksi nomor|transaksi|struk nomor|struk)\s+/g, '').trim();
                }
                // Skenario E (Fallback): Hanya bilang "Cari sapi"
                else if (cleanText.startsWith('cari ')) {
                    keyword = cleanText.replace('cari ', '').trim();
                }

                // Opsional: Jika Google Speech menangkap kata "satu" jadi huruf, kita ubah ke angka 1
                const numberMap = {'satu': '1', 'dua': '2', 'tiga': '3', 'empat': '4', 'lima': '5'};
                if (numberMap[keyword]) {
                    keyword = numberMap[keyword];
                }

                // 3. Masukkan KATA KUNCI BERSIH ke dalam input
                searchInput.value = keyword;
                
                // 4. Ubah placeholder sesaat biar user tau sistem ngerti
                searchInput.placeholder = `Mencari: ${keyword}...`;
                
                // 5. Submit form
                setTimeout(() => {
                    searchForm.submit();
                }, 500); // Delay setengah detik biar user sempat lihat tulisannya
            };

            recognition.onend = function() {
                micBtn.innerHTML = '<i class="fa-solid fa-microphone"></i>';
            };

            recognition.onerror = function(event) {
                console.error("Voice Search Error:", event.error);
                if(event.error === 'not-allowed') {
                    alert('Izinkan akses mikrofon di browser Anda!');
                }
                micBtn.innerHTML = '<i class="fa-solid fa-microphone"></i>';
                searchInput.placeholder = '{{ $searchPlaceholder }}';
            };

            // pasikan ada listener konfirmasi untuk form (global)
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('form[data-confirm]').forEach(f => {
                    f.addEventListener('submit', function(e) {
                        if (!confirm(f.dataset.confirm)) {
                            e.preventDefault();
                        }
                    });
                });
                document.querySelectorAll('a[data-confirm]').forEach(a => {
                    a.addEventListener('click', function(e) {
                        if (!confirm(a.dataset.confirm)) {
                            e.preventDefault();
                        }
                    });
                });
            });

            recognition.start();
        } else {
            alert('Browser Anda tidak mendukung fitur pencarian suara.');
        }
    }
</script>