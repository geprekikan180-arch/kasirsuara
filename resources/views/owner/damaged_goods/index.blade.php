<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Barang Rusak - Owner</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Poppins', sans-serif; } </style>
</head>
<body class="bg-gray-50">
    <div class="flex min-h-screen">
        @include('components.sidebar')
        
        <main class="flex-1 ml-0 md:ml-64 p-8">
            
            @include('components.search-panel')


            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-blue-600 text-white px-6 py-4 flex justify-between items-center">
                    <h2 class="text-xl font-bold">Daftar Barang</h2>
                    
                </div>
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 border-b border-gray-200 uppercase text-xs font-bold">
                        <tr>
                            <th class="px-6 py-4 font-semibold text-gray-700">No.</th>
                            <th class="px-6 py-4 font-semibold text-gray-700">Kode Barang</th>
                            <th class="px-6 py-4 font-semibold text-gray-700">Nama Barang</th>
                            <th class="px-6 py-4 font-semibold text-gray-700">Jumlah</th>
                            <th class="px-6 py-4 font-semibold text-gray-700">Keterangan</th>
                            <th class="px-6 py-4 font-semibold text-gray-700 text-center">Waktu</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @foreach($damagedGoods as $index => $item)
                        <tr class="hover:bg-gray-50 transition border-b border-gray-100 last:border-0">
                            <td class="px-6 py-4 font-semibold text-black font-bold">{{ $damagedGoods->firstItem() + $index }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $item->product->code }}</td>
                            <td class="px-6 py-4 font-bold text-gray-800">{{ $item->product->name }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $item->quantity }}</td>
                            <td class="px-6 py-4 text-gray-600">
                                @if($item->type == 'damaged')
                                    <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-bold">Rusak</span>
                                @else
                                    <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-xs font-bold">Basi</span>
                                @endif
                            </td>
                            <td class="p-4 text-gray-600 text-center">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                
                @if($damagedGoods->isEmpty())
                <div class="p-8 text-center text-gray-400">
                    <i class="fas fa-box-open text-4xl mb-3"></i>
                    <p>Belum ada data barang rusak.</p>
                </div>
                @endif
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $damagedGoods->links() }}
            </div>

        </main>
    </div>

</body>
</html>