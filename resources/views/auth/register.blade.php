<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Mitra - Kasir Suara Pintar</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style> body { font-family: 'Poppins', sans-serif; } </style>
</head>
<body class="bg-gradient-to-br from-white to-blue-400 h-screen flex items-center justify-center">

    <div class="bg-white w-full max-w-lg p-8 rounded-2xl shadow-2xl relative mx-4">
        
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-blue-700">Gabung Jadi Mitra</h2>
            <p class="text-gray-400 text-sm mt-1">Daftarkan tokomu dan kelola dengan mudah.</p>
        </div>

        <form action="{{ route('register.process') }}" method="POST" class="space-y-5">
            @csrf

            @if ($errors->any())
                <div class="bg-red-50 text-red-600 p-3 rounded-lg text-sm border border-red-200">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="space-y-4 border-b border-gray-100 pb-5">
                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide">Data Toko</h3>
                <input type="text" name="shop_name" placeholder="Nama Toko" required value="{{ old('shop_name') }}"
                    class="w-full px-5 py-3 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all">
                
                <textarea name="shop_address" placeholder="Alamat Lengkap Toko" rows="2" required
                    class="w-full px-5 py-3 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all">{{ old('shop_address') }}</textarea>
            </div>

            <div class="space-y-4 pt-2">
                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide">Akun Pemilik (Owner)</h3>
                
                <input type="text" name="owner_name" placeholder="Nama Lengkap Pemilik" required value="{{ old('owner_name') }}"
                    class="w-full px-5 py-3 rounded-xl border border-gray-300 focus:border-blue-500 outline-none">

                <div class="grid grid-cols-2 gap-4">
                    <input type="text" name="username" placeholder="Username" required value="{{ old('username') }}"
                        class="w-full px-5 py-3 rounded-xl border border-gray-300 focus:border-blue-500 outline-none">
                    
                    <input type="password" name="password" placeholder="Password" required
                        class="w-full px-5 py-3 rounded-xl border border-gray-300 focus:border-blue-500 outline-none">
                </div>
            </div>

            <button type="submit" class="w-full bg-blue-700 hover:bg-blue-800 text-white font-bold py-3.5 rounded-xl transition-all shadow-lg shadow-blue-700/30 mt-4">
                Daftar Sekarang
            </button>
        </form>

        <div class="mt-6 text-center text-xs text-gray-600 font-semibold">
            Sudah punya akun? <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Masuk disini</a>
        </div>
    </div>

</body>
</html>