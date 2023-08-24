<?php

require_once __DIR__ . '/../postgres.php';
require_once __DIR__ . '/../../console.php';
require_once __DIR__ . '/../../array_helper.php';

/**
 * @param array<string, mixed> $data
 */
function update_by_id(int $id, array $data): bool
{
    try {
        $conn = connect_master_db();
        $setString = convert_assoc_to_query_string($data);
        $query = 'UPDATE users SET ' . $setString . ' WHERE id=' . $id;
        $result = pg_query($conn, $query);
        $status = (bool) $result;
        pg_free_result($result);
        pg_close($conn);

        return $status;
    } catch (Throwable $e) {
        console_err('Got user repository error: %s' . $e->getMessage());

        return false;
    }
}

/**
 * @param array<integer> $id
 * @param array<string, mixed> $data
 */
function update_by_id_list(array $id, array $data): bool
{
    try {
        $conn = connect_master_db();
        $setString = convert_assoc_to_query_string($data);
        $query = 'UPDATE users SET ' . $setString . ' WHERE id IN (' . implode(',', $id) . ')';
        $result = pg_query($conn, $query);
        $status = (bool) $result;
        pg_free_result($result);
        pg_close($conn);

        return $status;
    } catch (Throwable $e) {
        console_err('Got user repository error: %s' . $e->getMessage());

        return false;
    }
}
