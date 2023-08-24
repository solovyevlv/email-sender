<?php

declare(strict_types=1);

require_once __DIR__ . '/../console.php';
require_once __DIR__ . '/config.php';

function send_email(string $from, string $to, string $text): void
{
    $sleep = rand(1, 10);
    sleep($sleep);
    console_info('send email from: %s to: %s at %d seconds | text: %s', $from, $to, $sleep, $text);
}

function check_email(string $email): int
{
    $sleep = rand(1, 60);
    sleep($sleep);
    console_info('checked email: %s at %d seconds', $email, $sleep);

    return rand(0, 1);
}
