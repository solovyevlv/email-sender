#!/bin/sh

for i in {1..100}
do
  php ./src/consumer/send_message.consumer.php > ./log/send_message."$i".consumer.log &
  php ./src/consumer/validate_email.consumer.php > ./log/validate_email."$i".consumer.log &
done
