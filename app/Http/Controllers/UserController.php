<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Menampilkan daftar pengguna aktif.
     */
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'search' => [
                'nullable',
                'string',
                'max:150',
            ],
        ]);

        $search = trim(
            (string) ($validated['search'] ?? '')
        );

        $users = User::query()
            ->when(
                $search !== '',
                function ($query) use ($search): void {
                    $query->where(
                        function ($query) use ($search): void {
                            $query
                                ->where(
                                    'name',
                                    'like',
                                    '%' . $search . '%'
                                )
                                ->orWhere(
                                    'email',
                                    'like',
                                    '%' . $search . '%'
                                );
                        }
                    );
                }
            )
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('users.index', compact(
            'users',
            'search'
        ));
    }

    /**
     * Menampilkan form tambah pengguna.
     */
    public function create(): View
    {
        return view('users.create');
    }

    /**
     * Menyimpan pengguna baru.
     */
    public function store(
        StoreUserRequest $request
    ): RedirectResponse {
        $validated = $request->validated();

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make(
                $validated['password']
            ),
        ]);

        return redirect()
            ->route('users.index')
            ->with(
                'success',
                'Pengguna baru berhasil ditambahkan.'
            );
    }

    /**
     * Menampilkan form edit akun sendiri.
     */
    public function edit(User $user): View
    {
        $this->ensureOwnAccount($user);

        return view('users.edit', compact('user'));
    }

    /**
     * Memperbarui akun sendiri.
     */
    public function update(
        UpdateUserRequest $request,
        User $user
    ): RedirectResponse {
        $this->ensureOwnAccount($user);

        $validated = $request->validated();

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        /**
         * Memperbarui password hanya jika diisi.
         */
        if (! empty($validated['password'])) {
            $data['password'] = Hash::make(
                $validated['password']
            );
        }

        $user->update($data);

        return redirect()
            ->route('users.edit', $user)
            ->with(
                'success',
                'Akun berhasil diperbarui.'
            );
    }

    /**
     * Menghapus akun sendiri menggunakan soft delete.
     */
    public function destroy(
        Request $request,
        User $user
    ): RedirectResponse {
        $this->ensureOwnAccount($user);

        /**
         * Memastikan password yang dimasukkan
         * adalah password akun yang sedang login.
         */
        $request->validate(
            [
                'current_password' => [
                    'required',
                    'current_password',
                ],
            ],
            [
                'current_password.required' =>
                    'Password saat ini wajib diisi.',

                'current_password.current_password' =>
                    'Password yang dimasukkan tidak sesuai.',
            ]
        );

        /**
         * Mencegah seluruh akun aktif habis.
         */
        if (User::query()->count() <= 1) {
            return back()
                ->withErrors([
                    'current_password' =>
                        'Akun terakhir dalam sistem tidak dapat dihapus.',
                ]);
        }

        /**
         * Menonaktifkan akun tanpa menghapus
         * riwayat transaksi pengguna.
         */
        $user->delete();

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with(
                'status',
                'Akun Anda berhasil dihapus.'
            );
    }

    /**
     * Memastikan pengguna hanya mengelola akunnya sendiri.
     */
    private function ensureOwnAccount(User $user): void
    {
        abort_unless(
            auth()->id() === $user->id,
            403,
            'Anda hanya dapat mengelola akun sendiri.'
        );
    }
}
