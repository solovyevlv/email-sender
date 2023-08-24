<?php

require_once __DIR__ . '/config.php';

use PgSql\Connection;

function connect_master_db(): Connection
{
    $connect = pg_connect(
        sprintf('postgresql://%s:%s@%s:%d/%s',
            DB_USER, DB_PASS, MASTER_DB_HOST, DB_PORT, DB_NAME
        )
    );

    if (!$connect) {
        trigger_error('Could not connect to PGSQL.', E_USER_ERROR);
    }

    return $connect;
}

function connect_rep_db(): Connection
{
    $connect = pg_connect(
        sprintf('postgresql://%s:%s@%s:%d/%s',
            DB_USER, DB_PASS, REP_DB_HOST, DB_PORT, DB_NAME
        )
    );

    if (!$connect) {
        trigger_error('Could not connect to PGSQL.', E_USER_ERROR);
    }

    return $connect;
}
