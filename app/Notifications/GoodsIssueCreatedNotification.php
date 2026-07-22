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

    public int $tries = 3;

    public int $timeout = 60;

    /**
     * Membuat notifikasi barang keluar.
     */
    public function __construct(
        public GoodsIssue $goodsIssue
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
     * Membuat isi email barang keluar.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $this->goodsIssue->loadMissing([
            'user:id,name',
            'details',
        ]);

        $totalQuantity = (int) $this->goodsIssue
            ->details
            ->sum('quantity');

        return (new MailMessage)
            ->mailer('smtp')
            ->subject(
                'Transaksi Barang Keluar - '
                . $this->goodsIssue
                    ->issued_at
                    ->format('d/m/Y')
            )
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line(
                'Transaksi barang keluar telah berhasil dicatat.'
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
     * Membuat data notifikasi.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'goods_issue_id' => $this->goodsIssue->id,
            'issued_at' =>
                $this->goodsIssue
                    ->issued_at
                    ->format('Y-m-d'),
            'destination' => $this->goodsIssue->destination,
        ];
    }
}
