<?php

require_once __DIR__ . '/../postgres.php';
require_once __DIR__ . '/../../console.php';
require_once __DIR__ . '/../../enum.php';
function get_subscribers_with_unverified_email(): Generator
{
    try {
        $conn = connect_rep_db();

        $query = sprintf(
            'SELECT id, email FROM users WHERE valid_ts < now() and checked=\'%s\' and confirmed is false',
            CheckStatus::NotChecked->value
        );

        $result = pg_query($conn, $query);

        while ($row = pg_fetch_array($result, mode: PGSQL_ASSOC)) {
            yield (object)$row;
        }

        pg_free_result($result);
        pg_close($conn);
    } catch (Throwable $e) {
        console_err('Got subscriber repository error: %s', $e->getMessage());
    }
}

function get_subscribes_with_soon_expiring_subscription(): Generator
{
    try {
        $conn = connect_rep_db();

        $query = '
            SELECT id, username, email
            FROM users
            WHERE valid_ts BETWEEN now() AND now() + interval \'1 day\'
            OR valid_ts BETWEEN now() + interval \' 2 day\' AND now() + interval \'3 day\'
            ORDER BY valid_ts;
        ';

        $result = pg_query($conn, $query);

        while ($row = pg_fetch_array($result, mode: PGSQL_ASSOC)) {
            yield (object)$row;
        }

        pg_free_result($result);
        pg_close($conn);
    } catch (Throwable $e) {
        console_err('Got subscriber repository error: %s', $e->getMessage());
    }
}

function get_subscribes_with_expired_subscription(): Generator
{
    try {
        $conn = connect_rep_db();

        $query = '
            SELECT id, username, email
            FROM users
            WHERE valid_ts < now() AND valid=1 OR confirmed is true
            ORDER BY valid_ts;
        ';

        $result = pg_query($conn, $query);

        while ($row = pg_fetch_array($result, mode: PGSQL_ASSOC)) {
            yield (object)$row;
        }

        pg_free_result($result);
        pg_close($conn);
    } catch (Throwable $e) {
        console_err('Got subscriber repository error: %s', $e->getMessage());
    }
}
