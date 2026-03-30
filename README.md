# Тестовый проект на Phalcon(Micro) + Vite/Vue

Это тестовый проект в котором используются:
* Phalcon Framework через Micro v5.9
* Vite
* Vue
* Vue-router

Всё собрано в Docker через docker-compose:
* php-fpm
* nginx
* mysql
* node

## Сборка

```
docker-compose up -d
cd backend
make init
make init-seeds
```

## Структура проекта

```
backend/
├── app/
│   ├── Controllers/          # Контроллеры
│   ├── Models/               # Модели (Phalcon ORM)
│   ├── Middlewares/          # Промежуточные обработчики
│   ├── Repositories/         # Репозитории моделей
├── config/                   # Конфигурационные файлы
├── core/                     # Общая логика ядра
├── db/                       # База данных
│   ├── migrations/           # Миграции БД
│   ├── seeds/                # Начальные данные
├── vendor/                   # Composer зависимости
├── composer.json             # Зависимости проекта
├── index.php                 # Входная точка
```

## Пример запросов
### Авторизация пользователя
**Запрос**
```
POST http://localhost:8080/api/v1/user/register
{
    "email": "test@test.com",
    "password": "Test1234"
}
```
**Ответ**
```
{
    "status": "success",
    "data": []
}
```
**Запрос**
```
POST http://localhost:8080/api/v1/user
{
    "email": "test@test.com",
    "password": "Test1234"
}
```
**Ответ**
```
{
    "status": "success",
    "data": {
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJqdGkiOiI2OWNhMWVhNjRiYmQzIiwic3ViIjoidGVzdDhAdGVzdC5jb20iLCJhdWQiOlsiIl0sImlzcyI6Imh0dHA6Ly9sb2NhbGhvc3Q6ODA4MC8iLCJpYXQiOjE3NzQ4NTM3OTgsIm5iZiI6MTc3NDg1Mzc5OCwiZXhwIjoxNzc0OTQwMTk4fQ.LrG7_72tLoGQ30GHWCXtWg1kB1-jgq4wavohrchsq0A",
        "expires_in": 1774940198
    }
}
```

### Получение продуктов
**Запрос**
```
POST http://localhost:8080/api/v1/product/list
{
    "page": 1,
    "limit": 20,
    "category": "books",
    "order": {
        "field": "name",
        "dir": "DESC"
    }
}
```
**Ответ**
```
{
    "status": "success",
    "data": {
        "items": [
            {
                "id": 193,
                "name": "Ultra Tool 943",
                "description": "Lorem Ipsum is simply dummy text of the printing and typesetting industry.",
                "price": 101,
                "in_stock": true,
                "created_at": "2026-03-29 00:00:59",
                "updated_at": "2026-03-29 00:00:59"
            },
            ...
        ],
        "page": 1,
        "total": 141
    }
}
```

### Подробное получение данных о продукте
**Запрос**
```
GET http://localhost:8080/api/v1/product/50
```
**Ответ**
```
{
    "status": "success",
    "data": {
        "id": 50,
        "name": "Eco Product 452",
        "description": "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.",
        "price": 924,
        "in_stock": true,
        "created_at": "2026-03-30 09:04:19",
        "updated_at": "2026-03-30 17:41:17",
        "categories": [
            1,
            4,
            6
        ]
    }
}
```

### Редактирование продукта
Смена названия и удаления из категории
**Запрос**
```
POST http://localhost:8080/api/v1/product
{
    "id": 50,
    "name": "Eco Product 450",
    "description": "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.",
    "price": 924,
    "in_stock": true,
    "created_at": "2026-03-30 09:04:19",
    "updated_at": "2026-03-30 09:04:19",
    "categories": [
        1,
        4
    ]
}
```
**Ответ**
```
{
    "status": "success",
    "data": {
        "id": 50,
        "name": "Eco Product 450",
        "description": "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.",
        "price": 924,
        "in_stock": true,
        "created_at": "2026-03-30 09:04:19",
        "updated_at": "2026-03-30 18:06:22",
        "categories": [
            1,
            4
        ]
    }
}
```
