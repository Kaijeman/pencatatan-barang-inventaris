<?php

namespace App\Http\Controllers;

use App\Http\Requests\GoodsReceipt\StoreGoodsReceiptRequest;
use App\Models\GoodsReceipt;
use App\Models\Item;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

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
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query
                        ->where(
                            'receipt_number',
                            'like',
                            '%' . $search . '%'
                        )
                        ->orWhereHas(
                            'supplier',
                            function ($supplierQuery) use ($search) {
                                $supplierQuery->where(
                                    'name',
                                    'like',
                                    '%' . $search . '%'
                                );
                            }
                        )
                        ->orWhereHas(
                            'user',
                            function ($userQuery) use ($search) {
                                $userQuery->where(
                                    'name',
                                    'like',
                                    '%' . $search . '%'
                                );
                            }
                        );
                });
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('received_at', $date);
            })
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
            ->get(['id', 'name']);

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

        $receipt = DB::transaction(function () use ($validated) {
            $receipt = GoodsReceipt::create([
                'receipt_number' => $this->generateReceiptNumber(),
                'supplier_id' => $validated['supplier_id'],
                'user_id' => auth()->id(),
                'received_at' => $validated['received_at'],
                'note' => $validated['note'] ?? null,
            ]);

            foreach ($validated['items'] as $detail) {
                $item = Item::query()
                    ->lockForUpdate()
                    ->findOrFail($detail['item_id']);

                $receipt->details()->create([
                    'item_id' => $item->id,
                    'quantity' => $detail['quantity'],
                    'purchase_price' => $detail['purchase_price'],
                ]);

                $item->stock += $detail['quantity'];
                $item->purchase_price = $detail['purchase_price'];
                $item->save();
            }

            return $receipt;
        });

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
    public function show(GoodsReceipt $goodsReceipt): View
    {
        $goodsReceipt->load([
            'supplier',
            'user',
            'details.item.category',
        ]);

        $totalQuantity = $goodsReceipt->details->sum('quantity');

        $totalValue = $goodsReceipt->details->sum(
            function ($detail) {
                return $detail->quantity * $detail->purchase_price;
            }
        );

        return view('goods_receipts.show', compact(
            'goodsReceipt',
            'totalQuantity',
            'totalValue'
        ));
    }

    /**
     * Membuat nomor transaksi barang masuk secara otomatis.
     */
    private function generateReceiptNumber(): string
    {
        $date = now()->format('Ymd');
        $prefix = 'BM-' . $date . '-';

        $lastNumber = GoodsReceipt::query()
            ->where('receipt_number', 'like', $prefix . '%')
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
}
