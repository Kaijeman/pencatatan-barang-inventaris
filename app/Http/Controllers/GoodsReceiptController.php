<?php

namespace App\Http\Controllers;

use App\Http\Requests\GoodsReceipt\StoreGoodsReceiptRequest;
use App\Models\GoodsReceipt;
use App\Models\Item;
use App\Models\Supplier;
use App\Models\User;
use App\Notifications\GoodsReceiptCreatedNotification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;
use Throwable;

class GoodsReceiptController extends Controller
{
    /**
     * Menampilkan daftar transaksi barang masuk.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));
        $date = $request->input('date');

        $receipts = GoodsReceipt::query()
            ->with([
                'supplier:id,name',
                'user:id,name',
            ])
            ->withCount('details')
            ->withSum('details', 'quantity')
            ->when(
                $search !== '',
                function ($query) use ($search): void {
                    $query->where(
                        function ($query) use ($search): void {
                            $query
                                ->where(
                                    'note',
                                    'like',
                                    '%' . $search . '%'
                                )
                                ->orWhere(
                                    'recorded_by_name',
                                    'like',
                                    '%' . $search . '%'
                                )
                                ->orWhereHas(
                                    'supplier',
                                    function ($supplierQuery) use (
                                        $search
                                    ): void {
                                        $supplierQuery->where(
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
                $date,
                function ($query) use ($date): void {
                    $query->whereDate(
                        'received_at',
                        $date
                    );
                }
            )
            ->orderByDesc('received_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('goods_receipts.index', compact(
            'receipts',
            'search',
            'date'
        ));
    }

    /**
     * Menampilkan form transaksi barang masuk.
     */
    public function create(): View
    {
        $suppliers = Supplier::query()
            ->orderBy('name')
            ->get([
                'id',
                'name',
            ]);

        $items = Item::query()
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'unit',
                'purchase_price',
                'stock',
            ]);

        return view('goods_receipts.create', compact(
            'suppliers',
            'items'
        ));
    }

    /**
     * Menyimpan transaksi barang masuk.
     */
    public function store(
        StoreGoodsReceiptRequest $request
    ): RedirectResponse {
        $validated = $request->validated();

        /**
         * Menyimpan data pengguna sebelum masuk
         * ke dalam closure transaksi database.
         */
        $authenticatedUser = $request->user();

        $receipt = DB::transaction(
            function () use (
                $validated,
                $authenticatedUser
            ): GoodsReceipt {
                /**
                 * Membuat transaksi barang masuk.
                 */
                $receipt = GoodsReceipt::create([
                    'supplier_id' =>
                        $validated['supplier_id'],

                    'user_id' =>
                        $authenticatedUser->id,

                    'recorded_by_name' =>
                        $authenticatedUser->name,

                    'received_at' =>
                        $validated['received_at'],

                    'note' =>
                        $validated['note'] ?? null,
                ]);

                /**
                 * Menyimpan setiap detail barang masuk.
                 */
                foreach ($validated['items'] as $detail) {
                    $item = Item::query()
                        ->lockForUpdate()
                        ->findOrFail($detail['item_id']);

                    $quantity =
                        (int) $detail['quantity'];

                    $purchasePrice =
                        $detail['purchase_price'];

                    /**
                     * Menyimpan detail barang masuk.
                     */
                    $receipt->details()->create([
                        'item_id' => $item->id,
                        'quantity' => $quantity,
                        'purchase_price' => $purchasePrice,
                    ]);

                    /**
                     * Menambah stok dan memperbarui
                     * harga beli terakhir barang.
                     */
                    $item->update([
                        'stock' =>
                            (int) $item->stock
                            + $quantity,

                        'purchase_price' =>
                            $purchasePrice,
                    ]);
                }

                return $receipt;
            },
            3
        );

        /**
         * Memuat data yang diperlukan oleh halaman
         * detail dan notifikasi.
         */
        $receipt->load([
            'supplier:id,name',
            'user:id,name',
            'details',
        ]);

        $this->queueGoodsReceiptNotification(
            $receipt
        );

        return redirect()
            ->route('goods-receipts.show', $receipt)
            ->with(
                'success',
                'Transaksi barang masuk berhasil disimpan.'
            );
    }

    /**
     * Menampilkan detail transaksi barang masuk.
     */
    public function show(
        GoodsReceipt $goodsReceipt
    ): View {
        $goodsReceipt->load([
            'supplier',
            'user',
            'details.item.category',
        ]);

        $totalQuantity = $goodsReceipt
            ->details
            ->sum('quantity');

        $totalValue = $goodsReceipt
            ->details
            ->sum(
                fn ($detail): float =>
                    (float) (
                        $detail->quantity
                        * $detail->purchase_price
                    )
            );

        return view('goods_receipts.show', compact(
            'goodsReceipt',
            'totalQuantity',
            'totalValue'
        ));
    }

    /**
     * Memasukkan notifikasi barang masuk ke antrean.
     */
    private function queueGoodsReceiptNotification(
        GoodsReceipt $receipt
    ): bool {
        try {
            $recipients =
                $this->getNotificationRecipients();

            if ($recipients->isEmpty()) {
                Log::warning(
                    'Notifikasi barang masuk tidak memiliki penerima.',
                    [
                        'receipt_id' => $receipt->id,
                    ]
                );

                return false;
            }

            Notification::send(
                $recipients,
                new GoodsReceiptCreatedNotification(
                    $receipt
                )
            );

            return true;
        } catch (Throwable $exception) {
            Log::error(
                'Notifikasi barang masuk gagal diproses.',
                [
                    'receipt_id' => $receipt->id,
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
     * Mendapatkan seluruh pengguna penerima notifikasi.
     */
    private function getNotificationRecipients(): Collection
    {
        return User::query()
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->where('email', 'not like', '%.test')
            ->get()
            ->unique('email')
            ->values();
    }
}
