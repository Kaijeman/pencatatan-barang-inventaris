<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StockOpnameDifferenceNotification extends Notification implements ShouldQueue
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
     * Membuat instance notifikasi selisih stock opname.
     *
     * @param array<string, mixed> $opnameData
     */
    public function __construct(
        public int $stockOpnameId,
        public array $opnameData
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
     * Menentukan jeda sebelum percobaan ulang.
     */
    public function backoff(): int
    {
        return 30;
    }

    /**
     * Membuat isi email selisih stock opname.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $difference = (int) $this->opnameData['difference'];

        $differenceLabel = $difference > 0
            ? 'Stok fisik lebih banyak'
            : 'Stok fisik lebih sedikit';

        $formattedDifference = $difference > 0
            ? '+' . number_format($difference)
            : number_format($difference);

        return (new MailMessage)
            ->mailer('smtp')
            ->subject(
                'Selisih Stock Opname - '
                . $this->opnameData['item_code']
            )
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line(
                'Ditemukan perbedaan antara stok sistem dan stok fisik pada proses stock opname.'
            )
            ->line(
                'Kode Barang: '
                . $this->opnameData['item_code']
            )
            ->line(
                'Nama Barang: '
                . $this->opnameData['item_name']
            )
            ->line(
                'Kategori: '
                . $this->opnameData['category_name']
            )
            ->line(
                'Stok Sistem: '
                . number_format(
                    (int) $this->opnameData['system_stock']
                )
                . ' '
                . $this->opnameData['unit']
            )
            ->line(
                'Stok Fisik: '
                . number_format(
                    (int) $this->opnameData['physical_stock']
                )
                . ' '
                . $this->opnameData['unit']
            )
            ->line(
                'Selisih: '
                . $formattedDifference
                . ' '
                . $this->opnameData['unit']
            )
            ->line(
                'Status: '
                . $differenceLabel
            )
            ->line(
                'Tanggal Opname: '
                . $this->opnameData['opname_date']
            )
            ->line(
                'Petugas: '
                . $this->opnameData['actor_name']
            )
            ->line(
                'Catatan: '
                . $this->opnameData['note']
            )
            ->action(
                'Lihat Detail Stock Opname',
                route(
                    'stock-opnames.show',
                    $this->stockOpnameId
                )
            )
            ->line(
                'Stok barang telah disesuaikan berdasarkan jumlah fisik yang dicatat.'
            )
            ->line(
                'Email ini dikirim otomatis oleh sistem.'
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
            'stock_opname_id' => $this->stockOpnameId,
            'item_code' => $this->opnameData['item_code'],
            'item_name' => $this->opnameData['item_name'],
            'system_stock' => $this->opnameData['system_stock'],
            'physical_stock' => $this->opnameData['physical_stock'],
            'difference' => $this->opnameData['difference'],
            'actor_name' => $this->opnameData['actor_name'],
        ];
    }
}
