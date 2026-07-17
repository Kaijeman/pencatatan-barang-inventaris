<?php

namespace App\Notifications;

use App\Models\GoodsReceipt;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GoodsReceiptCreatedNotification extends Notification
{
    use Queueable;

    /**
     * Membuat instance notifikasi barang masuk.
     */
    public function __construct(
        public GoodsReceipt $goodsReceipt
    ) {
    }

    /**
     * Menentukan channel pengiriman notifikasi.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Membuat isi notifikasi email barang masuk.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $totalQuantity = (int) $this->goodsReceipt
            ->details
            ->sum('quantity');

        $totalValue = (float) $this->goodsReceipt
            ->details
            ->sum(function ($detail): float {
                return (float) (
                    $detail->quantity
                    * $detail->purchase_price
                );
            });

        return (new MailMessage)
            ->subject(
                'Barang Masuk - '
                . $this->goodsReceipt->receipt_number
            )
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line(
                'Transaksi barang masuk telah berhasil dicatat.'
            )
            ->line(
                'Nomor Transaksi: '
                . $this->goodsReceipt->receipt_number
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
     * Membuat representasi data notifikasi.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'goods_receipt_id' => $this->goodsReceipt->id,
            'receipt_number' =>
                $this->goodsReceipt->receipt_number,
            'supplier_name' =>
                $this->goodsReceipt->supplier->name,
        ];
    }
}
