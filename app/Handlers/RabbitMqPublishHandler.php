<?php

namespace App\Handlers;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMqPublishHandler
{
    public function publish($message, $queue='', $exchange='', $key='')
    {
        $message = json_encode($message, JSON_UNESCAPED_UNICODE);
        $queue = !empty($queue) ? $queue : config('rabbitmq.queue');
        $exchange = !empty($exchange) ? $exchange : config('rabbitmq.exchange');
        $key = !empty($key) ? $key : config('rabbitmq.key');
        $connection = new AMQPStreamConnection(config('rabbitmq.host'),config('rabbitmq.port'),config('rabbitmq.login'),config('rabbitmq.password'),config('rabbitmq.vhost'));
        $channel = $connection->channel();
        $channel->exchange_declare($exchange, 'direct', false, true, false);
        $channel->queue_declare($queue, false, true, false, false);
        $channel->queue_bind($queue, $exchange, $key);
        $data = new AMQPMessage($message, ['content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_NON_PERSISTENT]);
        $channel->basic_publish($data, $exchange, $key);
        $channel->close();
        $connection->close();
    }
}