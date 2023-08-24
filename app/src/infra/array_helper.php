<?php

/**
 * Преобразует key-value to 'key=value'
 * @param array<string, mixed> $data
 */
function convert_assoc_to_query_string(array $data): string
{
    return implode(
        ',',
        array_map(static fn($k, $v) => sprintf('%s=\'%s\'' , $k, $v), array_keys($data), array_values($data))
    );
}
