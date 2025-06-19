<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use App\Models\Order; // Pastikan model Order di-import

class UpdateOrderStatus extends Command
{
    protected $signature = 'rabbitmq:listen-status-updates';
    protected $description = 'Listen for order status updates from other services';

    public function handle()
    {
        // Konfigurasi koneksi sama seperti consumer sebelumnya
        $connection = new AMQPStreamConnection(config('services.rabbitmq.host'), config('services.rabbitmq.port'), config('services.rabbitmq.user'), config('services.rabbitmq.password'), '/', false, 'AMQPLAIN', null, 'en_US', 30.0, 60.0, null, false, 30);
        $channel = $connection->channel();
        $exchangeName = 'order_exchange';
        $channel->exchange_declare($exchangeName, 'direct', false, true, false);

        list($queueName, ,) = $channel->queue_declare("", false, false, true, false);

        $bindingKeys = ['order.processing', 'payment.completed'];

        foreach ($bindingKeys as $bindingKey) {
            $channel->queue_bind($queueName, $exchangeName, $bindingKey);
        }

        $this->info('[*] Waiting for order status updates. To exit press CTRL+C');

        $callback = function ($msg) use ($channel) {
            $routingKey = $msg->delivery_info['routing_key'];
            
            $this->info(" [x] Received [{$routingKey}]: " . $msg->body);
            $data = json_decode($msg->body, true);
            $order = Order::find($data['order_id']);

            if ($order) {
                $newStatus = '';
                if ($routingKey === 'order.processing') {
                    $newStatus = 'processing';
                } elseif ($routingKey === 'payment.completed') {
                    $newStatus = 'paid';
                }

                if ($newStatus) {
                    $order->status = $newStatus;
                    $order->save();
                    $this->info("   - [✔] Order {$order->id} status updated to '{$newStatus}'");
                }
            }
            $channel->basic_ack($msg->delivery_info['delivery_tag']);
        };

        $channel->basic_consume($queueName, '', false, false, false, false, $callback);

        while (count($channel->callbacks)) {
            $channel->wait();
        }
        $channel->close();
        $connection->close();
    }
}