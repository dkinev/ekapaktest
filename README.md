# 📦 Test API (Laravel 12, DDD)

Тестовое задание: REST API для управления заказами, товарами и клиентами.

---

# 🚀 Особенности реализации

## 🧠 Архитектура (DDD)

Проект реализован с использованием Domain-Driven Design:

```
Domains/
├── Product/ 
├── Customer/ 
└── Order/
    ├── Services
    ├── Repositories (с интерфейсами)
    └── DTO
```

- Контроллеры — тонкие
- Бизнес-логика — в Services
- Доступ к данным — через Repository + Interface
- DTO используется для возврата данных из домена

---

## ⚙️ Бизнес-логика заказов

Реализована **state machine** для заказов:


new → confirmed → processing → shipped → completed


Дополнительно:
- `cancelled` доступен только из:
    - `new`
    - `confirmed`

✔ Проверка корректности переходов  
✔ Ошибка при недопустимом переходе  
✔ Выделенный класс `OrderStateMachine`

---

## 📦 Работа с заказами

- Проверка наличия товара (stock)
- Списание остатков при создании заказа
- Подсчёт `total_amount`
- Все операции в транзакции (`DB::transaction`)

---

## 🌐 REST API

Базовый путь:


/api/v1/


### Основные endpoints:

- `GET /products`
- `GET /products/{id}`
- `POST /customers`
- `POST /orders`
- `GET /orders/{id}`
- `PATCH /orders/{id}/confirm`
- `PATCH /orders/{id}/cancel`

Swagger в папке /docs/swagger под версией **OpenAPI v.2**

---

## ⚡ Rate limiting

Создание заказов ограничено:


не более 10 запросов в минуту на IP


---

## ❗ Единая обработка ошибок (Laravel 12)

Реализована через:


bootstrap/app.php → withExceptions()


Формат ответа:

```json
{
  "error": "validation_error",
  "message": "Validation failed",
  "details": {}
}
```

Обрабатываются:

ValidationException → 422
DomainException → 400
HttpException → корректный код
fallback → 500

---

## 🧾 Валидация

Используются FormRequest:

CreateCustomerRequest
CreateOrderRequest

---

## 📚 Swagger (OpenAPI v2)

**Документация**:

`/docs/swagger/openapi.yaml`

**Содержит:**

- все endpoints
- request/response схемы
- примеры
- коды ошибок

Коллекция **Postman** для проверки:

`./postman_collection.json`

---

## 🧪 Тесты (Feature)

Покрыто:

```
✔ создание заказа
✔ валидация
✔ недостаток stock
✔ rate limit
✔ state machine переходы
✔ невалидные переходы
```

Тесты используют **отдельную** БД.

---

## 🐳 Docker

Проект полностью разворачивается через Docker:

* app (PHP)
* db (основная БД)
* db_test (тестовая БД)
* redis

## ⚙️ Установка и запуск

1. Клонирование
```
   git clone <repo>
   cd <repo>
```   
2. Запуск Docker
```
    docker-compose up -d --build
```
3. Установка зависимостей
```
   docker-compose exec -it app composer install
```
4. Настройка окружения
```
   cp .env.example .env
   cp .env.example .env.testing
```
5. Генерация ключа
```
    docker-compose exec app php artisan key:generate
```    
6. Миграции
```
   docker-compose exec app php artisan migrate
   docker-compose exec app php artisan migrate --env=testing
``` 
или
```
    make migrate-all
```
7. Сидирование
```
   docker-compose exec app php artisan db:seed
   docker-compose exec app php artisan db:seed --env=testing
```
или
```
    make seed-all
```
--

##   🧪 Запуск тестов
```
   docker exec app php artisan test
```
или 
```
   docker-compose exec app php artisan test
```

---

##  Пример запроса

   Создание заказа

```
curl -X POST http://localhost:8080/api/v1/orders \
-H "Accept: application/json" \
-d '{
 "customer_id": 1,
 "items": [
   {"product_id": 1, "quantity": 2}
 ]
}'
```
