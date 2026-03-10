@extends('layouts.admin')

@section('title', 'Data Mitra Toko')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Data Mitra Toko</h2>
        <p class="text-gray-500 text-sm">Memantau daftar toko yang telah bergabung melalui aplikasi.</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-4 font-semibold">No</th>
                    <th class="px-6 py-4 font-semibold">Nama Toko</th>
                    <th class="px-6 py-4 font-semibold">Pemilik (Owner)</th>
                    <th class="px-6 py-4 font-semibold">Tanggal Gabung</th>
                    <th class="px-6 py-4 font-semibold text-center">Status</th>
                    <th class="px-6 py-4 font-semibold text-center">aksi</th>
                    <th class="px-6 py-4 font-semibold text-right">Akses</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                @forelse($shops as $shop)
                <tr class="group hover:bg-blue-50/50 transition-colors">
                    <td class="px-6 py-4">{{ $loop->iteration }}</td>
                    <td class="px-6 py-4">
                        <div class="font-semibold text-gray-800">{{ $shop->name }}</div>
                        <div class="text-xs text-gray-400">{{ Str::limit($shop->address, 30) }}</div>
                    </td>
                    
                    <td class="px-6 py-4">
                        @php $owner = $shop->users->where('role', 'owner')->first(); @endphp
                        @if($owner)
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">
                                    {{ substr($owner->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-gray-800 font-medium">{{ $owner->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $owner->username }}</p>
                                </div>
                            </div>
                        @else
                            <span class="text-red-400 text-xs italic">Data Owner Hilang/Error</span>
                        @endif
                    </td>

                    <td class="px-6 py-4 text-gray-500">{{ $shop->created_at->format('d M Y') }}</td>

                    <td class="px-6 py-4 text-center">
                        @if($shop->status == 'active')
                            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Aktif</span>
                        @else
                            <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold">Dibekukan</span>
                        @endif
                    </td>
                    
                    <td class="px-6 py-4 text-right">
                        <form action="{{ route('shops.toggle', $shop->id) }}" method="POST">
                            @csrf @method('PATCH')
                            @if($shop->status == 'active')
                                <button type="submit" class="text-red-500 hover:text-red-700 font-medium text-xs border border-red-200 px-3 py-1 rounded-lg hover:bg-red-50 transition-colors">
                                    Bekukan Toko
                                </button>
                            @else
                                <button type="submit" class="text-green-500 hover:text-green-700 font-medium text-xs border border-green-200 px-3 py-1 rounded-lg hover:bg-green-50 transition-colors">
                                    Aktifkan Kembali
                                </button>
                            @endif
                        </form>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('shops.show', $shop->id) }}" class="text-gray-400 hover:text-blue-600 px-2 transition-colors" title="Lihat Detail">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-10 text-center text-gray-400">Belum ada toko yang mendaftar.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection