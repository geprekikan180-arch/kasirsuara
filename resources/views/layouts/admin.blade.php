<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - Kasir Suara</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">

    <div class="flex h-screen overflow-hidden">
        <aside class="w-64 bg-white border-r border-gray-200 hidden md:flex flex-col justify-between">
            <div>
                <div class="h-20 flex items-center justify-center px-8 border-b border-gray-100">
                    <h1 class="text-2xl font-bold text-blue-700 tracking-tighter">Kasur<span class="text-gray-800">Pintar.</span></h1>
                </div>

                <nav class="mt-8 px-4 space-y-2">
                    <a href="{{ route('superadmin.dashboard') }}" 
                    class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-colors
                        {{ request()->routeIs('superadmin.dashboard') ? 'bg-blue-50 text-blue-700' : 'text-gray-500 hover:bg-gray-50 hover:text-blue-600' }}">
                        <i class="fa-solid fa-chart-pie w-5"></i> Dashboard
                    </a>

                    <a href="{{ route('shops.index') }}" 
                       class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-colors 
                       {{ request()->routeIs('shops.index') ? 'bg-blue-50 text-blue-700' : 'text-gray-500 hover:bg-gray-50 hover:text-blue-600' }}">
                        <i class="fa-solid fa-store w-5"></i> Data Toko
                    </a>

                    <a href="{{ route('chat.index') }}" class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-blue-600 rounded-xl font-medium transition-colors">
                        <i class="fa-solid fa-message w-5"></i> Pesan Masuk
                    </a>
                </nav>
            </div>

            <div class="p-4 border-t border-gray-100">
                <div class="flex items-center gap-3 px-4 py-3">
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500">Super Admin</p>
                    </div>
                </div>
                
                <form action="{{ route('logout') }}" method="POST" class="mt-2">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                        <i class="fa-solid fa-arrow-right-from-bracket mr-2"></i> Keluar
                    </button>
                </form>
            </div>
        </aside>

        <main class="flex-1 overflow-y-auto bg-gray-50 p-8">
            <div class="md:hidden mb-6 flex justify-between items-center">
                <h1 class="font-bold text-xl">KasirSuara.</h1>
                <button class="text-gray-600"><i class="fa-solid fa-bars"></i></button>
            </div>

            @yield('content')
        </main>
    </div>

</body>
</html>