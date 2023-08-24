<?php

require_once __DIR__ . '/../infra/amqp/amqp.php';
require_once __DIR__ . '/../infra/postgres/repository/subscriber_repository.php';

/**
 * Команда запускается кроном
 * получаем подписчиков valid_ts < now() and valid=1 or confirmed is true
 * кидаем в очередь на отправку писем об окончании подписки
 */
(function () {
    foreach (get_subscribes_with_soon_expiring_subscription() as $s) {
        $text = sprintf('%s, your subscription is expired', $s->username);

        publish(EMAIL_EVENT_EXCHANGE, SEND_EMAIL_KEY, json_encode([
            'id' => $s->id,
            'text' => $text,
            'email' => $s->email,
        ]));
    }
})();
