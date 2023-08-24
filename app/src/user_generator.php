<?php

require_once __DIR__ . '/infra/enum.php';

(function ($argv) {
    $total = !empty($argv[1]) && is_numeric($argv[1]) ? (int)$argv[1] : 100;
    $subscribers = 0;
    $confirmEmails = 0;
    $file = fopen("./users.csv", 'w');

    for ($id = 1; $id <= $total; $id++) {
        $name = 'test' . $id;
        $email = sprintf('%s@gmail.com', $name);
        $validTs = '';
        // Рандомно устанавливаем подписку, по условию 20% юзеров
        $isSubscriber = rand(false, true) && (floor($id * 0.2) - $subscribers);
        // Рандомно устанавливаем подтверждение email, по условию 15% юзеров
        $isConfirmed = rand(false, true) && (floor($id * 0.15) - $confirmEmails);

        if ($isConfirmed) {
            $confirmEmails++;
        }

        if ($isSubscriber) {
            // Генерим случайную дату в диапозоне 10 дней до текущей даты по 1 месяц после текущей даты
            $randDate = randomDateInRange(
                (new DateTimeImmutable())->sub(DateInterval::createFromDateString('10 days')),
                (new DateTimeImmutable())->sub(DateInterval::createFromDateString('-1 month'))
            );

            $validTs = $randDate->format(DateTimeInterface::ATOM);
            $subscribers++;
        }

        $checked = rand(0, 1) && $validTs && !$isConfirmed ? CheckStatus::Checked : CheckStatus::NotChecked;
        $valid = $checked === CheckStatus::Checked ? rand(0, 1) : 0;
        $val = [$id, $name, $email, $validTs, (int)$isConfirmed, $checked->value, $valid];
        fputcsv($file, $val);
    }

    fclose($file);
})($argv);

function randomDateInRange(DateTimeImmutable $start, DateTimeImmutable $end): DateTimeImmutable
{
    $randomTimestamp = mt_rand($start->getTimestamp(), $end->getTimestamp());

    return (new DateTimeImmutable())->setTimestamp($randomTimestamp);
}
