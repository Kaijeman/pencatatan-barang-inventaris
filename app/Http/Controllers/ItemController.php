<?php

namespace App\Http\Controllers;

use App\Http\Requests\Item\StoreItemRequest;
use App\Http\Requests\Item\UpdateItemRequest;
use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use App\Notifications\ItemCreatedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;
use Throwable;

class ItemController extends Controller
{
    /**
     * Menampilkan daftar barang.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));
        $categoryId = $request->input('category_id');
        $stockStatus = $request->input('stock_status');

        $items = Item::query()
            ->with('category')
            ->when(
                $search !== '',
                function ($query) use ($search): void {
                    $query->where(
                        function ($query) use ($search): void {
                            $query
                                ->where(
                                    'code',
                                    'like',
                                    '%' . $search . '%'
                                )
                                ->orWhere(
                                    'name',
                                    'like',
                                    '%' . $search . '%'
                                )
                                ->orWhere(
                                    'unit',
                                    'like',
                                    '%' . $search . '%'
                                )
                                ->orWhereHas(
                                    'category',
                                    function ($categoryQuery) use (
                                        $search
                                    ): void {
                                        $categoryQuery->where(
                                            'name',
                                            'like',
                                            '%' . $search . '%'
                                        );
                                    }
                                );
                        }
                    );
                }
            )
            ->when(
                $categoryId,
                function ($query) use ($categoryId): void {
                    $query->where(
                        'category_id',
                        $categoryId
                    );
                }
            )
            ->when(
                $stockStatus === 'low',
                function ($query): void {
                    $query
                        ->where('stock', '>', 0)
                        ->whereColumn(
                            'stock',
                            '<=',
                            'minimum_stock'
                        );
                }
            )
            ->when(
                $stockStatus === 'out',
                function ($query): void {
                    $query->where('stock', 0);
                }
            )
            ->when(
                $stockStatus === 'available',
                function ($query): void {
                    $query->whereColumn(
                        'stock',
                        '>',
                        'minimum_stock'
                    );
                }
            )
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $categories = Category::query()
            ->orderBy('name')
            ->get([
                'id',
                'name',
            ]);

        return view('items.index', compact(
            'items',
            'categories',
            'search',
            'categoryId',
            'stockStatus'
        ));
    }

    /**
     * Menampilkan form tambah barang.
     */
    public function create(): View
    {
        $categories = Category::query()
            ->orderBy('name')
            ->get([
                'id',
                'name',
            ]);

        return view('items.create', compact(
            'categories'
        ));
    }

    /**
     * Menyimpan barang baru.
     */
    public function store(
        StoreItemRequest $request
    ): RedirectResponse {
        $validated = $request->validated();

        $item = Item::create([
            'category_id' => $validated['category_id'],
            'code' => $validated['code'],
            'name' => $validated['name'],
            'unit' => $validated['unit'],
            'purchase_price' =>
                $validated['purchase_price'],
            'stock' => 0,
            'minimum_stock' =>
                $validated['minimum_stock'],
            'description' =>
                $validated['description'] ?? null,
        ]);

        /**
         * Memuat kategori yang digunakan oleh notifikasi.
         */
        $item->load('category:id,name');

        $notificationQueued =
            $this->queueItemCreatedNotification($item);

        if ($notificationQueued) {
            return redirect()
                ->route('items.index')
                ->with(
                    'success',
                    'Barang berhasil ditambahkan.'
                );
        }

        return redirect()
            ->route('items.index')
            ->with(
                'error',
                'Barang berhasil ditambahkan, tetapi notifikasi email gagal diproses.'
            );
    }

    /**
     * Menampilkan form edit barang.
     */
    public function edit(Item $item): View
    {
        $categories = Category::query()
            ->orderBy('name')
            ->get([
                'id',
                'name',
            ]);

        return view('items.edit', compact(
            'item',
            'categories'
        ));
    }

    /**
     * Memperbarui barang.
     */
    public function update(
        UpdateItemRequest $request,
        Item $item
    ): RedirectResponse {
        $item->update($request->validated());

        return redirect()
            ->route('items.index')
            ->with(
                'success',
                'Barang berhasil diperbarui.'
            );
    }

    /**
     * Menghapus barang.
     */
    public function destroy(Item $item): RedirectResponse
    {
        /**
         * Memeriksa seluruh riwayat transaksi barang.
         */
        $hasTransactionHistory =
            $item->receiptDetails()->exists()
            || $item->issueDetails()->exists()
            || $item->stockOpnames()->exists();

        if ($hasTransactionHistory) {
            return redirect()
                ->route('items.index')
                ->with(
                    'error',
                    'Barang tidak dapat dihapus karena sudah memiliki riwayat transaksi.'
                );
        }

        /**
         * Mencegah barang dengan stok tersisa dihapus.
         */
        if ((int) $item->stock > 0) {
            return redirect()
                ->route('items.index')
                ->with(
                    'error',
                    'Barang tidak dapat dihapus karena stoknya masih tersedia.'
                );
        }

        $item->delete();

        return redirect()
            ->route('items.index')
            ->with(
                'success',
                'Barang berhasil dihapus.'
            );
    }

    /**
     * Memasukkan notifikasi barang baru ke antrean.
     */
    private function queueItemCreatedNotification(
        Item $item
    ): bool {
        try {
            $recipients =
                $this->getNotificationRecipients();

            /**
             * Menghentikan proses jika tidak ada penerima.
             */
            if ($recipients->isEmpty()) {
                Log::warning(
                    'Notifikasi barang baru tidak memiliki penerima.',
                    [
                        'item_id' => $item->id,
                    ]
                );

                return false;
            }

            /**
             * Memasukkan notifikasi ke database queue.
             */
            Notification::send(
                $recipients,
                new ItemCreatedNotification(
                    $item,
                    auth()->user()->name
                )
            );

            return true;
        } catch (Throwable $exception) {
            Log::error(
                'Notifikasi barang baru gagal dimasukkan ke antrean.',
                [
                    'item_id' => $item->id,
                    'exception_class' =>
                        $exception::class,
                    'message' =>
                        $exception->getMessage(),
                ]
            );

            report($exception);

            return false;
        }
    }

    /**
     * Mendapatkan kepala gudang dan pembuat data.
     */
    private function getNotificationRecipients()
    {
        return User::query()
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->where('email', 'not like', '%.test')
            ->where(
                function ($query): void {
                    $query
                        ->where(
                            'role',
                            'kepala_gudang'
                        )
                        ->orWhere(
                            'id',
                            auth()->id()
                        );
                }
            )
            ->get()
            ->unique('email')
            ->values();
    }
}
