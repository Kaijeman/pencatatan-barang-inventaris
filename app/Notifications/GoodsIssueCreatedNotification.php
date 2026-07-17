<?php

namespace App\Notifications;

use App\Models\GoodsIssue;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GoodsIssueCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Jumlah maksimal percobaan pengiriman notifikasi.
     */
    public int $tries = 3;

    /**
     * Batas waktu pemrosesan notifikasi dalam detik.
     */
    public int $timeout = 60;

    /**
     * Membuat instance notifikasi barang keluar.
     */
    public function __construct(
        public GoodsIssue $goodsIssue
    ) {
        /**
         * Memastikan notifikasi diproses setelah transaksi selesai.
         */
        $this->afterCommit();
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
     * Menentukan nama queue untuk setiap channel.
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
     * Menentukan jeda sebelum percobaan ulang.
     */
    public function backoff(): int
    {
        return 30;
    }

    /**
     * Membuat isi notifikasi email barang keluar.
     */
    public function toMail(object $notifiable): MailMessage
    {
        /**
         * Memuat relasi yang diperlukan dalam email.
         */
        $this->goodsIssue->loadMissing([
            'user:id,name',
            'details',
        ]);

        $totalQuantity = (int) $this->goodsIssue
            ->details
            ->sum('quantity');

        return (new MailMessage)
            ->subject(
                'Barang Keluar - '
                . $this->goodsIssue->issue_number
            )
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line(
                'Transaksi barang keluar telah berhasil dicatat.'
            )
            ->line(
                'Nomor Transaksi: '
                . $this->goodsIssue->issue_number
            )
            ->line(
                'Tanggal: '
                . $this->goodsIssue
                    ->issued_at
                    ->format('d/m/Y')
            )
            ->line(
                'Tujuan: '
                . $this->goodsIssue->destination
            )
            ->line(
                'Jumlah Jenis Barang: '
                . number_format(
                    $this->goodsIssue->details->count()
                )
            )
            ->line(
                'Total Barang Keluar: '
                . number_format($totalQuantity)
                . ' unit'
            )
            ->line(
                'Dicatat Oleh: '
                . $this->goodsIssue->user->name
            )
            ->action(
                'Lihat Detail Barang Keluar',
                route(
                    'goods-issues.show',
                    $this->goodsIssue
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
            'goods_issue_id' =>
                $this->goodsIssue->id,
            'issue_number' =>
                $this->goodsIssue->issue_number,
            'destination' =>
                $this->goodsIssue->destination,
        ];
    }
}
