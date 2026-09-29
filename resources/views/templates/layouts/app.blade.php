<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name') }}</title>
    {{-- <link rel="icon" type="image/png" href="{{ asset('admin/assets/images/favicon.png') }}" sizes="16x16"> --}}

    <!-- remix icon font css  -->
    <link rel="stylesheet" href="{{ asset('admin/assets/css/remixicon.css') }}">
    <!-- BootStrap css -->
    <link rel="stylesheet" href="{{ asset('admin/assets/css/lib/bootstrap.min.css') }}">
    <!-- Apex Chart css -->
    <link rel="stylesheet" href="{{ asset('admin/assets/css/lib/apexcharts.css') }}">
    <!-- Data Table css -->
    <link rel="stylesheet" href="{{ asset('admin/assets/css/lib/dataTables.min.css') }}">
    <!-- Date picker css -->
    <link rel="stylesheet" href="{{ asset('admin/assets/css/lib/flatpickr.min.css') }}">
    <!-- Calendar css -->
    <link rel="stylesheet" href="{{ asset('admin/assets/css/lib/full-calendar.css') }}">
    <!-- Vector Map css -->
    <link rel="stylesheet" href="{{ asset('admin/assets/css/lib/jquery-jvectormap-2.0.5.css') }}">
    <!-- Popup css -->
    <link rel="stylesheet" href="{{ asset('admin/assets/css/lib/magnific-popup.css') }}">
    <!-- Slick Slider css -->
    <link rel="stylesheet" href="{{ asset('admin/assets/css/lib/slick.css') }}">
    <!-- main css -->
    <link rel="stylesheet" href="{{ asset('admin/assets/css/style.css') }}">

    <!-- App Custom CSS & JS (Classic Asset) -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
    <script src="{{ asset('js/app.js') }}"></script>

    @livewireStyles
    @stack('css')
</head>

<body>
    {{-- Modal Universal & Notifikasi Toast --}}
    <livewire:admin.modal />
    <livewire:admin.toast />

    {{-- sidebar --}}
    <livewire:admin.sidebar wire:key="admin-sidebar-nav" />

    <main class="dashboard-main">
        {{-- navbar / header --}}
        <div style="position: sticky; top: 0; z-index: 1040;">
            <livewire:admin.header wire:key="admin-header-nav" />
        </div>

        {{-- main contenct  --}}
        <div class="dashboard-main-body">
            {{ $slot }}
        </div>
    </main>

    <!-- jQuery library js -->
    <script data-navigate-once src="{{ asset('admin/assets/js/lib/jquery-3.7.1.min.js') }}"></script>
    <!-- Bootstrap js -->
    <script data-navigate-once src="{{ asset('admin/assets/js/lib/bootstrap.bundle.min.js') }}"></script>
    <!-- Apex Chart js -->
    <script data-navigate-once src="{{ asset('admin/assets/js/lib/apexcharts.min.js') }}"></script>
    <!-- Data Table js -->
    <script data-navigate-once src="{{ asset('admin/assets/js/lib/dataTables.min.js') }}"></script>
    <!-- jQuery UI js -->
    <script data-navigate-once src="{{ asset('admin/assets/js/lib/jquery-ui.min.js') }}"></script>
    <!-- Vector Map js -->
    <script data-navigate-once src="{{ asset('admin/assets/js/lib/jquery-jvectormap-2.0.5.min.js') }}"></script>
    <script data-navigate-once src="{{ asset('admin/assets/js/lib/jquery-jvectormap-world-mill-en.js') }}"></script>
    <!-- Popup js -->
    <script data-navigate-once src="{{ asset('admin/assets/js/lib/magnifc-popup.min.js') }}"></script>
    <!-- Slick Slider js -->
    <script data-navigate-once src="{{ asset('admin/assets/js/lib/slick.min.js') }}"></script>
    <!-- main js -->
    <script data-navigate-once src="{{ asset('admin/assets/js/app.js') }}?v={{ filemtime(public_path('admin/assets/js/app.js')) }}"></script>
    <!-- Skrip helper utilitas global aplikasi -->
    <script data-navigate-once src="{{ asset('mine/script.js') }}?v={{ filemtime(public_path('mine/script.js')) }}"></script>

    @livewireScripts
    @stack('js-stack')
    @stack('scripts')
</body>

</html>
