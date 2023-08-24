<?php

use PhpAmqpLib\Message\AMQPMessage;

require_once __DIR__ . '/../infra/console.php';
require_once __DIR__ . '/../infra/email/email-service.php';
require_once __DIR__ . '/../infra/amqp/amqp.php';

/**
 * TODO Уместно сделать логирование отправки с сохранением статуса отправки, чтобы в дальнейшем ориентироваться о необходимости валидации email
 */
(function () {
    consume(SEND_EMAIL_QUEUE, function (AMQPMessage $msg) {
        try {
            $message = json_decode(json: $msg->body, flags: JSON_THROW_ON_ERROR);
            //TODO logging in progress
            send_email(EMAIL_SENDER, $message->email, $message->text);
            //TODO logging success || fail
            $msg->ack();
        } catch (Throwable $e) {
            console_info('Got consume error: %s, message: %s', $e->getMessage(), $msg);
            $msg->reject();
        }
    });
})();
