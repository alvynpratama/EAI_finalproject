<?php

namespace App\Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Illuminate\Support\Facades\Log;

class RabbitMQService
{
    protected $connection;
    protected $channel;

    public function __construct()
    {
        try {
            $this->connection = new AMQPStreamConnection(
                config('services.rabbitmq.host'),
                config('services.rabbitmq.port'),
                config('services.rabbitmq.user'),
                config('services.rabbitmq.password')
            );
            $this->channel = $this->connection->channel();
        } catch (\Exception $e) {
            Log::error('RabbitMQ Connection Error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function publish($message, $routingKey)
    {
        $exchangeName = 'order_exchange';
        $this->channel->exchange_declare($exchangeName, 'direct', false, true, false);
        $msg = new AMQPMessage(
            $message,
            ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]
        );
        $this->channel->basic_publish($msg, $exchangeName, $routingKey);
        Log::info(" [x] Sent [{$routingKey}] message to RabbitMQ: '{$message}'");
    }

    public function __destruct()
    {
        if ($this->channel) {
            $this->channel->close();
        }
        if ($this->connection) {
            $this->connection->close();
        }
    }
}