CREATE TYPE checked_enum AS ENUM ('not_checked', 'in_progress', 'checked');

CREATE TABLE IF NOT EXISTS users
(
    id         bigint  not null
        constraint users_id_pk
            primary key,
    username   varchar not null,
    email      varchar not null
        constraint users_email_ukey
            unique,
    valid_ts   timestamp,
    confirmed  boolean not null default false,
    checked    checked_enum not null default 'not_checked',
    valid      smallint not null default 0
);

CREATE INDEX IF NOT EXISTS users_valid_ts_index
    ON users (valid_ts ASC);

ALTER TABLE IF EXISTS users
    OWNER TO postgres;

SELECT pg_sleep(30);

CREATE SUBSCRIPTION subscribers
    CONNECTION 'host=karma8_db dbname=karma8 user=postgres password=postgres-karma8'
    PUBLICATION subscribers;
