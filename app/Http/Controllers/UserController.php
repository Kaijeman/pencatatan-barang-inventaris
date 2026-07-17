<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Menampilkan daftar pengguna.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));

        $requestedRole = $request->input('role');

        $role = in_array(
            $requestedRole,
            [
                'kepala_gudang',
                'staff_gudang',
            ],
            true
        )
            ? $requestedRole
            : null;

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
            ->when(
                $role !== null,
                function ($query) use ($role): void {
                    $query->where('role', $role);
                }
            )
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('users.index', compact(
            'users',
            'search',
            'role'
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
            'role' => $validated['role'],
            'password' => Hash::make(
                $validated['password']
            ),
        ]);

        return redirect()
            ->route('users.index')
            ->with(
                'success',
                'Pengguna berhasil ditambahkan.'
            );
    }

    /**
     * Menampilkan form edit pengguna.
     */
    public function edit(User $user): View
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Memperbarui pengguna.
     */
    public function update(
        UpdateUserRequest $request,
        User $user
    ): RedirectResponse {
        $validated = $request->validated();

        /*
         * Mencegah perubahan role kepala gudang terakhir.
         */
        if (
            $user->role === 'kepala_gudang'
            && $validated['role'] !== 'kepala_gudang'
            && $this->isLastHeadWarehouse($user)
        ) {
            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'Role tidak dapat diubah karena pengguna ini merupakan kepala gudang terakhir.'
                );
        }

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ];

        /*
         * Memperbarui password hanya ketika password baru diisi.
         */
        if (! empty($validated['password'])) {
            $updateData['password'] = Hash::make(
                $validated['password']
            );
        }

        $user->update($updateData);

        return redirect()
            ->route('users.index')
            ->with(
                'success',
                'Pengguna berhasil diperbarui.'
            );
    }

    /**
     * Menghapus pengguna.
     */
    public function destroy(User $user): RedirectResponse
    {
        /*
         * Mencegah pengguna menghapus akun sendiri.
         */
        if ($user->id === auth()->id()) {
            return redirect()
                ->route('users.index')
                ->with(
                    'error',
                    'Anda tidak dapat menghapus akun yang sedang digunakan.'
                );
        }

        /*
         * Mencegah penghapusan kepala gudang terakhir.
         */
        if ($this->isLastHeadWarehouse($user)) {
            return redirect()
                ->route('users.index')
                ->with(
                    'error',
                    'Pengguna tidak dapat dihapus karena merupakan kepala gudang terakhir.'
                );
        }

        /*
         * Mencegah penghapusan pengguna yang memiliki riwayat transaksi.
         */
        if ($this->hasTransactionHistory($user)) {
            return redirect()
                ->route('users.index')
                ->with(
                    'error',
                    'Pengguna tidak dapat dihapus karena memiliki riwayat transaksi.'
                );
        }

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with(
                'success',
                'Pengguna berhasil dihapus.'
            );
    }

    /**
     * Memeriksa apakah pengguna merupakan kepala gudang terakhir.
     */
    private function isLastHeadWarehouse(User $user): bool
    {
        if ($user->role !== 'kepala_gudang') {
            return false;
        }

        return User::query()
            ->where('role', 'kepala_gudang')
            ->where('id', '!=', $user->id)
            ->doesntExist();
    }

    /**
     * Memeriksa riwayat transaksi pengguna.
     */
    private function hasTransactionHistory(User $user): bool
    {
        return $user->goodsReceipts()->exists()
            || $user->goodsIssues()->exists()
            || $user->stockOpnames()->exists();
    }
}
