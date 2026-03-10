<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kasir Suara - Sistem Manajemen Inklusif</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            blue: '#1d4ed8', 
                            dark: '#0f172a',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-white text-slate-800 antialiased">

    <nav class="fixed top-1 left-4 right-4 p-4 md:p-6 flex items-center z-50">
    <!-- Lingkaran sekarang di kiri karena mr-auto mendorong elemen lain ke kanan -->
    <div class="mr-auto">
        <img src="img\logooo.png" alt="" class="w-12 h-12 rounded-full">
    </div>
    


    <div class="flex gap-1">
        <a href="{{ route('login') }}" class="inline-flex items-center h-10 px-4 text-blue-500 font-semibold hover:text-blue-700 hover:bg-slate-100 transition rounded-full">
            Masuk
        </a>
        <a href="{{ route('register') }}" class="inline-flex items-center h-10 px-4 text-blue-600 font-semibold hover:text-blue-700 hover:bg-slate-100 transition rounded-full">
            Daftar
        </a>
    </div>
</nav>


    <section class="min-h-screen flex items-center justify-center p-6 relative">
        <div class="w-full max-w-2xl">
            
            <h3 class="text-gray-500 font-bold text-sm tracking-widest mb-2 uppercase animate-fade-in-up">
                Hallo!
            </h3>

            <h1 class="text-5xl md:text-7xl font-extrabold text-slate-900 leading-tight mb-4">
                Selamat Datang <br>
                Kasir Suara.
            </h1>

            <h2 class="text-gray-500 font-bold text-sm tracking-wide uppercase mb-8">
                Sistem Manajemen Barang
            </h2>

            <div class="w-24 h-0.5 bg-gray-300 mb-10"></div>

            <div class="flex flex-col md:flex-row gap-12 mb-10">
                <div>
                    <div class="flex items-baseline">
                        <span class="text-4xl font-extrabold text-slate-900">{{ number_format(\App\Models\User::whereIn('role', ['cashier','inventory'])->count() + \App\Models\Shop::count(), 0, ',', '.') }}</span>
                        <span class="text-4xl font-extrabold text-slate-900 text-blue-600">+</span>
                    </div>
                    <p class="text-gray-500 font-medium mt-1 mr-40">Pengguna Seluruh Platform</p>
                </div>
                <div>
                    <div class="flex items-baseline gap-2">
                        <span class="text-4xl font-extrabold text-gray-500">{{ number_format(\App\Models\Shop::count(), 0, ',', '.') }}+</span>
                        <span class="text-3xl font-bold text-gray-500">toko</span>
                    </div>
                    <p class="text-gray-500 font-medium mt-1">Telah Bergabung</p>
                </div>
            </div>

            <a href="#tentang" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-full transition-all duration-300 shadow-lg shadow-blue-500/30 group">
                <span>Lanjut</span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-4 h-4 group-hover:translate-y-1 transition-transform">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                </svg>
            </a>
        </div>
    </section>

    <section id="tentang" class="py-20 bg-slate-50 px-6">
        <div class="max-w-5xl mx-auto">
            
            <div class="mb-16 max-w-1xl">
                <h4 class="text-blue-600 font-bold tracking-widest uppercase text-sm mb-3">Misi Kami</h4>
                <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 mb-6">Membangun Teknologi yang Mengerti Manusia.</h2>
                <p class="text-lg text-justify text-slate-600 leading-relaxed ">
                    Kami menghadirkan <strong>Kasir Suara</strong> dengan tujuan mempermudah manajemen barang sekaligus menciptakan lingkungan kerja yang inklusif. 
                    Sistem ini dirancang khusus untuk <strong>membantu kasir dan inventory</strong> agar dapat bekerja maksimal menggunakan perintah suara, 
                    menyediakan ruang percakapan yang hangat di lingkungan toko, serta menyajikan antarmuka visual yang nyaman dan tidak melelahkan mata.
                </p>
            </div>

            <h4 class="text-blue-600 font-bold tracking-widest uppercase text-sm mb-8">Keunggulan Fitur</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                <div class="bg-white p-8 rounded-2xl shadow-sm hover:shadow-md transition border border-slate-100">
                    <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 006-6v-1.5m-6 7.5a6 6 0 01-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 01-3-3V4.5a3 3 0 116 0v8.25a3 3 0 01-3 3z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Teknologi Perintah Suara</h3>
                    <p class="text-slate-600">
                        Input data dan transaksi tanpa sentuhan. Fitur aksesibilitas canggih yang memungkinkan siapa saja, termasuk teman difabel, untuk mengoperasikan kasir dengan mudah dan cepat.
                    </p>
                </div>

                <div class="bg-white p-8 rounded-2xl shadow-sm hover:shadow-md transition border border-slate-100">
                    <div class="w-12 h-12 bg-green-100 text-green-600 rounded-lg flex items-center justify-center mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5m.75-9l3-3 2.148 2.148A12.061 12.061 0 0116.5 7.605" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Laporan Analitik Bulanan</h3>
                    <p class="text-slate-600">
                        Pantau performa toko Anda dengan rekap data otomatis setiap bulan. Data disajikan secara transparan untuk membantu pengambilan keputusan bisnis yang lebih tepat.
                    </p>
                </div>

                <div class="bg-white p-8 rounded-2xl shadow-sm hover:shadow-md transition border border-slate-100">
                    <div class="w-12 h-12 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Tampilan Visual Nyaman</h3>
                    <p class="text-slate-600">
                        Desain antarmuka yang bersih (clean) dan tidak membingungkan. Kami memprioritaskan kenyamanan visual agar pengguna betah berlama-lama mengelola manajemen barang.
                    </p>
                </div>

                <div class="bg-white p-8 rounded-2xl shadow-sm hover:shadow-md transition border border-slate-100">
                    <div class="w-12 h-12 bg-orange-100 text-orange-600 rounded-lg flex items-center justify-center mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 01-.825-.242m9.345-8.334a2.126 2.126 0 00-.476-.095 48.64 48.64 0 00-8.048 0c-1.186.078-2.1 1.051-2.1 2.238v4.692c0 1.135.844 2.098 1.976 2.192.89.073 1.804.12 2.734.12 1.256 0 2.51-.05 3.754-.142l1.657 1.657v-2.071c.797-.157 1.488-.824 1.488-1.66V10.608a2.11 2.11 0 00-.476-1.319" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Room Chat Terintegrasi</h3>
                    <p class="text-slate-600">
                        Membangun komunikasi tim yang solid langsung dari aplikasi. Diskusikan stok barang atau koordinasi shift tanpa perlu berpindah ke aplikasi pesan lain.
                    </p>
                </div>

            </div>

            <div class="mt-20 pt-8 border-t border-slate-200 text-center text-slate-400 text-sm">
                &copy; {{ date('Y') }} Kasir Suara. All rights reserved.
            </div>

        </div>
    </section>

</body>
</html>