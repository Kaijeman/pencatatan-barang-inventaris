<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>@hasSection('title')@yield('title') - @endif{{ config('app.name') }}</title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js',
    ])

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>

<body class="bg-slate-100">
    <div class="flex h-screen overflow-hidden">

        @include('partials.sidebar')

        <div class="flex min-w-0 flex-1 flex-col overflow-hidden">

            @include('partials.navbar')

            <main class="flex-1 overflow-y-auto p-6">
                @yield('content')
            </main>

        </div>
    </div>
    {{-- Modal global aplikasi. --}}
    @include('partials.app_modal')

    {{-- Memuat JavaScript tambahan dari halaman tertentu. --}}
    @stack('scripts')
</body>
</html>
