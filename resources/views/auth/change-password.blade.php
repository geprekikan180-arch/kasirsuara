<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ganti Password - Kasir Pintar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Poppins', sans-serif; } </style>
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center">

    <div class="bg-white w-full max-w-md p-8 rounded-2xl shadow-xl border border-gray-200">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-yellow-100 text-yellow-600 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                <i class="fa-solid fa-lock"></i> 🔒
            </div>
            <h2 class="text-2xl font-bold text-gray-800">Keamanan Akun</h2>
            <p class="text-gray-500 text-sm mt-2">
                Halo, <span class="font-bold text-blue-600">{{ Auth::user()->name }}</span>. 
                <br>Karena ini login pertamamu, kamu wajib mengganti password default demi keamanan.
            </p>
        </div>

        <form action="{{ route('password.update') }}" method="POST" class="space-y-5">
            @csrf

            @if ($errors->any())
                <div class="bg-red-50 text-red-600 p-3 rounded-lg text-sm border border-red-200">
                    {{ $errors->first() }}
                </div>
            @endif

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Password Baru</label>
                <input type="password" name="password" required 
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none" 
                    placeholder="Minimal 6 karakter">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" required 
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-blue-500 outline-none" 
                    placeholder="Ketik ulang password baru">
            </div>

            <button type="submit" class="w-full bg-blue-700 hover:bg-blue-800 text-white font-bold py-3 rounded-xl shadow-lg shadow-blue-700/30 transition-all mt-4">
                Simpan & Masuk Dashboard
            </button>
        </form>
    </div>

</body>
</html>