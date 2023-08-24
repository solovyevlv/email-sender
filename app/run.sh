#!/bin/sh
composer install

while ! nc -z karma8_db 5432; do echo "Waiting db master..."; sleep 1; done;
while ! nc -z karma8_db_rep 5432; do echo "Waiting db replica..."; sleep 1; done;
while ! nc -z karma8_amqp 5672; do echo "Waiting..."; sleep 1; done;

bash run_consumers.sh

tail -f /dev/null
