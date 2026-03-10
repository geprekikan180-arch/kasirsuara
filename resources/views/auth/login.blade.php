<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Kasir Suara Pintar</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-br from-white to-blue-400 h-screen flex items-center justify-center">

    <div class="bg-white w-full max-w-md p-10 rounded-2xl shadow-2xl relative">
        
        <div class="flex justify-center gap-8 mb-10">
            <div class="flex flex-col items-center cursor-pointer">
                <span class="text-blue-700 font-bold text-lg">Masuk</span>
                <div class="h-1 w-12 bg-yellow-400 mt-1 rounded-full"></div> </div>
        </div>

        <form action="{{ route('login.process') }}" method="POST" class="space-y-6">
            @csrf

            @if ($errors->has('frozen_shop'))
                <div class="bg-red-50 border-2 border-red-500 text-red-800 px-4 py-4 rounded-lg relative mb-4 text-sm font-medium flex items-start gap-3">
                    <i class="fa-solid fa-lock text-lg mt-0.5 flex-shrink-0"></i>
                    <div>
                        <p class="font-bold">Akses Ditolak</p>
                        <p class="mt-1">{{ $errors->first('frozen_shop') }}</p>
                    </div>
                </div>
            @elseif ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <div>
                <input type="text" name="username" placeholder="Masukan Username" required
                    class="w-full px-6 py-3 rounded-full border border-gray-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 placeholder-gray-300 transition-all text-gray-700"
                    value="{{ old('username') }}">
            </div>

            <div class="relative">
                <input type="password" name="password" id="passwordInput" placeholder="Masukan Password" required
                    class="w-full px-6 py-3 rounded-full border border-gray-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 placeholder-gray-300 transition-all text-gray-700 pr-12">
                
                <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-5 flex items-center text-gray-500 hover:text-blue-600 focus:outline-none">
                    <i class="fa-solid fa-eye-slash" id="eyeIcon"></i>
                </button>
            </div>

            <div class="flex gap-4 pt-4">
                <a href="{{ url('/') }}" class="w-1/2 inline-flex items-center justify-center bg-red-600 hover:bg-red-700 text-white font-semibold py-3 rounded-xl transition-colors shadow-md">
                    Batal
                </a>

                <button type="submit" class="w-1/2 bg-blue-700 hover:bg-blue-800 text-white font-bold py-3 rounded-xl transition-colors shadow-md">
                    Masuk
                </button>
            </div>
        </form>

        <div class="mt-8 text-center text-xs text-gray-600 font-semibold">
            Belum memiliki akun? <a href="{{ route('register') }}" class="text-blue-600 hover:underline">Pendaftaran</a>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('passwordInput');
            const eyeIcon = document.getElementById('eyeIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            }
        }
    </script>
</body>
</html>