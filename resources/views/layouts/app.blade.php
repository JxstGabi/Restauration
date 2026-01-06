<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Restauration scolaire')</title>

    <!-- Tailwind (développement rapide) -->
    <script src="https://cdn.tailwindcss.com"></script>

    @yield('styles')
</head>
<body class="bg-gray-100 text-gray-800">

    @yield('header')

    <div class="">
        @yield('content')
    </div>

    @yield('scripts')
</body>
</html>
