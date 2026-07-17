<?php

namespace App\Http\Controllers;

use App\Http\Requests\GoodsReceipt\StoreGoodsReceiptRequest;
use App\Models\GoodsReceipt;
use App\Models\Item;
use App\Models\Supplier;
use App\Models\User;
use App\Notifications\GoodsReceiptCreatedNotification;
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
                                    'receipt_number',
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
                                )
                                ->orWhereHas(
                                    'user',
                                    function ($userQuery) use (
                                        $search
                                    ): void {
                                        $userQuery->where(
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
                'code',
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
     * Menyimpan transaksi barang masuk dan menambah stok.
     */
    public function store(
        StoreGoodsReceiptRequest $request
    ): RedirectResponse {
        $validated = $request->validated();

        $receipt = DB::transaction(
            function () use ($validated): GoodsReceipt {
                $receipt = GoodsReceipt::create([
                    'receipt_number' =>
                        $this->generateReceiptNumber(),
                    'supplier_id' =>
                        $validated['supplier_id'],
                    'user_id' => auth()->id(),
                    'received_at' =>
                        $validated['received_at'],
                    'note' =>
                        $validated['note'] ?? null,
                ]);

                foreach ($validated['items'] as $detail) {
                    $item = Item::query()
                        ->lockForUpdate()
                        ->findOrFail(
                            $detail['item_id']
                        );

                    /**
                     * Menyimpan detail barang masuk.
                     */
                    $receipt->details()->create([
                        'item_id' => $item->id,
                        'quantity' =>
                            $detail['quantity'],
                        'purchase_price' =>
                            $detail['purchase_price'],
                    ]);

                    /**
                     * Menambah stok dan memperbarui harga beli.
                     */
                    $item->stock +=
                        (int) $detail['quantity'];

                    $item->purchase_price =
                        $detail['purchase_price'];

                    $item->save();
                }

                return $receipt;
            },
            3
        );

        /**
         * Memuat relasi yang digunakan dalam notifikasi.
         */
        $receipt->load([
            'supplier:id,name',
            'user:id,name',
            'details',
        ]);

        $notificationQueued =
            $this->queueGoodsReceiptNotification(
                $receipt
            );

        if ($notificationQueued) {
            return redirect()
                ->route(
                    'goods-receipts.show',
                    $receipt
                )
                ->with(
                    'success',
                    'Transaksi barang masuk berhasil disimpan.'
                );
        }

        return redirect()
            ->route(
                'goods-receipts.show',
                $receipt
            )
            ->with(
                'error',
                'Transaksi barang masuk berhasil disimpan, tetapi notifikasi email gagal diproses.'
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
            ->sum(function ($detail): float {
                return (float) (
                    $detail->quantity
                    * $detail->purchase_price
                );
            });

        return view('goods_receipts.show', compact(
            'goodsReceipt',
            'totalQuantity',
            'totalValue'
        ));
    }

    /**
     * Membuat nomor transaksi barang masuk.
     */
    private function generateReceiptNumber(): string
    {
        $date = now()->format('Ymd');
        $prefix = 'BM-' . $date . '-';

        $lastNumber = GoodsReceipt::query()
            ->where(
                'receipt_number',
                'like',
                $prefix . '%'
            )
            ->lockForUpdate()
            ->orderByDesc('receipt_number')
            ->value('receipt_number');

        $nextSequence = $lastNumber
            ? ((int) substr($lastNumber, -4)) + 1
            : 1;

        return $prefix . str_pad(
            (string) $nextSequence,
            4,
            '0',
            STR_PAD_LEFT
        );
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

            /**
             * Menghentikan proses jika tidak ada penerima.
             */
            if ($recipients->isEmpty()) {
                Log::warning(
                    'Notifikasi barang masuk tidak memiliki penerima.',
                    [
                        'receipt_id' => $receipt->id,
                    ]
                );

                return false;
            }

            /**
             * Memasukkan notifikasi ke database queue.
             */
            Notification::send(
                $recipients,
                new GoodsReceiptCreatedNotification(
                    $receipt
                )
            );

            return true;
        } catch (Throwable $exception) {
            Log::error(
                'Notifikasi barang masuk gagal dimasukkan ke antrean.',
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
     * Mendapatkan kepala gudang dan pembuat transaksi.
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
