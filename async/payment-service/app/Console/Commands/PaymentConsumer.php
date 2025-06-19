<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Illuminate\Support\Facades\Log;
// Import model Payment
use App\Models\Payment;

class PaymentConsumer extends Command
{
    protected $signature = 'rabbitmq:consume-payments';
    protected $description = 'Listen for new order messages to prepare for payment';

    public function handle()
{
    $this->info('Consumer worker is starting...');

    try {
        $connection = new AMQPStreamConnection(
            config('services.rabbitmq.host'), config('services.rabbitmq.port'), config('services.rabbitmq.user'), config('services.rabbitmq.password'), '/', false, 'AMQPLAIN', null, 'en_US', 30.0, 60.0, null, false, 30
        );
        $channel = $connection->channel();
        $exchangeName = 'order_exchange';
        $channel->exchange_declare($exchangeName, 'direct', false, true, false);
        $queueName = 'payment_queue';
        $channel->queue_declare($queueName, false, true, false, false);

        // ++ PERBAIKAN: Buat agar worker mendengarkan DUA pesan ++
        $bindingKeys = ['order.created', 'payment.created'];
        foreach ($bindingKeys as $bindingKey) {
            $channel->queue_bind($queueName, $exchangeName, $bindingKey);
        }

        $this->info('[*] Waiting for messages (new orders or new payments)...');

        $callback = function ($msg) use ($channel) {
            $routingKey = $msg->delivery_info['routing_key'];
            $this->info(" [x] Received [{$routingKey}]: " . $msg->body);
            $data = json_decode($msg->body, true);

            // Logika untuk setiap jenis pesan
            if ($routingKey === 'order.created') {
                $this->line("   - Initializing payment process for Order ID: {$data['order_id']}...");
                
                // Kirim pesan balasan 'order.processing'
                $rabbitmqService = app(\App\Services\RabbitMQService::class);
                $replyMessage = json_encode(['order_id' => $data['order_id']]);
                $rabbitmqService->publish($replyMessage, 'order.processing');
                $this->info("   - [✔] Sent 'order.processing' message back.");

            } elseif ($routingKey === 'payment.created') {
                $this->line("   - [✔] Confirmed: Payment with ID {$data['id']} for Order ID {$data['order_id']} has been created.");
            }
            
            $channel->basic_ack($msg->delivery_info['delivery_tag']);
        };

        $channel->basic_consume($queueName, '', false, false, false, false, $callback);

        while (count($channel->callbacks)) {
            $channel->wait();
        }
        $channel->close();
        $connection->close();
    } catch (\Exception $e) {
        $this->error('RabbitMQ Consumer Error: ' . $e->getMessage());
        Log::error('RabbitMQ Consumer Error: ' . $e->getMessage());
    }
}
}