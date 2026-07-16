<?php

namespace App\Http\Controllers;

use App\Http\Requests\Supplier\StoreSupplierRequest;
use App\Http\Requests\Supplier\UpdateSupplierRequest;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierController extends Controller
{
    /**
     * Menampilkan daftar supplier.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));

        $suppliers = Supplier::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('phone', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('address', 'like', '%' . $search . '%');
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('suppliers.index', compact('suppliers', 'search'));
    }

    /**
     * Menampilkan form tambah supplier.
     */
    public function create(): View
    {
        return view('suppliers.create');
    }

    /**
     * Menyimpan supplier baru.
     */
    public function store(
        StoreSupplierRequest $request
    ): RedirectResponse {
        Supplier::create($request->validated());

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Supplier berhasil ditambahkan.');
    }

    /**
     * Menampilkan form edit supplier.
     */
    public function edit(Supplier $supplier): View
    {
        return view('suppliers.edit', compact('supplier'));
    }

    /**
     * Memperbarui supplier.
     */
    public function update(
        UpdateSupplierRequest $request,
        Supplier $supplier
    ): RedirectResponse {
        $supplier->update($request->validated());

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Supplier berhasil diperbarui.');
    }

    /**
     * Menghapus supplier.
     */
    public function destroy(Supplier $supplier): RedirectResponse
    {
        if ($supplier->goodsReceipts()->exists()) {
            return redirect()
                ->route('suppliers.index')
                ->with(
                    'error',
                    'Supplier tidak dapat dihapus karena sudah digunakan dalam transaksi barang masuk.'
                );
        }

        $supplier->delete();

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Supplier berhasil dihapus.');
    }
}
