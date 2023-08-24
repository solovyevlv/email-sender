# Сервис для рассылки уведомлений об истекающих подписках

## Необходимые команды

* Команда отправки email об скором окончании подписки (за 1 и 3 дня)
* Команда отправки email об истичении срока подписки на `valid || confirmed` (2 раза в месяц)
* Команда для валидации email (`set checked and valid`)

## Инфраструктура 
**1. DB**

На примере Postgres
Система хранения данных построена на логической репликации по условию
`valid_ts is not null` ,таким образом в реплицирующую бд `karma8_db_rep` дублируются только подписчики.

Запись идет через `karma8_db` - мастер бд, которая содержит все записи.

Это дает нам возможность устанавливать любые индексы для быстрого чтения,
не оказывая влияния на запись.

Для тестирования на большом количестве записей необходимо поднить бд вручную.
Через докер композ до 100000 юзеров

**2. AMQP**
   * Exchange `email_event.exchange`
      * routing key: `send_email_key`, queue: `send_email_queue` 
      * routing key: `check_email_key`, queue: `check_email_queue`

Скрипты запускаются по крону и закидывают нужные записи в очередь, 
которая разгребается консумерами:
* `app/src/send_message.consumer.php`
* `app/src/validate_email.consumer.php`

Количество консумеров необходимо установить `app/run_consumers.sh`, тестировалось на 100

Есть генератор юзеров для заполнения бд запуск: `php src/user_generator.php n`
n - колличество записей. 
Успешно протестировано на: 10K, 100K, 1M, 5M

**3. PHP**

Команды расположены `app/src/command`
 * send_message_expired_subscription.php
 * send_message_soon_expiring_subscription.php
 * validate_email.php

Запускаются cron: `config/crontab`

* `0 22   *   * *  validate_email.php`
* `0 10   *   * *  send_message_soon_expiring_subscription.php`
* `0 10 10,25 * *  send_message_expired_subscription.php`
