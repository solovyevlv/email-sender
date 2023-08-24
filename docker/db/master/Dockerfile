FROM postgres:15.4-alpine3.17

RUN mkdir -p "$PGDATA" && chmod -R 700 "$PGDATA" && chown -R postgres:postgres "$PGDATA"

COPY ./init.sql /docker-entrypoint-initdb.d
COPY ./users.csv /docker-entrypoint-initdb.d
