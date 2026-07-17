<?php

namespace App\Notifications;

use App\Models\Item;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ItemCreatedNotification extends Notification implements ShouldQueue
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
     * Membuat instance notifikasi barang baru.
     */
    public function __construct(
        public Item $item,
        public string $actorName
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
     * Membuat isi notifikasi email barang baru.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(
                'Barang Baru Ditambahkan - ' . $this->item->code
            )
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line(
                'Barang baru telah ditambahkan ke dalam sistem inventaris.'
            )
            ->line('Kode Barang: ' . $this->item->code)
            ->line('Nama Barang: ' . $this->item->name)
            ->line(
                'Kategori: ' . $this->item->category->name
            )
            ->line('Satuan: ' . $this->item->unit)
            ->line(
                'Harga Beli: Rp'
                . number_format(
                    (float) $this->item->purchase_price,
                    0,
                    ',',
                    '.'
                )
            )
            ->line(
                'Stok Minimum: '
                . number_format(
                    (int) $this->item->minimum_stock
                )
                . ' '
                . $this->item->unit
            )
            ->line('Ditambahkan Oleh: ' . $this->actorName)
            ->action(
                'Lihat Data Barang',
                route('items.index')
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
            'item_id' => $this->item->id,
            'item_code' => $this->item->code,
            'item_name' => $this->item->name,
            'actor_name' => $this->actorName,
        ];
    }
}
