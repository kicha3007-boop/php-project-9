# Анализатор страниц (PHP)

[![hexlet-check](https://github.com/kicha3007-boop/php-project-9/actions/workflows/hexlet-check.yml/badge.svg)](https://github.com/kicha3007-boop/php-project-9/actions/workflows/hexlet-check.yml)
[![PHP CI](https://github.com/kicha3007-boop/php-project-9/actions/workflows/workflow.yml/badge.svg)](https://github.com/kicha3007-boop/php-project-9/actions/workflows/workflow.yml)

Сайт, который проверяет страницы на SEO-пригодность: код ответа, `<h1>`, `<title>` и `<meta name="description">`.

Стек: Slim 4, PHP-View, PDO + PostgreSQL, Guzzle, DomCrawler, Valitron, Tailwind CSS.

Демо: деплой на Render пока не выполнен.

## Требования

- PHP 8.3+ с `pdo_pgsql`
- Composer, Node.js (сборка стилей)
- PostgreSQL

## Установка и запуск

```bash
git clone https://github.com/kicha3007-boop/php-project-9.git
cd php-project-9
make setup                                   # зависимости и сборка стилей
export DATABASE_URL=postgresql://user:password@localhost:5432/page_analyzer
make db-init                                 # таблицы из database.sql
make start                                   # http://localhost:8000
```

`APP_DEBUG=true` включает вывод трассировки ошибок; по умолчанию выключен.

## Разработка

```bash
make lint   # PHP_CodeSniffer, PSR-12
make test   # PHPUnit
```
