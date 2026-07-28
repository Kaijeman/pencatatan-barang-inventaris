<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    {{-- Judul tab browser. --}}
    <title>
        @hasSection('title')
            @yield('title') -
        @endif
        {{ config('app.name') }}
    </title>

    {{-- Asset aplikasi. --}}
    @vite([
        'resources/css/app.css',
        'resources/js/app.js',
    ])

    {{-- Ikon Bootstrap. --}}
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
    >
</head>

<body class="bg-slate-100">
    {{-- Sidebar aplikasi. --}}
    @include('partials.sidebar')

    {{-- Area utama aplikasi. --}}
    <div
        id="app-shell"
        class="flex h-screen min-w-0 flex-col
            overflow-hidden transition-[margin]
            duration-300 ease-in-out lg:ml-64"
    >
        {{-- Navbar aplikasi. --}}
        @include('partials.navbar')

        {{-- Konten utama aplikasi. --}}
        <main class="flex-1 overflow-y-auto p-4 sm:p-6">
            @yield('content')
        </main>
    </div>

    {{-- Modal global aplikasi. --}}
    @include('partials.app_modal')

    {{-- JavaScript tambahan dari halaman tertentu. --}}
    @stack('scripts')

    {{-- Pengaturan membuka dan menutup sidebar. --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const sidebar = document.getElementById(
                'sidebar'
            );

            const appShell = document.getElementById(
                'app-shell'
            );

            const overlay = document.getElementById(
                'sidebar-overlay'
            );

            const toggleButtons = document.querySelectorAll(
                '[data-sidebar-toggle]'
            );

            const sidebarLinks = document.querySelectorAll(
                '[data-sidebar-link]'
            );

            if (
                ! sidebar
                || ! appShell
                || ! overlay
            ) {
                return;
            }

            const desktopBreakpoint = 1024;

            /**
             * Menyimpan status sidebar desktop.
             */
            let desktopSidebarOpen =
                localStorage.getItem('desktopSidebarOpen')
                !== 'false';

            /**
             * Sidebar perangkat kecil ditutup secara default.
             */
            let mobileSidebarOpen = false;

            /**
             * Memeriksa apakah layar menggunakan ukuran desktop.
             */
            function isDesktop() {
                return window.innerWidth
                    >= desktopBreakpoint;
            }

            /**
             * Memperbarui tampilan sidebar.
             */
            function updateSidebar() {
                const desktop = isDesktop();

                const sidebarOpen = desktop
                    ? desktopSidebarOpen
                    : mobileSidebarOpen;

                /**
                 * Menggeser sidebar sepenuhnya keluar layar
                 * ketika dalam kondisi tertutup.
                 */
                sidebar.style.transform = sidebarOpen
                    ? 'translateX(0)'
                    : 'translateX(-100%)';

                /**
                 * Menghilangkan ruang sidebar ketika sidebar
                 * desktop ditutup.
                 */
                appShell.style.marginLeft =
                    desktop && desktopSidebarOpen
                        ? '16rem'
                        : '0';

                /**
                 * Menampilkan overlay hanya ketika sidebar
                 * perangkat kecil sedang terbuka.
                 */
                overlay.classList.toggle(
                    'hidden',
                    desktop || ! mobileSidebarOpen
                );

                /**
                 * Mencegah halaman belakang bergeser ketika
                 * sidebar perangkat kecil sedang terbuka.
                 */
                document.body.classList.toggle(
                    'overflow-hidden',
                    ! desktop && mobileSidebarOpen
                );

                /**
                 * Memperbarui atribut aksesibilitas tombol.
                 */
                toggleButtons.forEach((button) => {
                    button.setAttribute(
                        'aria-expanded',
                        sidebarOpen
                            ? 'true'
                            : 'false'
                    );
                });
            }

            /**
             * Membuka atau menutup sidebar.
             */
            function toggleSidebar() {
                if (isDesktop()) {
                    desktopSidebarOpen =
                        ! desktopSidebarOpen;

                    localStorage.setItem(
                        'desktopSidebarOpen',
                        desktopSidebarOpen
                            ? 'true'
                            : 'false'
                    );
                } else {
                    mobileSidebarOpen =
                        ! mobileSidebarOpen;
                }

                updateSidebar();
            }

            /**
             * Menangani tombol sidebar.
             */
            toggleButtons.forEach((button) => {
                button.addEventListener(
                    'click',
                    toggleSidebar
                );
            });

            /**
             * Menutup sidebar melalui overlay.
             */
            overlay.addEventListener('click', () => {
                mobileSidebarOpen = false;

                updateSidebar();
            });

            /**
             * Menutup sidebar perangkat kecil setelah
             * pengguna memilih salah satu menu.
             */
            sidebarLinks.forEach((link) => {
                link.addEventListener('click', () => {
                    if (isDesktop()) {
                        return;
                    }

                    mobileSidebarOpen = false;

                    updateSidebar();
                });
            });

            /**
             * Menyesuaikan sidebar ketika ukuran layar berubah.
             */
            window.addEventListener('resize', () => {
                if (isDesktop()) {
                    mobileSidebarOpen = false;
                }

                updateSidebar();
            });

            updateSidebar();
        });
    </script>
</body>
</html>
