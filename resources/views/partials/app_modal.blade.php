{{-- Modal global untuk konfirmasi dan pemberitahuan. --}}
<div
    id="app-modal"
    class="fixed inset-0 z-[100] hidden items-center justify-center p-4"
    aria-hidden="true"
>
    {{-- Latar belakang modal. --}}
    <div
        id="app-modal-backdrop"
        class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm"
    ></div>

    {{-- Kotak modal. --}}
    <div
        class="relative z-10 w-full max-w-md overflow-hidden
            rounded-2xl bg-white shadow-2xl"
        role="dialog"
        aria-modal="true"
        aria-labelledby="app-modal-title"
        aria-describedby="app-modal-message"
    >
        <div class="p-6">
            <div class="flex items-start gap-4">

                {{-- Ikon modal. --}}
                <div
                    id="app-modal-icon-wrapper"
                    class="flex h-12 w-12 flex-shrink-0 items-center
                        justify-center rounded-full bg-amber-100
                        text-xl text-amber-700"
                >
                    <i
                        id="app-modal-icon"
                        class="bi bi-exclamation-triangle"
                    ></i>
                </div>

                {{-- Isi modal. --}}
                <div class="min-w-0 flex-1">
                    <h2
                        id="app-modal-title"
                        class="text-lg font-bold text-slate-800"
                    >
                        Konfirmasi
                    </h2>

                    <p
                        id="app-modal-message"
                        class="mt-2 whitespace-pre-line text-sm
                            leading-6 text-slate-600"
                    ></p>
                </div>
            </div>
        </div>

        {{-- Tombol aksi modal. --}}
        <div
            class="flex flex-col-reverse gap-3 border-t
                border-slate-200 bg-slate-50 px-6 py-4 sm:flex-row
                sm:justify-end"
        >
            <button
                type="button"
                id="app-modal-cancel"
                class="rounded-lg border border-slate-300 bg-white
                    px-5 py-2.5 text-sm font-semibold text-slate-600
                    transition hover:bg-slate-100 focus:outline-none
                    focus:ring-2 focus:ring-slate-200"
            >
                Batal
            </button>

            <button
                type="button"
                id="app-modal-confirm"
                class="rounded-lg bg-amber-600 px-5 py-2.5
                    text-sm font-semibold text-white transition
                    hover:bg-amber-700 focus:outline-none
                    focus:ring-2 focus:ring-amber-200"
            >
                Lanjutkan
            </button>
        </div>
    </div>
</div>

<script>
    (() => {
        const modal = document.getElementById('app-modal');
        const backdrop = document.getElementById(
            'app-modal-backdrop'
        );

        const titleElement = document.getElementById(
            'app-modal-title'
        );

        const messageElement = document.getElementById(
            'app-modal-message'
        );

        const iconWrapper = document.getElementById(
            'app-modal-icon-wrapper'
        );

        const iconElement = document.getElementById(
            'app-modal-icon'
        );

        const cancelButton = document.getElementById(
            'app-modal-cancel'
        );

        const confirmButton = document.getElementById(
            'app-modal-confirm'
        );

        let modalResolver = null;
        let previousFocusedElement = null;
        let cancelAllowed = true;

        /**
         * Konfigurasi tampilan berdasarkan jenis modal.
         */
        const modalStyles = {
            warning: {
                iconWrapper:
                    'flex h-12 w-12 flex-shrink-0 items-center ' +
                    'justify-center rounded-full bg-amber-100 ' +
                    'text-xl text-amber-700',

                icon:
                    'bi bi-exclamation-triangle',

                confirmButton:
                    'rounded-lg bg-amber-600 px-5 py-2.5 ' +
                    'text-sm font-semibold text-white transition ' +
                    'hover:bg-amber-700 focus:outline-none ' +
                    'focus:ring-2 focus:ring-amber-200',
            },

            danger: {
                iconWrapper:
                    'flex h-12 w-12 flex-shrink-0 items-center ' +
                    'justify-center rounded-full bg-red-100 ' +
                    'text-xl text-red-700',

                icon:
                    'bi bi-trash',

                confirmButton:
                    'rounded-lg bg-red-600 px-5 py-2.5 ' +
                    'text-sm font-semibold text-white transition ' +
                    'hover:bg-red-700 focus:outline-none ' +
                    'focus:ring-2 focus:ring-red-200',
            },

            info: {
                iconWrapper:
                    'flex h-12 w-12 flex-shrink-0 items-center ' +
                    'justify-center rounded-full bg-blue-100 ' +
                    'text-xl text-blue-700',

                icon:
                    'bi bi-info-circle',

                confirmButton:
                    'rounded-lg bg-blue-600 px-5 py-2.5 ' +
                    'text-sm font-semibold text-white transition ' +
                    'hover:bg-blue-700 focus:outline-none ' +
                    'focus:ring-2 focus:ring-blue-200',
            },

            success: {
                iconWrapper:
                    'flex h-12 w-12 flex-shrink-0 items-center ' +
                    'justify-center rounded-full bg-green-100 ' +
                    'text-xl text-green-700',

                icon:
                    'bi bi-check-circle',

                confirmButton:
                    'rounded-lg bg-green-600 px-5 py-2.5 ' +
                    'text-sm font-semibold text-white transition ' +
                    'hover:bg-green-700 focus:outline-none ' +
                    'focus:ring-2 focus:ring-green-200',
            },
        };

        /**
         * Menutup modal dan mengembalikan hasil pilihan.
         */
        function closeModal(result) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            modal.setAttribute('aria-hidden', 'true');

            document.body.classList.remove('overflow-hidden');

            if (previousFocusedElement) {
                previousFocusedElement.focus();
            }

            if (modalResolver) {
                modalResolver(result);
                modalResolver = null;
            }
        }

        /**
         * Membuka modal global.
         */
        function openModal(options = {}) {
            const type = options.type ?? 'warning';
            const style = modalStyles[type]
                ?? modalStyles.warning;

            previousFocusedElement =
                document.activeElement;

            cancelAllowed = options.showCancel !== false;

            titleElement.textContent =
                options.title ?? 'Konfirmasi';

            messageElement.textContent =
                options.message ?? '';

            confirmButton.textContent =
                options.confirmText ?? 'Lanjutkan';

            cancelButton.textContent =
                options.cancelText ?? 'Batal';

            iconWrapper.className =
                style.iconWrapper;

            iconElement.className =
                style.icon;

            confirmButton.className =
                style.confirmButton;

            cancelButton.classList.toggle(
                'hidden',
                ! cancelAllowed
            );

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            modal.setAttribute('aria-hidden', 'false');

            document.body.classList.add('overflow-hidden');

            window.setTimeout(() => {
                confirmButton.focus();
            }, 50);

            return new Promise((resolve) => {
                modalResolver = resolve;
            });
        }

        /**
         * Menampilkan modal konfirmasi.
         */
        window.appConfirm = function (
            message,
            options = {}
        ) {
            return openModal({
                title: options.title ?? 'Konfirmasi',
                message,
                type: options.type ?? 'warning',
                confirmText:
                    options.confirmText ?? 'Lanjutkan',
                cancelText:
                    options.cancelText ?? 'Batal',
                showCancel: true,
            });
        };

        /**
         * Menampilkan modal pemberitahuan.
         */
        window.appAlert = function (
            message,
            options = {}
        ) {
            return openModal({
                title:
                    options.title ?? 'Pemberitahuan',
                message,
                type: options.type ?? 'info',
                confirmText:
                    options.confirmText ?? 'Mengerti',
                showCancel: false,
            });
        };

        /**
         * Menangani tombol konfirmasi.
         */
        confirmButton.addEventListener('click', () => {
            closeModal(true);
        });

        /**
         * Menangani tombol batal.
         */
        cancelButton.addEventListener('click', () => {
            closeModal(false);
        });

        /**
         * Menutup modal melalui latar belakang.
         */
        backdrop.addEventListener('click', () => {
            closeModal(cancelAllowed ? false : true);
        });

        /**
         * Menangani tombol Escape.
         */
        document.addEventListener('keydown', (event) => {
            if (
                event.key !== 'Escape'
                || modal.classList.contains('hidden')
            ) {
                return;
            }

            closeModal(cancelAllowed ? false : true);
        });

        /**
         * Menangani seluruh form yang membutuhkan
         * konfirmasi tanpa confirm bawaan browser.
         */
        document.addEventListener(
            'submit',
            async (event) => {
                const form = event.target;

                if (
                    ! (form instanceof HTMLFormElement)
                    || ! form.dataset.confirm
                ) {
                    return;
                }

                if (
                    form.dataset.confirmApproved
                    === 'true'
                ) {
                    delete form.dataset.confirmApproved;
                    return;
                }

                event.preventDefault();

                const confirmed = await window.appConfirm(
                    form.dataset.confirm,
                    {
                        title:
                            form.dataset.confirmTitle
                            ?? 'Konfirmasi',

                        type:
                            form.dataset.confirmType
                            ?? 'warning',

                        confirmText:
                            form.dataset.confirmButton
                            ?? 'Lanjutkan',

                        cancelText:
                            form.dataset.cancelButton
                            ?? 'Batal',
                    }
                );

                if (! confirmed) {
                    return;
                }

                form.dataset.confirmApproved = 'true';

                form.requestSubmit(
                    event.submitter ?? undefined
                );
            }
        );
    })();
</script>
