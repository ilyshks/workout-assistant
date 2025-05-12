#!/bin/sh
set -e

env

# Проверка доступности PostgreSQL
until pg_isready -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USERNAME" -d "$DB_DATABASE"; do
  echo "Ожидание готовности PostgreSQL..."
  sleep 2
done

echo "PostgreSQL готов!"

# Запуск миграций
php artisan migrate --force || {
  echo "Миграции не удались!"
  exit 1
}

echo "Миграции выполнены успешно."

# Заполнение таблицы с упражнениями
php artisan db:seed --class=ExercisesTableSeeder || {
  echo "Таблица exercises не заполнена!"
  exit 1
}

echo "Запуск ExercisesTableSeeder выполнен успешно."

exec php-fpm
