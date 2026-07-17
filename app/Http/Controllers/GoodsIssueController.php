<?php

namespace App\Http\Controllers;

use App\Http\Requests\GoodsIssue\StoreGoodsIssueRequest;
use App\Models\GoodsIssue;
use App\Models\Item;
use App\Models\User;
use App\Notifications\GoodsIssueCreatedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class GoodsIssueController extends Controller
{
    /**
     * Menampilkan daftar transaksi barang keluar.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));
        $date = $request->input('date');

        $issues = GoodsIssue::query()
            ->with([
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
                                    'issue_number',
                                    'like',
                                    '%' . $search . '%'
                                )
                                ->orWhere(
                                    'destination',
                                    'like',
                                    '%' . $search . '%'
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
                        'issued_at',
                        $date
                    );
                }
            )
            ->orderByDesc('issued_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('goods_issues.index', compact(
            'issues',
            'search',
            'date'
        ));
    }

    /**
     * Menampilkan form transaksi barang keluar.
     */
    public function create(): View
    {
        $items = Item::query()
            ->with('category:id,name')
            ->orderBy('name')
            ->get([
                'id',
                'category_id',
                'code',
                'name',
                'unit',
                'stock',
            ]);

        $hasAvailableItems = $items->contains(
            fn (Item $item): bool =>
                (int) $item->stock > 0
        );

        return view('goods_issues.create', compact(
            'items',
            'hasAvailableItems'
        ));
    }

    /**
     * Menyimpan transaksi barang keluar dan mengurangi stok.
     */
    public function store(
        StoreGoodsIssueRequest $request
    ): RedirectResponse {
        $validated = $request->validated();

        $issue = DB::transaction(
            function () use ($validated): GoodsIssue {
                $issue = GoodsIssue::create([
                    'issue_number' =>
                        $this->generateIssueNumber(),
                    'user_id' => auth()->id(),
                    'destination' =>
                        $validated['destination'],
                    'issued_at' =>
                        $validated['issued_at'],
                    'note' =>
                        $validated['note'] ?? null,
                ]);

                foreach (
                    $validated['items'] as $index => $detail
                ) {
                    $item = Item::query()
                        ->lockForUpdate()
                        ->findOrFail($detail['item_id']);

                    $requestedQuantity =
                        (int) $detail['quantity'];

                    $availableStock =
                        (int) $item->stock;

                    if (
                        $requestedQuantity
                        > $availableStock
                    ) {
                        throw ValidationException::withMessages([
                            "items.$index.quantity" =>
                                "Stok {$item->name} hanya tersedia "
                                . "{$availableStock} {$item->unit}.",
                        ]);
                    }

                    $issue->details()->create([
                        'item_id' => $item->id,
                        'quantity' =>
                            $requestedQuantity,
                    ]);

                    $item->stock =
                        $availableStock
                        - $requestedQuantity;

                    $item->save();
                }

                return $issue;
            },
            3
        );

        $issue->load([
            'user:id,name',
            'details',
        ]);

        $mailSent = $this
            ->sendGoodsIssueNotification($issue);

        $message = $mailSent
            ? 'Transaksi barang keluar berhasil disimpan.'
            : 'Transaksi barang keluar berhasil disimpan, tetapi email notifikasi gagal dikirim.';

        return redirect()
            ->route(
                'goods-issues.show',
                $issue
            )
            ->with('success', $message);
    }

    /**
     * Menampilkan detail transaksi barang keluar.
     */
    public function show(GoodsIssue $goodsIssue): View
    {
        $goodsIssue->load([
            'user',
            'details.item.category',
        ]);

        $totalQuantity = $goodsIssue
            ->details
            ->sum('quantity');

        return view('goods_issues.show', compact(
            'goodsIssue',
            'totalQuantity'
        ));
    }

    /**
     * Membuat nomor transaksi barang keluar.
     */
    private function generateIssueNumber(): string
    {
        $date = now()->format('Ymd');
        $prefix = 'BK-' . $date . '-';

        $lastNumber = GoodsIssue::query()
            ->where(
                'issue_number',
                'like',
                $prefix . '%'
            )
            ->lockForUpdate()
            ->orderByDesc('issue_number')
            ->value('issue_number');

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
     * Mengirim notifikasi email barang keluar.
     */
    private function sendGoodsIssueNotification(
        GoodsIssue $issue
    ): bool {
        try {
            $recipients = $this
                ->getNotificationRecipients();

            Notification::send(
                $recipients,
                new GoodsIssueCreatedNotification(
                    $issue
                )
            );

            return true;
        } catch (Throwable $exception) {
            report($exception);

            return false;
        }
    }

    /**
     * Mendapatkan kepala gudang dan pengguna pembuat transaksi.
     */
    private function getNotificationRecipients()
    {
        return User::query()
            ->whereNotNull('email')
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
            ->get();
    }
}
