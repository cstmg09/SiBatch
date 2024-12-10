<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="application-name" content="{{ config('app.name') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name') }}</title>

    <!-- TailwindCSS and Alpine.js -->
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    @filamentStyles
    @vite('resources/css/app.css')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="@yield('body-class')">
    <!-- Main Content -->
    @yield('content')

    <!-- Livewire Notifications -->
    @livewire('notifications')

    <!-- Filament Scripts and Vite -->
    @filamentScripts
    @vite('resources/js/app.js')


</body>
<footer class="bg-gray-800 text-gray-400">
    <div class="container mx-auto px-6 py-6 text-center">
      <p>Skripsi M.Canro</p>
    </div>
  </footer>


</html>
