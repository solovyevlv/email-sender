ALTER SYSTEM SET wal_level = logical;

CREATE SEQUENCE IF NOT EXISTS users_id_seq as bigint;
ALTER SEQUENCE users_id_seq OWNER TO postgres;

CREATE TYPE checked_enum AS ENUM ('not_checked', 'in_progress', 'checked');

CREATE TABLE IF NOT EXISTS users
(
    id        bigint           default nextval('users_id_seq'::regclass) not null
        constraint users_id_pk
            primary key,
    username  varchar not null,
    email     varchar not null
        constraint users_email_ukey
            unique,
    valid_ts  timestamp,
    confirmed boolean not null default false,
    checked   checked_enum not null default 'not_checked',
    valid     smallint not null default 0
);

ALTER TABLE IF EXISTS users
    OWNER TO postgres;
ALTER TABLE IF EXISTS users
    replica identity full;
CREATE PUBLICATION subscribers FOR TABLE users WHERE (valid_ts is not null);

COPY users FROM '/docker-entrypoint-initdb.d/users.csv' WITH (FORMAT csv);
