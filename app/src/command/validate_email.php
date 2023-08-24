<?php

declare(strict_types=1);

require_once __DIR__ . '/../infra/amqp/amqp.php';
require_once __DIR__ . '/../infra/postgres/repository/subscriber_repository.php';
require_once __DIR__ . '/../infra/postgres/repository/user_repository.php';
require_once __DIR__ . '/../infra/enum.php';

/**
 * Команда запускается кроном
 * получаем подписчиков, у которых истекли подписки
 * valid_ts < now() && checked=not_checked && confirmed is false
 * кидаем в очередь на проверку
 */
(function () {
    foreach (get_subscribers_with_unverified_email() as $s) {
        publish(EMAIL_EVENT_EXCHANGE, CHECK_EMAIL_KEY, json_encode($s));
    }
})();
