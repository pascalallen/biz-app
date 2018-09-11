## About

Web application which uses Quickbooks Online API.

- `git clone https://github.com/pascalallen/matt-pascal.git`
- `cp .env.example .env`
- Open `.env` and add your credentials to the `DB_*` block and to the `QUICKBOOKS_*` block
- `composer install`
- `php artisan key:generate`
- `npm install`
- `php artisan migrate`
