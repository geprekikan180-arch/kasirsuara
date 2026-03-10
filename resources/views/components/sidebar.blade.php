<aside class="w-64 bg-white border-r border-gray-100 hidden md:block fixed h-full z-10">
    <div>
        <div class="h-20 flex items-center justify-center border-b border-gray-100">
            <h1 class="text-2xl font-bold text-blue-700 tracking-tighter">Kasur<span class="text-gray-800">Pintar.</span></h1>
        </div>
    </div>

    {{-- <div class="px-6 text-center mb-8">
        <div class="w-24 h-24 bg-gray-300 rounded-full mx-auto mb-3"></div>
        <h3 class="font-bold text-gray-800">{{ auth()->user()->shop->name ?? 'Toko' }}</h3>
    </div> --}}

    <nav class="px-4 space-y-2">
        {{-- Owner Navigation --}}
        @can('access-owner')
            {{-- @include('components.voice-assistant') --}}

            <div class="text-center mb-8">
                <div class="w-20 h-20 mx-auto rounded-full bg-gray-200 mb-3 overflow-hidden">
                    <img src="{{ Auth::user()->shop->logo ? asset('storage/shops/'.Auth::user()->shop->logo) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->shop->name).'&background=0D8ABC&color=fff' }}" 
                    class="w-full h-full object-cover">
                </div>
        
                <a href="{{ route('owner.profile') }}" class="text-lg font-bold text-gray-800 hover:text-blue-600 transition flex items-center justify-center gap-2">
                    {{ Auth::user()->shop->name }}
                <i class="fas fa-edit text-xs text-gray-400"></i> </a>
    
                 <p class="text-sm bg-green-100 text-green-700 px-3 py-1 rounded-full">Owner</p>
            </div>
            <a href="{{ route('owner.dashboard') }}" 
               class="flex items-center gap-3 px-6 py-3 @if(request()->routeIs('owner.dashboard')) bg-blue-600 text-white rounded-2xl shadow-lg shadow-blue-600/30 @else text-gray-400 hover:text-blue-600 hover:bg-gray-50 rounded-2xl transition-all @endif font-medium">
                <i class="fa-solid fa-home"></i> Beranda
            </a>
            <a href="{{ route('owner.products') }}" 
               class="flex items-center gap-3 px-6 py-3 @if(request()->routeIs('owner.products')) bg-blue-600 text-white rounded-2xl shadow-lg shadow-blue-600/30 @else text-gray-400 hover:text-blue-600 hover:bg-gray-50 rounded-2xl transition-all @endif font-medium">
                <i class="fa-solid fa-box"></i> Data Barang
            </a>
            <a href="{{ route('owner.transactions') }}" 
            class="flex items-center gap-3 px-6 py-3 @if(request()->routeIs('owner.transactions')) bg-blue-600 text-white rounded-2xl shadow-lg shadow-blue-600/30 @else text-gray-400 hover:text-blue-600 hover:bg-gray-50 rounded-2xl transition-all @endif font-medium">
            <i class="fa-solid fa-receipt"></i> Data Transaksi
            </a>
            <a href="{{ route('owner.report') }}" 
            class="flex items-center gap-3 px-6 py-3 @if(request()->routeIs('owner.report')) bg-blue-600 text-white rounded-2xl shadow-lg shadow-blue-600/30 @else text-gray-400 hover:text-blue-600 hover:bg-gray-50 rounded-2xl transition-all @endif font-medium font-medium">
            <i class="fa-solid fa-chart-line"></i> Laporan Bulanan
            </a>
            <a href="{{ route('employees.index') }}" 
               class="flex items-center gap-3 px-6 py-3 @if(request()->routeIs('employees.index')) bg-blue-600 text-white rounded-2xl shadow-lg shadow-blue-600/30 @else text-gray-400 hover:text-blue-600 hover:bg-gray-50 rounded-2xl transition-all @endif font-medium">
                <i class="fa-solid fa-users"></i> Data Karyawan
            </a>
            <a href="{{ route('damaged_goods.index') }}" 
               class="flex items-center gap-3 px-6 py-3 @if(request()->routeIs('damaged_goods.index')) bg-blue-600 text-white rounded-2xl shadow-lg shadow-blue-600/30 @else text-gray-400 hover:text-blue-600 hover:bg-gray-50 rounded-2xl transition-all @endif font-medium">
                <i class="fa-solid fa-exclamation-triangle"></i> Barang Rusak
            </a>
            <a href="{{ route('chat.index') }}" 
               class="flex items-center gap-3 px-6 py-3 @if(request()->routeIs('')) bg-blue-600 text-white rounded-2xl shadow-lg shadow-blue-600/30 @else text-gray-400 hover:text-blue-600 hover:bg-gray-50 rounded-2xl transition-all @endif font-medium">
                <i class="fa-solid fa-message"></i> Ruang Pesan
            </a>
        @endcan

        {{-- Cashier Navigation --}}
        @can('access-cashier')
                
    
            <div class="px-6 text-center mb-8">
                <div class="w-20 h-20 mx-auto rounded-full bg-gray-200 mb-3 overflow-hidden">
                    <img src="{{ Auth::user()->shop->logo ? asset('storage/shops/'.Auth::user()->shop->logo) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->shop->name).'&background=0D8ABC&color=fff' }}" 
                    class="w-full h-full object-cover">
                </div>
                <h3 class="font-bold text-gray-800">{{ auth()->user()->name ?? 'Toko' }}</h3>
                <p class="text-sm text-gray-500">
                    @if(Auth::user()->role == 'cashier')
                        <span class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-xs font-bold">Kasir</span>
                    @else
                        <span class="bg-orange-100 text-orange-700 px-3 py-1 rounded-full text-xs font-bold">Gudang</span>
                    @endif
                </p>
            </div>
            
            <a href="{{ route('cashier.dashboard') }}" 
               class="flex items-center gap-3 px-6 py-3 @if(request()->routeIs('cashier.dashboard')) bg-blue-600 text-white rounded-2xl shadow-lg shadow-blue-600/30 @else text-gray-400 hover:text-blue-600 hover:bg-gray-50 rounded-2xl transition-all @endif font-medium">
                <i class="fa-solid fa-calculator"></i> Kasir
            </a>
            <a href="{{ route('cashier.transactions') }}" 
               class="flex items-center gap-3 px-6 py-3 @if(request()->routeIs('cashier.transactions')) bg-blue-600 text-white rounded-2xl shadow-lg shadow-blue-600/30 @else text-gray-400 hover:text-blue-600 hover:bg-gray-50 rounded-2xl transition-all @endif font-medium">
                <i class="fa-solid fa-receipt"></i> Riwayat Transaksi
            </a>
            <a href="{{ route('chat.index') }}" 
               class="flex items-center gap-3 px-6 py-3 @if(request()->routeIs('employees.index')) bg-blue-600 text-white rounded-2xl shadow-lg shadow-blue-600/30 @else text-gray-400 hover:text-blue-600 hover:bg-gray-50 rounded-2xl transition-all @endif font-medium">
                <i class="fa-solid fa-message"></i> Ruang Pesan
            </a>

                <form action="{{ route('logout') }}" method="POST" class="mt-10">
                    @csrf
                    <button type="submit" class="absolute bottom-0 w-full text-left px-4 py-5 text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                        <i class="fa-solid fa-arrow-right-from-bracket mr-2"></i> Keluar
                    </button>
                </form>
        @endcan

        {{-- Inventory Navigation --}}
        @can('access-inventory')
        
            <div class="px-6 text-center mb-8">
                <div class="w-20 h-20 mx-auto rounded-full bg-gray-200 mb-3 overflow-hidden">
                    <img src="{{ Auth::user()->shop->logo ? asset('storage/shops/'.Auth::user()->shop->logo) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->shop->name).'&background=0D8ABC&color=fff' }}" 
                    class="w-full h-full object-cover">
                 </div>
                 <h3 class="font-bold text-gray-800">{{ auth()->user()->name ?? 'Toko' }}</h3>
                 <p class="text-sm text-gray-500">
                    @if(Auth::user()->role == 'cashier')
                        <span class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-xs font-bold">Kasir</span>
                    @else
                        <span class="bg-orange-100 text-orange-700 px-3 py-1 rounded-full text-xs font-bold">Gudang</span>
                    @endif
                </p>
            </div>
            
            <a href="{{ route('inventory.dashboard') }}" 
               class="flex items-center gap-3 px-6 py-3 @if(request()->routeIs('inventory.dashboard')) bg-blue-600 text-white rounded-2xl shadow-lg shadow-blue-600/30 @else text-gray-400 hover:text-blue-600 hover:bg-gray-50 rounded-2xl transition-all @endif font-medium">
                <i class="fa-solid fa-warehouse"></i> data
            </a>
            <a href="{{ route('inventory.condition') }}" 
               class="flex items-center gap-3 px-6 py-3 @if(request()->routeIs('inventory.condition')) bg-blue-600 text-white rounded-2xl shadow-lg shadow-blue-600/30 @else text-gray-400 hover:text-blue-600 hover:bg-gray-50 rounded-2xl transition-all @endif font-medium">
                <i class="fa-solid fa-check"></i> kondisi
            </a>
            <a href="{{ route('chat.index') }}" 
               class="flex items-center gap-3 px-6 py-3 @if(request()->routeIs('chat.index')) bg-blue-600 text-white rounded-2xl shadow-lg shadow-blue-600/30 @else text-gray-400 hover:text-blue-600 hover:bg-gray-50 rounded-2xl transition-all @endif font-medium">
                <i class="fa-solid fa-message"></i> Ruang Pesan
            </a>
        @endcan

        
    </nav>
</aside>
