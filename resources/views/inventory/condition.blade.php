<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kondisi Barang - Gudang</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Poppins', sans-serif; } </style>
</head>
<body class="bg-gray-50">

    <div class="flex min-h-screen">
        @include('components.sidebar')

        <main class="flex-1 ml-0 md:ml-64 p-8">
            
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Cek Kondisi Barang</h1>
                    <p class="text-gray-500 text-sm">Input jumlah barang yang rusak atau basi untuk mengurangi stok.</p>
                </div>
                <div class="bg-white px-4 py-2 rounded-lg shadow-sm text-sm border border-gray-100">
                    <span class="mr-3"><i class="fas fa-circle text-green-500 text-[10px]"></i> Bagus (klik tombol)</span>
                    <span class="mr-3"><i class="fas fa-circle text-red-500 text-[10px]"></i> Input Jumlah Rusak</span>
                    <span><i class="fas fa-circle text-gray-900 text-[10px]"></i> Input Jumlah Basi</span>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-blue-600 text-white uppercase text-xs font-bold">
                        <tr>
                            <th class="p-4 border-b">No.</th>
                            <th class="p-4 border-b">Kode</th>
                            <th class="p-4 border-b">Nama Barang</th>
                            <th class="p-4 border-b">Kategori</th>
                            <th class="p-4 border-b">Stok</th>
                            <th class="p-4 border-b text-center">Status Kondisi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @foreach($products as $index => $item)
                        <tr class="hover:bg-gray-50 transition border-b border-gray-100 last:border-0">
                            <td class="p-4 font-semibold text-black font-bold">{{ $index + 1 }}</td>
                            <td class="p-4 text-gray-600">{{ $item->code }}</td>
                            <td class="p-4 font-bold text-gray-800">
                                {{ $item->name }}
                            </td>
                            <td class="p-4 text-gray-600">
                                @php
                                    if(!function_exists('categoryColor')) {
                                        function categoryColor($name) {
                                            $colors = ['red','yellow','green','blue','indigo','purple','pink','teal','orange','gray'];
                                            $idx = abs(crc32($name)) % count($colors);
                                            return $colors[$idx];
                                        }
                                    }
                                @endphp
                                @if($item->categories && $item->categories->isNotEmpty())
                                    @foreach($item->categories as $cat)
                                        @php $col = categoryColor($cat->name); @endphp
                                        <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold bg-{{ $col }}-100 text-{{ $col }}-800">{{ $cat->name }}</span>
                                    @endforeach
                                @else
                                    -
                                @endif
                            </td>
                            <td class="p-4 font-bold">
                            @if(($item->stock ?? 0) > 20)
                                        <span class="inline-block px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">
                                            {{ $item->stock }}
                                        </span>
                                    @elseif(($item->stock ?? 0) > 0)
                                        <span class="inline-block px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-semibold">
                                            {{ $item->stock }}
                                        </span>
                                    @else
                                        <span class="inline-block px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold">
                                            {{ $item->stock }}!
                                        </span>
                                    @endif 
                                    <span>
                                        {{ $item->unit ?? 'pcs' }}
                                    </span>   
                            </td>
                            
                            <td class="p-4">
                                <div class="flex justify-center items-center gap-4">
                                    
                                    <button onclick="updateStatus({{ $item->id }}, 'good')" 
                                            id="btn-good-{{ $item->id }}"
                                            class="w-6 h-6 rounded-full border-2 border-green-500 transition-all duration-300 hover:scale-110 
                                            {{ $item->current_condition == 'good' ? 'bg-green-500 shadow-lg shadow-green-200 ring-2 ring-green-100' : 'bg-transparent' }}"
                                            title="Set Good">
                                    </button>

                                    <div class="flex flex-col items-center gap-1">
                                        <label class="text-xs text-gray-500">Rusak</label>
                                        <input type="number" id="input-damaged-{{ $item->id }}" min="0" placeholder="0" 
                                               class="w-16 text-center border border-gray-300 rounded px-2 py-1 text-sm" 
                                               onchange="updateDamage({{ $item->id }}, 'damaged', this.value)">
                                    </div>

                                    <div class="flex flex-col items-center gap-1">
                                        <label class="text-xs text-gray-500">Basi</label>
                                        <input type="number" id="input-expired-{{ $item->id }}" min="0" placeholder="0" 
                                               class="w-16 text-center border border-gray-300 rounded px-2 py-1 text-sm" 
                                               onchange="updateDamage({{ $item->id }}, 'expired', this.value)">
                                    </div>

                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                
                @if($products->isEmpty())
                <div class="p-8 text-center text-gray-400">
                    <i class="fas fa-box-open text-4xl mb-3"></i>
                    <p>Belum ada data barang.</p>
                </div>
                @endif
            </div>

        </main>
    </div>

    <script>
        function updateStatus(productId, condition) {
            if (!confirm('Yakin mengubah kondisi barang menjadi baik?')) {
                return;
            }
            // 1. Visual Feedback Langsung (Biar kerasa cepet)
            resetButtons(productId);
            setActiveButton(productId, condition);

            // 2. Kirim ke Database via Fetch API
            fetch("{{ route('inventory.condition.update') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    product_id: productId,
                    condition: condition
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log("Success:", data);
                if (data.error) {
                    alert(data.error);
                    // Reset visual jika error
                    location.reload();
                }
            })
            .catch((error) => {
                console.error("Error:", error);
                alert("Gagal mengupdate kondisi barang.");
                location.reload();
            });
        }

        function updateDamage(productId, condition, quantity) {
            quantity = parseInt(quantity);
            if (quantity <= 0) return; // Jangan kirim jika 0 atau kosong

            if (!confirm(`Yakin menandai ${quantity} barang sebagai ${condition === 'damaged' ? 'rusak' : 'basi'}?`)) {
                // Reset input jika cancel
                document.getElementById(`input-${condition}-${productId}`).value = '';
                return;
            }

            fetch("{{ route('inventory.condition.update') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    product_id: productId,
                    condition: condition,
                    quantity: quantity
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log("Success:", data);
                if (data.error) {
                    alert(data.error);
                    // Reset input jika error
                    document.getElementById(`input-${condition}-${productId}`).value = '';
                } else {
                    alert(data.message);
                    location.reload(); // Refresh to update stock
                }
            })
            .catch((error) => {
                console.error("Error:", error);
                alert("Gagal mengupdate kondisi barang.");
                // Reset input jika error
                document.getElementById(`input-${condition}-${productId}`).value = '';
            });
        }

        // Helper: Matikan semua warna tombol di baris itu
        function resetButtons(id) {
            document.getElementById(`btn-good-${id}`).classList.remove('bg-green-500', 'shadow-lg', 'shadow-green-200', 'ring-2', 'ring-green-100');
            document.getElementById(`btn-good-${id}`).classList.add('bg-transparent');
        }

        // Helper: Nyalakan warna tombol yang diklik
        function setActiveButton(id, condition) {
            let btn = document.getElementById(`btn-${condition}-${id}`);
            let colorClass = 'bg-green-500';
            let shadowClass = 'shadow-green-200';
            
            btn.classList.remove('bg-transparent');
            btn.classList.add(colorClass, 'shadow-lg', shadowClass, 'ring-2', 'ring-green-100');
        }
    </script>

</body>
</html>