<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(
    basePath: dirname(__DIR__)
)
    /**
     * Memuat route aplikasi.
     *
     * Route pada routes/web.php secara otomatis
     * menggunakan middleware group web.
     */
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )

    /**
     * Mengatur middleware aplikasi.
     *
     * Middleware role sudah tidak digunakan sehingga
     * tidak perlu lagi didaftarkan sebagai alias.
     */
    ->withMiddleware(
        function (Middleware $middleware): void {
            //
        }
    )

    /**
     * Mengatur penanganan exception aplikasi.
     */
    ->withExceptions(
        function (Exceptions $exceptions): void {
            //
        }
    )
    ->create();
