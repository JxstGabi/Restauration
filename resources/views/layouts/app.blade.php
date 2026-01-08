<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Restauration scolaire')</title>

    <!-- Tailwind (développement rapide) -->
    <script src="https://cdn.tailwindcss.com"></script>

    @yield('styles')
</head>
<body class="bg-gray-100 text-gray-800 min-h-screen relative">
    
    @unless(View::hasSection('no_background'))
    <!-- Background Image -->
    <div class="fixed inset-0 z-0">
        <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ asset('images/repas-de-famille.jpg') }}'); filter: blur(4px);"></div>
        <div class="absolute inset-0 bg-black/10"></div>
    </div>
    @endunless

    <!-- Content Wrapper -->
    <div class="relative z-10 flex flex-col min-h-screen">
        @yield('header')

        <div class="flex-grow">
            @yield('content')
        </div>
    </div>

    @yield('scripts')
</body>
</html>
