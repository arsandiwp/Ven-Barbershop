# Template Project

# Tools :

- PHP 8.2
- MySQL 8.0.30
- OpenRouter API (AI cloud tools) : Please generate your own key

# Quick & Simple Steps To Config Project :

- Pull from git
- Delete composer lock json
- make sure .env file exists by editing .env.example file
- Composer install
- php artisan key:generate
- php artisan migrate
- php artisan db:seed
- php artisan storage:link
