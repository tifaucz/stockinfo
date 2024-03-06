
# Stocks Info

This is a Laravel, Livewire and Bootstrap project, contains user login and signup, where the user can select stocks to add to their watchlist. The dashboard will display information and charts from the user's watchlist.
All data and APIs for live pricing info come from Finnhub: https://finnhub.io/docs/api/

## Requirements
- A Finnhub API key. You can get one free at https://finnhub.io/dashboard 
- PHP (version as required by your version of Laravel)
- Composer (for managing PHP dependencies)
- Node.js and npm (for managing front-end assets)
- A database system like MySQL, PostgreSQL, or SQLite

## Installation

To install the project, follow these steps:

1. Clone the repository to your local machine:
```
git clone https://github.com/your-username/your-project-name.git
```

2. Navigate to the project directory:
```
cd your-project-name
```

3. Install PHP dependencies using Composer:
```
composer install
```

4. Copy the `.env.example` file to create a `.env` file: 
```
cp .env.example .env
```

4.1 Set your database connection details in the `.env` file:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=example
DB_USERNAME=example
DB_PASSWORD=example
```

4.2 Set your Finnhub API key in the `.env` file:
```
FINNHUB_API_KEY=example_key
```

5. Generate an application key:
```
php artisan key:generate
```

6. Run the database migrations and seeders (make sure your database connection is configured in `.env`):
```
php artisan migrate --seed
```

7. Install front-end dependencies and compile assets:
```
npm install
npm run dev
```


## Running the Application

To serve the application on your local machine, you can use Laravel's built-in server:
```
php artisan serve
```

This will start a development server at `http://localhost:8000`.


## Running the Application

To serve the application on your local machine, you can use Laravel's built-in server:
```
php artisan test
```
