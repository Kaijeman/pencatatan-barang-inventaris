<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StockAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Jumlah maksimal percobaan pengiriman.
     */
    public int $tries = 3;

    /**
     * Batas waktu pemrosesan dalam detik.
     */
    public int $timeout = 60;

    /**
     * Membuat notifikasi peringatan stok.
     *
     * @param array<int, array<string, mixed>> $alerts
     */
    public function __construct(
        public array $alerts,
        public string $sourceReference,
        public string $actorName
    ) {
        /**
         * Menunggu transaksi database selesai sebelum diproses.
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
     * Menentukan queue untuk channel email.
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
     * Membuat isi email peringatan stok.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mailMessage = (new MailMessage)
            ->mailer('smtp')
            ->subject(
                'Peringatan Stok - ' . $this->sourceReference
            )
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line(
                'Terdapat barang yang mencapai kondisi stok menipis atau habis.'
            )
            ->line(
                'Sumber perubahan: ' . $this->sourceReference
            )
            ->line(
                'Diproses oleh: ' . $this->actorName
            );

        /**
         * Menambahkan informasi setiap barang ke dalam email.
         */
        foreach ($this->alerts as $alert) {
            $statusLabel = $alert['status'] === 'out'
                ? 'STOK HABIS'
                : 'STOK MENIPIS';

            $mailMessage
                ->line('----------------------------------------')
                ->line(
                    'Barang: '
                    . $alert['code']
                    . ' - '
                    . $alert['name']
                )
                ->line('Status: ' . $statusLabel)
                ->line(
                    'Stok Sebelumnya: '
                    . number_format(
                        (int) $alert['previous_stock']
                    )
                    . ' '
                    . $alert['unit']
                )
                ->line(
                    'Stok Saat Ini: '
                    . number_format(
                        (int) $alert['current_stock']
                    )
                    . ' '
                    . $alert['unit']
                )
                ->line(
                    'Stok Minimum: '
                    . number_format(
                        (int) $alert['minimum_stock']
                    )
                    . ' '
                    . $alert['unit']
                );
        }

        return $mailMessage
            ->action(
                'Lihat Laporan Stok',
                route('reports.stock')
            )
            ->line(
                'Segera lakukan pemeriksaan atau pengadaan barang apabila diperlukan.'
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
            'alerts' => $this->alerts,
            'source_reference' => $this->sourceReference,
            'actor_name' => $this->actorName,
        ];
    }
}
