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
docker-compose up
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
                "created_at": "2026-03-29 15:57:39",
                "updated_at": "2026-03-29 15:57:39"
            },
            ...
        ],
        "page": 1,
        "total": 141
    }
}
```
