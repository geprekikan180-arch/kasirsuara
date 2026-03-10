<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Chat') - Kasir Suara</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<!-- Menghilangkan padding dan margin default dari body untuk menggunakan tinggi penuh layar -->
<body class="bg-gray-50 text-gray-800 min-h-full">
    <!-- Menggunakan min-h-screen untuk memastikan div ini setinggi layar penuh -->
    <div class="flex min-h-screen">

        <!-- Main content area akan mengambil sisa ruang yang tersedia -->
        <main class="flex-1">
            @yield('content')
        </main>
    </div>

</body>
</html>
