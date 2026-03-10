<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Karyawan - Kasir Pintar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Poppins', sans-serif; } </style>
</head>
<body class="bg-gray-50 font-poppins">
    <div class="flex min-h-screen">
        @include('components.sidebar')

        <main class="flex-1 ml-0 md:ml-64 p-8 relative">
            
            @include('components.search-panel')
            
            <div class="flex justify-end items-center gap-3 mb-8">
                <button id="btnVoiceAdd" onclick="startVoiceAdd()" class="bg-purple-600 hover:bg-purple-700 text-white px-5 py-3 rounded-xl font-bold shadow-lg shadow-purple-600/20 flex items-center gap-2 transition-all group">
                    <i class="fa-solid fa-microphone group-hover:scale-110 transition-transform"></i> 
                </button>

                <button onclick="document.getElementById('addModal').classList.remove('hidden')" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded-xl font-bold shadow-lg shadow-blue-600/20 flex items-center gap-2 transition-all">
                    <i class="fa-solid fa-plus"></i>
                </button>
            </div>

            <div id="voiceToast" class="hidden fixed bottom-10 left-1/2 transform -translate-x-1/2 bg-gray-900 text-white px-6 py-4 rounded-xl shadow-2xl z-50 flex items-center gap-4 transition-all">
                <div class="w-8 h-8 rounded-full bg-purple-500 flex items-center justify-center animate-pulse">
                    <i class="fa-solid fa-microphone text-white"></i>
                </div>
                <div>
                    <p class="text-xs text-purple-300 font-bold uppercase tracking-wider mb-1">Mendengarkan...</p>
                    <p id="voiceToastText" class="text-sm font-medium">Sebutkan nama dan posisi...</p>
                </div>
                <button onclick="stopVoiceAdd()" class="ml-4 text-gray-400 hover:text-white">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-6 flex items-center gap-2">
                    <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-blue-600 text-white px-6 py-4 flex justify-between items-center">
                    <h2 class="text-xl font-bold">Daftar Karyawan</h2>
                </div>
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-semibold tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Nama Lengkap</th>
                            <th class="px-6 py-4">Username</th>
                            <th class="px-6 py-4">Peran (Role)</th>
                            <th class="px-6 py-4">Status Akun</th>
                            <th class="px-6 py-4">Status Karyawan</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($employees as $emp)
                        <tr class="hover:bg-blue-50/30 transition-colors">
                            <td class="px-6 py-4 font-bold text-gray-800">{{ $emp->name }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $emp->username }}</td>
                            <td class="px-6 py-4">
                                @if($emp->role == 'cashier')
                                    <span class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-xs font-bold">Kasir</span>
                                @else
                                    <span class="bg-orange-100 text-orange-700 px-3 py-1 rounded-full text-xs font-bold">Gudang</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($emp->must_change_password)
                                    <span class="text-xs text-yellow-600 bg-yellow-100 px-2 py-1 rounded border border-yellow-200">
                                        <i class="fa-solid fa-lock"></i> Belum Ganti Pass
                                    </span>
                                @else
                                    <span class="text-xs text-green-600 bg-green-100 px-2 py-1 rounded border border-green-200">
                                        <i class="fa-solid fa-check"></i> Aktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($emp->is_frozen)
                                    <span class="text-xs text-red-600 bg-red-100 px-2 py-1 rounded border border-red-200">
                                        <i class="fa-solid fa-ban"></i> Dibekukan
                                    </span>
                                @else
                                    <span class="text-xs text-green-600 bg-green-100 px-2 py-1 rounded border border-green-200">
                                        <i class="fa-solid fa-check"></i> Aktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button onclick="openEditModal({{ $emp->id }}, '{{ $emp->name }}', '{{ $emp->username }}', '{{ $emp->role }}', {{ $emp->is_frozen ? 'true' : 'false' }})" 
                                    class=" text-yellow-400 hover:text-yellow-600 transition-colors mr-4" title="Edit">
                                    <i class="fa-solid fa-edit text-lg"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                <i class="fa-solid fa-user-slash text-4xl mb-3 block opacity-20"></i>
                                Belum ada karyawan. Tambahkan sekarang!
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-8">
                {{ $employees->links() }}
            </div>
        </main>
    </div>

    <div id="addModal" class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 transition-all">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl p-6 transform scale-100">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-800">Tambah Karyawan Baru</h3>
                <button onclick="document.getElementById('addModal').classList.add('hidden')" class="text-gray-400 hover:text-red-500">
                    <i class="fa-solid fa-times text-xl"></i>
                </button>
            </div>

            <form action="{{ route('employees.store') }}" method="POST" id="formAddEmployee">                
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" id="addName" name="name" required class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-blue-500 outline-none" placeholder="Contoh: Siti Aminah">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Username Login</label>
                        <input type="text" id="addUsername" name="username" required class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-blue-500 outline-none" placeholder="Otomatis dari nama (tanpa spasi)">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Posisi / Role</label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="cursor-pointer">
                                <input type="radio" id="addRoleCashier" name="role" value="cashier" class="peer sr-only" checked>
                                <div class="p-3 rounded-xl border border-gray-300 peer-checked:border-purple-500 peer-checked:bg-purple-50 peer-checked:text-purple-700 text-center transition-all">
                                    <i class="fa-solid fa-cash-register mb-1 block"></i> Kasir
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" id="addRoleInventory" name="role" value="inventory" class="peer sr-only">
                                <div class="p-3 rounded-xl border border-gray-300 peer-checked:border-orange-500 peer-checked:bg-orange-50 peer-checked:text-orange-700 text-center transition-all">
                                    <i class="fa-solid fa-boxes-stacked mb-1 block"></i> Gudang
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="bg-yellow-50 text-yellow-800 text-xs p-3 rounded-lg border border-yellow-200 mt-2">
                        <i class="fa-solid fa-info-circle mr-1"></i> Password bawaan adalah <b>123456</b>. Karyawan dapat menggantinya nanti.
                    </div>
                </div>

                <div class="mt-8">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl shadow-lg shadow-blue-600/30 transition-all">
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="editModal" class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 transition-all">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl p-6 transform scale-100">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-800">Edit Karyawan</h3>
                <button onclick="document.getElementById('editModal').classList.add('hidden')" class="text-gray-400 hover:text-red-500">
                    <i class="fa-solid fa-times text-xl"></i>
                </button>
            </div>

            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" id="editName" name="name" required class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-blue-500 outline-none">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Username Login</label>
                        <input type="text" id="editUsername" name="username" required class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-blue-500 outline-none" disabled>
                        <p class="text-[10px] text-gray-400 mt-1">Username tidak dapat diubah setelah dibuat.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Posisi / Role</label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="cursor-pointer">
                                <input type="radio" id="editRoleCashier" name="role" value="cashier" class="peer sr-only">
                                <div class="p-3 rounded-xl border border-gray-300 peer-checked:border-purple-500 peer-checked:bg-purple-50 peer-checked:text-purple-700 text-center transition-all">
                                    <i class="fa-solid fa-cash-register mb-1 block"></i> Kasir
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" id="editRoleInventory" name="role" value="inventory" class="peer sr-only">
                                <div class="p-3 rounded-xl border border-gray-300 peer-checked:border-orange-500 peer-checked:bg-orange-50 peer-checked:text-orange-700 text-center transition-all">
                                    <i class="fa-solid fa-boxes-stacked mb-1 block"></i> Gudang
                                </div>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Status Karyawan</label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="cursor-pointer">
                                <input type="radio" id="editStatusActive" name="is_frozen" value="0" class="peer sr-only">
                                <div class="p-3 rounded-xl border border-gray-300 peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:text-green-700 text-center transition-all">
                                    <i class="fa-solid fa-check-circle mb-1 block"></i> Aktif
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" id="editStatusFrozen" name="is_frozen" value="1" class="peer sr-only">
                                <div class="p-3 rounded-xl border border-gray-300 peer-checked:border-red-500 peer-checked:bg-red-50 peer-checked:text-red-700 text-center transition-all">
                                    <i class="fa-solid fa-ban mb-1 block"></i> Bekukan
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex gap-3">
                    <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3 rounded-xl transition-all border border-gray-200">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl shadow-lg shadow-blue-600/30 transition-all">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // --- Fungsi Buka Modal Edit ---
        function openEditModal(id, name, username, role, isFrozen) {
            document.getElementById('editName').value = name;
            document.getElementById('editUsername').value = username;
            
            if (role === 'cashier') {
                document.getElementById('editRoleCashier').checked = true;
            } else {
                document.getElementById('editRoleInventory').checked = true;
            }
            
            if (isFrozen) {
                document.getElementById('editStatusFrozen').checked = true;
            } else {
                document.getElementById('editStatusActive').checked = true;
            }
            
            const form = document.getElementById('editForm');
            form.action = `/toko/karyawan/${id}`;
            
            document.getElementById('editModal').classList.remove('hidden');
        }

        // --- FITUR VOICE COMMAND UNTUK TAMBAH KARYAWAN ---
        let recognition;
        let isVoiceActive = false;
        const voiceToast = document.getElementById('voiceToast');
        const voiceToastText = document.getElementById('voiceToastText');

        if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            recognition = new SpeechRecognition();
            recognition.lang = 'id-ID';
            recognition.interimResults = false;
            recognition.maxAlternatives = 1;

            recognition.onstart = function() {
                isVoiceActive = true;
                voiceToast.classList.remove('hidden');
                voiceToastText.innerText = 'Sebutkan nama dan posisi...';
            };

            recognition.onend = function() {
                isVoiceActive = false;
                voiceToast.classList.add('hidden');
            };

            recognition.onresult = function(event) {
                const transcript = event.results[0][0].transcript.toLowerCase();
                voiceToastText.innerText = `"${transcript}"`;
                
                // Proses suara setelah delay sedikit biar user bisa baca teksnya
                setTimeout(() => {
                    processVoiceAdd(transcript);
                }, 1000);
            };

            recognition.onerror = function(event) {
                console.log('Voice Error:', event.error);
                if(event.error === 'not-allowed') {
                    alert('Izinkan akses mikrofon di browser Anda!');
                }
                stopVoiceAdd();
            };
        } else {
            document.getElementById('btnVoiceAdd').style.display = 'none';
        }

        function startVoiceAdd() {
            if (recognition && !isVoiceActive) {
                recognition.start();
            }
        }

        function stopVoiceAdd() {
            if (recognition && isVoiceActive) {
                recognition.stop();
            }
        }

        // --- OTAK PEMROSESAN SUARA (Parsing Spesifik Nama, User, Role) ---
        function processVoiceAdd(cmd) {
            // Hilangkan tanda baca
            cmd = cmd.replace(/[.,\/#!$%\^&\*;:{}=\-_`~()]/g,"").trim().toLowerCase();

            // 1. Normalisasi Alias/Kata Ganti
            // Ubah kata "username" menjadi "user", dan "jabatan/posisi" menjadi "role"
            // agar mesin lebih mudah mendeteksinya.
            cmd = cmd.replace(/\b(username)\b/g, 'user');
            cmd = cmd.replace(/\b(jabatan|posisi)\b/g, 'role');

            // 2. Ekstrak Data menggunakan Pola Cerdas (Regex)
            // Ini akan mengambil teks SETELAH kata kunci, dan BERHENTI jika bertemu kata kunci lain.
            let nameMatch = cmd.match(/nama\s+(.*?)(?=\s+user|\s+role|$)/);
            let userMatch = cmd.match(/user\s+(.*?)(?=\s+nama|\s+role|$)/);
            let roleMatch = cmd.match(/role\s+(.*?)(?=\s+nama|\s+user|$)/);

            let extractedName = nameMatch ? nameMatch[1].trim() : '';
            let extractedUser = userMatch ? userMatch[1].trim() : '';
            let extractedRole = roleMatch ? roleMatch[1].trim() : '';

            // Jika sama sekali tidak ada keyword yang diucapkan
            if (!extractedName && !extractedUser && !extractedRole) {
                alert("Format tidak dikenali. Coba: 'Nama Budi user budi01 jabatan kasir'");
                stopVoiceAdd();
                return;
            }

            // 3. Masukkan ke Form HTML
            
            // Format Nama (Title Case: huruf besar di awal kata)
            if (extractedName) {
                let formattedName = extractedName.split(' ').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ');
                document.getElementById('addName').value = formattedName;
            }

            // Format Username (Hapus spasi)
            if (extractedUser) {
                let formattedUser = extractedUser.replace(/\s+/g, '');
                document.getElementById('addUsername').value = formattedUser;
            }

            // Format Role
            if (extractedRole) {
                if (extractedRole.includes('gudang') || extractedRole.includes('inventori')) {
                    document.getElementById('addRoleInventory').checked = true;
                } else {
                    // Default / jika terdengar 'kasir'
                    document.getElementById('addRoleCashier').checked = true;
                }
            }

            // 4. Matikan Mic dan Buka Modal (User tinggal klik simpan/edit jika salah dengar)
            stopVoiceAdd();
            document.getElementById('addModal').classList.remove('hidden');
        }    
    </script>
</body>
</html>