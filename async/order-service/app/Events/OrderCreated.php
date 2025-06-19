<?php

namespace App\Events;

use App\Models\Order; // <-- Import model Order
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order; // <-- Kita simpan seluruh objek order

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order) // <-- Terima objek Order
    {
        $this->order = $order;
    }
}