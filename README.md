# Афиша волонтёров Московского Политеха

Laravel 13, PHP 8.3+, SQLite. Роли: волонтёр, организатор, администратор.

## Запуск

    composer install
    cp .env.example .env
    php artisan key:generate
    php artisan migrate:fresh --seed
    php artisan serve

Сайт: http://127.0.0.1:8000. Тесты: `php artisan test`.

## Учётные записи

– admin@mospolytech.local / admin123
– org@mospolytech.local, org2@mospolytech.local / org12345
– vol@mospolytech.local ... vol5@mospolytech.local / vol12345
