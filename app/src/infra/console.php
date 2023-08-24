<?php

declare(strict_types=1);

function console_info(string $message, ...$args): void
{
    fprintf(STDOUT, $message . PHP_EOL, ...$args);
}

function console_err(string $message, ...$args): void
{
    fprintf(STDERR, $message . PHP_EOL, ...$args);
}
