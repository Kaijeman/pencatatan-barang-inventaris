<?php

namespace App\Notifications;

use App\Models\Item;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ItemCreatedNotification extends Notification
{
    use Queueable;

    /**
     * Membuat instance notifikasi barang baru.
     */
    public function __construct(
        public Item $item,
        public string $actorName
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
                . number_format((int) $this->item->minimum_stock)
                . ' '
                . $this->item->unit
            )
            ->line('Ditambahkan Oleh: ' . $this->actorName)
            ->action(
                'Lihat Data Barang',
                route('items.index')
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
            'item_id' => $this->item->id,
            'item_code' => $this->item->code,
            'item_name' => $this->item->name,
            'actor_name' => $this->actorName,
        ];
    }
}
