<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/config.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

const EMAIL_EVENT_EXCHANGE = 'email_event.exchange';
const SEND_EMAIL_QUEUE = 'send_email.queue';
const CHECK_EMAIL_QUEUE = 'check_email.queue';
const SEND_EMAIL_KEY = 'send_email.key';
const CHECK_EMAIL_KEY = 'check_email.key';

function amqp_connect(): AMQPStreamConnection
{
    $conn = new AMQPStreamConnection(AMQP_HOST, AMQP_PORT, AMQP_USER, AMQP_PASS, connection_timeout: 60, read_write_timeout: 60, keepalive: false, heartbeat: 30);
    $ch = $conn->channel();
    $ch->exchange_declare(EMAIL_EVENT_EXCHANGE, 'direct', false, true, false);
    $ch->queue_declare(SEND_EMAIL_QUEUE, false, true, false, false);
    $ch->queue_declare(CHECK_EMAIL_QUEUE, false, true, false, false);
    $ch->queue_bind(SEND_EMAIL_QUEUE, EMAIL_EVENT_EXCHANGE, SEND_EMAIL_KEY);
    $ch->queue_bind(CHECK_EMAIL_QUEUE, EMAIL_EVENT_EXCHANGE, CHECK_EMAIL_KEY);

    return $conn;
}

function consume(string $queue, Closure|string $func): void
{
    try {
        $conn = amqp_connect();
        $ch = $conn->channel();
        $ch->basic_qos(0, 1, false);
        $ch->basic_consume($queue, callback: $func);
        $ch->consume();
    } catch (Throwable $e) {
        console_err('Got AMQP consume err: %s', $e->getMessage());
    }
}

function publish(string $exchange, string $routingKey, string $msg): void
{
    try {
        $conn = amqp_connect();
        $ch = $conn->channel();
        $ch->basic_publish(new AMQPMessage($msg), $exchange, $routingKey);
        $ch->close();
        $conn->close();
    } catch (Throwable $e) {
        console_err('Got AMQP publish err: %s', $e->getMessage());
    }
}
