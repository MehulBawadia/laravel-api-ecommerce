## API for E-Commerce

This is the API for an E-Commerce web store. At the moment, only one seller and multiple buyers are supported.

#### Steps to install this project:

Run the commands below one by one

```bash
git clone git@github.com:MehulBawadia/laravel-api-ecommerce.git
cd laravel-api-ecommerce
cp .env.example .env
## Don't forget to update the DB_* credentials in the .env file
composer install
php artisan key:generate
php artisan migrate
php artisan storage:link
php artisan scribe:generate
php artisan serve --host=localhost
# The docs can be viewed at http://localhost:8000/docs
```

#### Test the project

```bash
php artisan test
# or vendor/bin/phpunit
```

#### Live Preview

You can check the [live preview here](https://ecomapi.bmehul.com)

#### License

This project is an open-sourced software licensed under the [MIT License](https://opensource.org/licenses/MIT).
