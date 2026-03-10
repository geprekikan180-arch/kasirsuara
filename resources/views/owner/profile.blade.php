<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil Toko</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Poppins', sans-serif; } </style>
</head>
<body class="bg-gray-50">

    <div class="flex min-h-screen">
        @include('components.sidebar')

        <main class="flex-1 ml-0 md:ml-64 p-8">
            
            <div class="max-w-2xl mx-auto">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl w-full font-bold text-gray-800">Pengaturan Toko</h1>
                    <div>
                        <form action="{{ route('logout') }}" method="POST">
                        @csrf
                            <button type="submit" onclick="localStorage.removeItem('appMode')" class="flex item-center text-right gap-3 px-6 py-3 text-red-400 hover:text-red-600 w-full font-medium transition-colors">
                                <i class="fa-solid fa-sign-out-alt"></i> Keluar
                            </button>
                        </form>
                    </div>
                </div>


                @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    {{ session('success') }}
                </div>
                @endif

                <form action="{{ route('owner.profile.update') }}" method="POST" enctype="multipart/form-data" class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                    @csrf
                    @method('PUT')

                    <div class="mb-8 text-center">
                        <div class="relative inline-block">
                            <img id="preview-img" 
                            src="{{ $shop->logo ? asset('storage/shops/'.$shop->logo) . '?v=' . time() : 'https://ui-avatars.com/api/?name='.urlencode($shop->name).'&background=0D8ABC&color=fff' }}" 
                            class="w-32 h-32 rounded-full object-cover border-4 border-blue-50 shadow-lg">
                            
                            <label for="logo-upload" class="absolute bottom-0 right-0 bg-blue-600 hover:bg-blue-700 text-white p-2 rounded-full cursor-pointer transition shadow-md">
                                <i class="fas fa-camera text-sm"></i>
                            </label>
                            <input type="file" id="logo-upload" name="logo" class="hidden" accept="image/*" onchange="previewImage(event)">
                        </div>
                        <p class="text-sm text-gray-400 mt-2">Klik ikon kamera untuk mengganti logo.</p>
                        <p class="text-sm text-red-400 mt-1">(Ukuran logo maksimal 1MB!)</p>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Nama Toko</label>
                        <input type="text" name="shop_name" value="{{ old('shop_name', $shop->name) }}" 
                               class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>

                    <hr class="my-6 border-gray-100">

                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Password Baru <span class="text-gray-400 font-normal text-xs">(Opsional)</span></label>
                        <input type="password" name="password" placeholder="Isi hanya jika ingin mengganti password"
                               class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>

                    <div class="mb-8">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" placeholder="Ulangi password baru"
                               class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-xl transition duration-300 shadow-lg shadow-blue-200">
                        Simpan Perubahan
                    </button>

                </form>
            </div>

        </main>
    </div>

    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function(){
                const output = document.getElementById('preview-img');
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</body>
</html>