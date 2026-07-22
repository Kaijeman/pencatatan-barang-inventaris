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

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (! empty($validated['password'])) {
            $data['password'] = Hash::make(
                $validated['password']
            );
        }

        $user->update($data);

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
        if ($user->is(auth()->user())) {
            return redirect()
                ->route('users.index')
                ->with(
                    'error',
                    'Akun yang sedang digunakan tidak dapat dihapus.'
                );
        }

        $hasTransactionHistory =
            $user->goodsReceipts()->exists()
            || $user->goodsIssues()->exists();

        if ($hasTransactionHistory) {
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
}
