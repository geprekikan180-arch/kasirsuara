<div id="modeSelectionModal" class="fixed inset-0 bg-gray-900 bg-opacity-80 z-[100] hidden flex items-center justify-center backdrop-blur-sm">
    <div class="bg-white rounded-2xl p-8 max-w-2xl w-full text-center shadow-2xl transform transition-all scale-95 opacity-0 animate-fadeInModal" id="modalContent">
        <h2 class="text-3xl font-bold mb-2 text-gray-800">Pilih Mode Kerja</h2>
        <p class="text-gray-500 mb-8">Bagaimana Anda ingin mengoperasikan sistem hari ini?</p>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <button onclick="setSystemMode('voice')" class="p-8 border-2 border-purple-200 rounded-2xl hover:bg-purple-50 hover:border-purple-500 transition-all group flex flex-col items-center">
                <div class="bg-purple-100 p-4 rounded-full mb-4 group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-headset text-5xl text-purple-600"></i>
                </div>
                <h3 class="font-bold text-xl text-gray-800 mb-2">Full Suara (Kasur)</h3>
                <p class="text-sm text-gray-500">Kontrol sistem sepenuhnya dengan perintah suara tanpa menyentuh mouse/keyboard.</p>
            </button>
            
            <button onclick="setSystemMode('manual')" class="p-8 border-2 border-gray-200 rounded-2xl hover:bg-gray-50 hover:border-blue-500 transition-all group flex flex-col items-center">
                <div class="bg-gray-100 p-4 rounded-full mb-4 group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-computer-mouse text-5xl text-gray-600"></i>
                </div>
                <h3 class="font-bold text-xl text-gray-800 mb-2">Mode Manual</h3>
                <p class="text-sm text-gray-500">Operasikan sistem secara tradisional menggunakan sentuhan, mouse, dan keyboard.</p>
            </button>
        </div>
    </div>
</div>

<style>
    @keyframes fadeInModal {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }
    .animate-fadeInModal { animation: fadeInModal 0.4s ease-out forwards; }
</style>

<div id="kasurIndicator" class="fixed bottom-6 right-6 z-50 hidden transition-all duration-300 transform translate-y-10 opacity-0">
    <div class="bg-gray-900 text-white px-5 py-3 rounded-2xl shadow-2xl flex items-center gap-4 border border-gray-700">
        <div class="relative flex h-4 w-4">
          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
          <span class="relative inline-flex rounded-full h-4 w-4 bg-blue-500"></span>
        </div>
        <span id="kasurText" class="font-semibold tracking-wide">Kasur siap mendengar...</span>
    </div>
</div>

<script>
    let kasurRecognition;
    let kasurState = 'IDLE'; 
    // Ambil ingatan dari halaman sebelumnya, jika tidak ada, set ke GLOBAL
    let kasurContext = sessionStorage.getItem('kasurContext') || 'GLOBAL'; 

    document.addEventListener('DOMContentLoaded', () => {
        const currentMode = localStorage.getItem('appMode');
        if (!currentMode) {
            document.getElementById('modeSelectionModal').classList.remove('hidden');
        } else if (currentMode === 'voice') {
            initKasur();
            cekIngatanKasur(); // <-- Fungsi baru untuk mengecek ingatan setelah pindah halaman
        }
    });

    function setSystemMode(mode) {
        localStorage.setItem('appMode', mode);
        document.getElementById('modeSelectionModal').classList.add('hidden');
        if (mode === 'voice') {
            initKasur();
            cekIngatanKasur();
        }
    }

    function cekIngatanKasur() {
        const textUi = document.getElementById('kasurText');
        
        // Jika Kasur ingat dia sedang disuruh nambah karyawan...
        if (kasurContext === 'ADD_EMPLOYEE') {
            const modal = document.getElementById('addModal');
            // Pastikan dia sudah berada di halaman yang ada modal-nya
            if (modal) {
                modal.classList.remove('hidden');
                bangunkanKasur();
                textUi.innerText = "Lanjut bos. Sebutkan nama, username, dan role...";
            } else {
                // Jika tidak ada modal (berarti belum di halaman karyawan), pindahkan otomatis!
                textUi.innerText = "Menuju halaman karyawan...";
                window.location.href = '/toko/karyawan'; // Sesuaikan dengan URL route karyawanmu!
            }
        }
    }

    function initKasur() {
        if (!('webkitSpeechRecognition' in window)) return;
        kasurRecognition = new webkitSpeechRecognition();
        kasurRecognition.lang = 'id-ID';
        kasurRecognition.continuous = true; 
        kasurRecognition.interimResults = false;

        kasurRecognition.onstart = function() {
            document.getElementById('kasurIndicator').classList.remove('hidden', 'translate-y-10', 'opacity-0');
            if(kasurState === 'IDLE') document.getElementById('kasurText').innerText = "Mode Full Suara Aktif...";
        };

        kasurRecognition.onresult = function(event) {
            const lastIndex = event.results.length - 1;
            const transcript = event.results[lastIndex][0].transcript.toLowerCase().trim();
            let cleanText = transcript.replace(/[.,\/#!$%\^&\*;:{}=\-_`~()]/g,"").trim();
            
            console.log("Status:", kasurContext, "| Terdengar:", cleanText);

            if (kasurState === 'IDLE') {
                if (cleanText.includes('kasur')) {
                    bangunkanKasur();
                    let commandAfterKasur = cleanText.split('kasur')[1].trim();
                    if (commandAfterKasur.length > 0) prosesPerintah(commandAfterKasur);
                }
            } else if (kasurState === 'LISTENING') {
                prosesPerintah(cleanText);
            }
        };

        kasurRecognition.onend = function() {
            if (localStorage.getItem('appMode') === 'voice') {
                setTimeout(() => { try { kasurRecognition.start(); } catch(e) {} }, 100);
            }
        };

        kasurRecognition.start();
    }

    function bangunkanKasur() {
        kasurState = 'LISTENING';
        if (kasurContext === 'GLOBAL') {
            document.getElementById('kasurText').innerText = "Ya Bos? Kasur siap...";
        }
        resetTimeoutKasur(8000); 
    }

    function tidurkanKasur() {
        kasurState = 'IDLE';
        ubahKonteks('GLOBAL'); // Reset ingatan
        document.getElementById('kasurText').innerText = "Mode Full Suara Aktif. Ucapkan 'Kasur'...";
    }

    function ubahKonteks(konteksBaru) {
        kasurContext = konteksBaru;
        sessionStorage.setItem('kasurContext', konteksBaru); // Simpan ke ingatan browser
    }

    function resetTimeoutKasur(waktu) {
        clearTimeout(window.kasurTimeout);
        window.kasurTimeout = setTimeout(tidurkanKasur, waktu);
    }

    // ==========================================
    // OTAK UTAMA: MESIN STATUS & ROUTING
    // ==========================================
    function prosesPerintah(cmd) {
        const textUi = document.getElementById('kasurText');
        
        // 1. STATUS: GLOBAL (Menu Utama)
        if (kasurContext === 'GLOBAL') {
            if (cmd === 'batal' || cmd === 'diam' || cmd.includes('matikan suara')) {
                tidurkanKasur();
            }
            else if (cmd.includes('tambah karyawan')) {
                ubahKonteks('ADD_EMPLOYEE'); // Catat ingatan!
                
                const modal = document.getElementById('addModal');
                if(modal) {
                    // Jika sedang di halaman karyawan, langsung buka
                    modal.classList.remove('hidden');
                    textUi.innerText = "Membuka form. Sebutkan nama, username, dan role...";
                    resetTimeoutKasur(30000);
                } else {
                    // Jika di halaman lain (misal Dashboard), redirect otomatis!
                    textUi.innerText = "Membuka halaman karyawan...";
                    window.location.href = '/toko/karyawan'; // <-- Ganti jika URL route-mu berbeda
                }
            } 
            else if (cmd.includes('ganti mode')) {
                localStorage.removeItem('appMode');
                location.reload();
            }
            else {
                textUi.innerText = "Perintah tidak dikenali...";
                resetTimeoutKasur(3000);
            }
        }
        
        // 2. STATUS: MENGISI FORM KARYAWAN
        else if (kasurContext === 'ADD_EMPLOYEE') {
            resetTimeoutKasur(30000); 

            if (cmd.includes('batal') || cmd.includes('tutup')) {
                textUi.innerText = "Dibatalkan. Form ditutup.";
                document.getElementById('addModal').classList.add('hidden');
                tidurkanKasur();
                return;
            }

            if (cmd.includes('simpan')) {
                textUi.innerText = "Apakah data sudah benar? Ucapkan 'Ya' atau 'Batal'.";
                ubahKonteks('CONFIRM_ADD'); 
                return;
            }

            // Normalisasi Alias (Biar pintar seperti fitur lamamu)
            cmd = cmd.replace(/\b(user)\b/g, 'username');
            cmd = cmd.replace(/\b(jabatan|posisi)\b/g, 'role');

            // Regex Pintar
            let namaMatch = cmd.match(/nama\s+(.*?)(?=\s+username|\s+role|\s+simpan|$)/);
            let userMatch = cmd.match(/username\s+(.*?)(?=\s+nama|\s+role|\s+simpan|$)/);
            let roleMatch = cmd.match(/role\s+(.*?)(?=\s+nama|\s+username|\s+simpan|$)/);

            if (namaMatch) {
                let formattedName = namaMatch[1].split(' ').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ');
                document.getElementById('addName').value = formattedName;
            }
            if (userMatch) {
                document.getElementById('addUsername').value = userMatch[1].replace(/\s+/g, '');
            }
            if (roleMatch) {
                let role = roleMatch[1];
                if (role.includes('gudang') || role.includes('inventori')) {
                    document.getElementById('addRoleInventory').checked = true;
                } else {
                    document.getElementById('addRoleCashier').checked = true;
                }
            }

            textUi.innerText = "Mendengarkan data...";
        }

        // 3. STATUS: KONFIRMASI SIMPAN
        else if (kasurContext === 'CONFIRM_ADD') {
            if (cmd === 'ya' || cmd === 'yakin') {
                textUi.innerText = "Menyimpan data karyawan...";
                ubahKonteks('GLOBAL'); // Hapus ingatan form
                
                const form = document.getElementById('formAddEmployee');
                if(form) form.submit();
            } 
            else if (cmd === 'tidak' || cmd === 'batal') {
                textUi.innerText = "Penyimpanan dibatalkan. Masih di dalam form.";
                ubahKonteks('ADD_EMPLOYEE'); 
                resetTimeoutKasur(30000);
            }
            else {
                textUi.innerText = "Tolong jawab 'Ya' atau 'Batal'.";
                resetTimeoutKasur(10000);
            }
        }
    }
</script>