<?php

require_once __DIR__ . '/../infra/amqp/amqp.php';
require_once __DIR__ . '/../infra/postgres/repository/subscriber_repository.php';

/**
 * Команда запускается кроном
 * получаем подписчиков valid_ts - now() = 1 || 3 дня
 * кидаем в очередь на отправку писем об истечении срока
 */
(function () {
    foreach (get_subscribes_with_expired_subscription() as $s) {
        $text = sprintf('%s, your subscription is expiring soon', $s->username);

        publish(EMAIL_EVENT_EXCHANGE, SEND_EMAIL_KEY, json_encode([
            'id' => $s->id,
            'text' => $text,
            'email' => $s->email,
        ]));
    }
})();
