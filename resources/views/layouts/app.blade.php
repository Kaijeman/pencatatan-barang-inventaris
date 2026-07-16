<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"content="width=device-width, initial-scale=1.0">
        <title>{{ config('app.name') }}</title>
        @vite([
            'resources/css/app.css',
            'resources/js/app.js'
        ])
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    </head>

    <body class="bg-slate-100">
        <div class="flex h-screen">
            <x-sidebar />
            <div class="flex flex-col flex-1 overflow-hidden">
                <x-navbar />
                <main class="flex-1 overflow-y-auto p-6">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
