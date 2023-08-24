<?php

use PhpAmqpLib\Message\AMQPMessage;

require_once __DIR__ . '/../infra/console.php';
require_once __DIR__ . '/../infra/email/email-service.php';
require_once __DIR__ . '/../infra/amqp/amqp.php';
require_once __DIR__ . '/../infra/postgres/repository/user_repository.php';
require_once __DIR__ . '/../infra/enum.php';

/**
 * Обрабатываем очередь CHECK_EMAIL_QUEUE
 * Валидируем email и обновляем в мастере состояние checked + valid
 */
(function () {
    console_info('Validate email consumer is started');

    consume(CHECK_EMAIL_QUEUE, function (AMQPMessage $msg) {
        try {
            $subscriber = json_decode($msg->body);

            update_by_id($subscriber->id, [
                'checked' => CheckStatus::InProgress->value,
            ]);

            $isValid = check_email($subscriber->email);

            console_info(
                'User Id:[%d] email:[%s] valid: [%s]',
                $subscriber->id,
                $subscriber->email,
                $isValid === 1 ? 'true' : 'false'
            );

            update_by_id($subscriber->id, [
                'checked' => CheckStatus::Checked->value,
                'valid' => $isValid
            ]);

            $msg->ack();
        } catch (Throwable $e) {
            console_err('Got consume error: %s, message: %s', $e->getMessage(), $msg->body);
            // todo reject or requeue or delayed message
        }
    });
})();
