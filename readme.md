## About

Web application which uses Quickbooks Online API.

- `git clone https://github.com/pascalallen/matt-pascal.git`
- `cp .env.example .env`
- Open `.env` and add your credentials to the `DB_*` block and to the `QUICKBOOKS_*` block
- `php artisan key:generate`
- `composer install`
- `npm install`
- `php artisan migrate`
