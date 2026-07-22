<?php

namespace App\Notifications;

use App\Models\GoodsReceipt;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GoodsReceiptCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 60;

    /**
     * Membuat notifikasi barang masuk.
     */
    public function __construct(
        public GoodsReceipt $goodsReceipt
    ) {
        $this->afterCommit();
    }

    /**
     * Menentukan channel notifikasi.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Menentukan queue notifikasi.
     *
     * @return array<string, string>
     */
    public function viaQueues(): array
    {
        return [
            'mail' => 'emails',
        ];
    }

    /**
     * Menentukan jeda percobaan ulang.
     */
    public function backoff(): int
    {
        return 30;
    }

    /**
     * Membuat isi email barang masuk.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $this->goodsReceipt->loadMissing([
            'supplier:id,name',
            'user:id,name',
            'details',
        ]);

        $totalQuantity = (int) $this->goodsReceipt
            ->details
            ->sum('quantity');

        $totalValue = (float) $this->goodsReceipt
            ->details
            ->sum(
                fn ($detail): float =>
                    (float) (
                        $detail->quantity
                        * $detail->purchase_price
                    )
            );

        return (new MailMessage)
            ->mailer('smtp')
            ->subject(
                'Transaksi Barang Masuk - '
                . $this->goodsReceipt
                    ->received_at
                    ->format('d/m/Y')
            )
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line(
                'Transaksi barang masuk telah berhasil dicatat.'
            )
            ->line(
                'Tanggal: '
                . $this->goodsReceipt
                    ->received_at
                    ->format('d/m/Y')
            )
            ->line(
                'Supplier: '
                . $this->goodsReceipt->supplier->name
            )
            ->line(
                'Jumlah Jenis Barang: '
                . number_format(
                    $this->goodsReceipt->details->count()
                )
            )
            ->line(
                'Total Barang Masuk: '
                . number_format($totalQuantity)
                . ' unit'
            )
            ->line(
                'Total Nilai: Rp'
                . number_format(
                    $totalValue,
                    0,
                    ',',
                    '.'
                )
            )
            ->line(
                'Dicatat Oleh: '
                . $this->goodsReceipt->user->name
            )
            ->action(
                'Lihat Detail Barang Masuk',
                route(
                    'goods-receipts.show',
                    $this->goodsReceipt
                )
            )
            ->line(
                'Email ini dikirim otomatis oleh sistem inventaris gudang.'
            );
    }

    /**
     * Membuat data notifikasi.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'goods_receipt_id' =>
                $this->goodsReceipt->id,
            'received_at' =>
                $this->goodsReceipt
                    ->received_at
                    ->format('Y-m-d'),
            'supplier_name' =>
                $this->goodsReceipt->supplier->name,
        ];
    }
}
