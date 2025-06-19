<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Services\RabbitMQService; // Kita akan gunakan service yang sudah dibuat
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendOrderToRabbitMQ
{
    protected $rabbitmqService;

    /**
     * Create the event listener.
     */
    public function __construct(RabbitMQService $rabbitmqService)
    {
        // Suntikkan RabbitMQService kita ke dalam listener ini
        $this->rabbitmqService = $rabbitmqService;
    }

    /**
     * Handle the event.
     */
   public function handle(OrderCreated $event): void
{
    // Ambil objek order dari event
    $order = $event->order;

    // Buat pesan yang lengkap dengan semua data yang dibutuhkan
    $messageData = [
        'order_id'    => $order->id,
        'user_id'     => $order->user_id, // <-- DATA YANG HILANG SEBELUMNYA
        'total_price' => $order->total_price,
        // Anda bisa tambahkan data lain jika perlu
        'quantity'    => $order->quantity, 
    ];

    $messageJson = json_encode($messageData);

    try {
        // Kirim dengan routing key 'order.created'
        $this->rabbitmqService->publish($messageJson, 'order.created');
        Log::info('Successfully published complete order to RabbitMQ: ' . $messageJson);
    } catch (\Exception $e) {
        Log::error('Failed to publish order to RabbitMQ: ' . $e->getMessage());
    }
}
}