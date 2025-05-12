# Workout Assistant

## Название и назначение сервиса

**Workout Assistant** — сервис для тренировок в спортзале, помогает анализировать тренировки, содержит базу с видеогидами.

**Роль в системе:**
- Аутентификация (Sanctum): Регистрация, вход, выход пользователей.
- Тренировки: Хранение и анализ данных о тренировках.
- Интеграция с другими микросервисами: Микросервис взаимодействует с другими микросервисами через API для получения/передачи необходимых данных.

**Основные функции:**
- Регистрация новых пользователей.
- Аутентификация и получение токенов доступа.
- Отзыв токенов.
- Сохранение информации о тренировке в зале.
- Предоставляет гайды по правильному выполнению упражнений.
- Просмотр результатов по упражнениям.
- Анализ отстающих групп мышц.

## Архитектура и зависимости

### Технологии и фреймворки
- **PHP 8.2**
- **Laravel 12.0**
- **Laravel Sanctum** – для аутентификации и управления токенами API.
- **Pest PHP** – для unit-тестирования.

## Способы запуска сервиса

### Запуск без Docker

1. **Настройка `php.ini`**

    Перед началом работы необходимо активировать следующие расширения в файле `php.ini`:

    ```ini
    ; ====================================
    ; Обязательные расширения для проекта
    ; ====================================
    
    ; Для определения MIME-типов файлов
    extension=fileinfo
    
    ; Для работы с PostgreSQL через PDO
    extension=pdo_pgsql
    
    ; Для работы с ZIP-архивами
    extension=zip
    ```
2. **Установка зависимостей:**
   ```bash
   composer install
   ```
3. **Настройка окружения:**
    - Скопируйте `.env.example` в `.env` и настройте необходимые переменные.
    - Создайте БД и выполните миграции.
   ```bash
   php artisan migrate
   ```
4. **Генерация ключа приложения:**
   ```bash
   php artisan key:generate
   ```
5. **Заполнение таблицы `exercises`:**
   
   Добавьте в таблицу свои данные или воспользуйтесь `ExercisesTableSeeder`.
    ```bash
    php artisan db:seed --class=ExercisesTableSeeder
    ```
6. **Запуск локального сервера:**
   ```bash
   php artisan serve
   ```
   Сервис будет доступен по адресу: [http://localhost:8000](http://localhost:8000)

### Запуск через Docker
#### Требования
- Docker
- Docker-compose

1. **Настройка окружения**:
   Проверьте `.env`, следующие значения должны быть заданы:
    ```
    POSTGRES_DB=your_container_db_name
    POSTGRES_USER=your_container_db_username
    POSTGRES_PASSWORD=your_container_db_password
    PGADMIN_DEFAULT_EMAIL=your_container_pgadmin_login_email
    PGADMIN_DEFAULT_PASSWORD=your_container_pgadmin_password
    ```

2. **Запуск локального сервера**:
   Создать и запустить все требуемые контейнеры:
    ```bash
    docker-compose up -d --build
    ```
   После этого, сам сервис и pgadmin станут доступны на портах, указанных в docker-compose.yml.

   Для остановки сервиса используется команда:
    ```bash
    docker-compose stop
    ```
   Для повторного запуска:
    ```bash
    docker-compose start
    ```
   Для остановки и удаления всех созданных контейнеров:
    ```bash
    docker-compose down
    ```

## API

### Основные endpoint'ы:

- **POST /api/v1/register**
  
  **Описание**: Регистрирует нового пользователя.
  **Параметры**:
    - `name` (строка, обязательный)
    - `email` (строка, обязательный, уникальный)
    - `password` (строка, обязательный, минимум 8 символов, верхний и нижний регистры, цифры, спец. символы)

  **Ответ (201 Created)**:
  ```json
  {
    "data": {
      "access_token": "<sanctum_token>",
      "token_type": "Bearer"
    }
  }
  ```

- **POST /api/v1/login**  
  **Описание:** Выполняет аутентификацию пользователя и возвращает токен Sanctum.  
  **Параметры:**
    - `email` (string, обязательный)
    - `password` (string, обязательный)
  
  **Заголовки:**
  - `Accept: application/json`

  **Ответ (200 OK):**
  ```json
  {
    "data": {
      "access_token": "<sanctum_token>",
      "token_type": "Bearer"
    },
    "meta": null
  }

- **POST /api/v1/logout**  
  **Описание:** Инвалидирует текущий токен доступа.
  **Заголовки**:
  - `Authorization: Bearer {ваш_токен}` - Токен доступа пользователя.
  - `Accept: application/json`
  
  **Параметры**: Нет

- **GET /api/v1/users**  
  **Описание:** Возвращает информацию об аутентифицированном пользователе.
  **Заголовки**:
  - `Authorization: Bearer {ваш_токен}` - Токен доступа пользователя.
  - `Accept: application/json`
  
  **Параметры**: Нет

  **Ответ (200 OK):**
  ```json
    {
      "id": 4,
      "name": "Test User 3",
      "email": "example3@mail.ru",
      "created_at": "2025-05-10T17:16:26.000000Z",
      "updated_at": "2025-05-10T17:16:26.000000Z"
    }
  ```
- **GET /api/v1/exercise/by-name/{exercise_name}/guide**  
  **Описание:** Возвращает ссылку на гайд по правильному выполнению упражнения.
  **Заголовки**:
    - `Authorization: Bearer {ваш_токен}` - Токен доступа пользователя.
    - `Accept: application/json`
  
  **Параметры**:
    - `{exercise_name}` (строка) - название упражнения.

- **Ответ (200 OK):**
    ```json
    {
      "data": {
        "tutorial": "<ссылка на видео>"
      },
      "errors": null,
      "meta": null
    }
    ```
- **GET /api/v1/user-exercise-progress**  
  **Описание:** Возвращает результаты пользователя в упражнении.
  **Заголовки**:
    - `Authorization: Bearer {ваш_токен}` - Токен доступа пользователя.
    - `Accept: application/json`
  
  **Параметры**:
    - `muscle_group` (string, обязательный)
    - `exercise_name` (string, обязательный)
  
  **Ответ (200 OK):**
    ```json
    {
      "data": {
        "id": 1,
        "user_id": 4,
        "exercise_id": 1,
        "record_weight": 60,
        "record_repeats": 8,
        "last_weight": 60,
        "last_repeats": 8,
        "created_at": "2025-05-11T11:13:51.000000Z",
        "updated_at": "2025-05-11T11:13:51.000000Z"
      },
      "meta": null
    }
    ```

- **POST /api/v1/workouts**  
  **Описание:** Сохраняет информацию о тренировке в базу данных.
  **Заголовки**:
    - `Authorization: Bearer {ваш_токен}` - Токен доступа пользователя.
    - `Accept: application/json`

  **Параметры**:
    - `exercises` (array, обязательный)

  **Пример Body**:
  ```json
  {
    "exercises": [
      {
        "exercise_name": "Приседания со штангой на спине",
        "weight": 60,
        "reps": 8
      },
      {
        "exercise_name": "Жим штанги лёжа",
        "weight": 70,
        "reps": 10
      }
    ]
  }
  ```
  **Ответ (201 Created):**
  ```json
  {
    "data": {
      "message": "Workout saved successfully",
      "ignored_exercises": []
    },
    "meta": null,
    "errors": null
  }
  ```
- **GET /api/v1/lagging-muscle-groups**  
  **Описание:** Возвращает список отстающих групп мышц отсортированный от наиболее отстающих к наименее отстающим.

  **Заголовки**:
    - `Authorization: Bearer {ваш_токен}` - Токен доступа пользователя.
    - `Accept: application/json`

  **Параметры**: Нет

  **Ответ (200 OK):**
  ```json
    {
      "data": {
        "lagging_muscle_groups": [
            "Спина",
            "Плечи",
            "Руки",
            "Пресс",
            "Грудь",
            "Ноги и Ягодицы"
        ]
      },
      "meta": null,
      "errors": null
  }
  ```

## Тестирование

**Настройка окружения:**
- Создайте `.env.testing`, скопируйте туда данные из `.env.example`и настройте необходимые переменные.
- Создайте отдельную БД для тестов.
- Сгенерируйте APP_KEY.
```bash
   php artisan key:generate --env=testing
```
### Запуск тестов
```bash
php artisan test
```

## Контакты и поддержка

- **Разработчик:** Горюнов Илья
- **Telegram:** @ilyshka_k
- **Email:** ilyagorunov.04@gmail.com
