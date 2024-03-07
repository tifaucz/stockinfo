
# Stocks Info

This is a Laravel, Livewire and Bootstrap project, contains user login and signup, where the user can select stocks to add to their watchlist. The dashboard will display information and charts from the user's watchlist.
All data and APIs for live pricing info come from Finnhub: https://finnhub.io/docs/api/

![image](https://github.com/tifaucz/stockinfo/assets/15833226/db12b022-55a6-4da6-be99-223b6e971aa5)

## Usage
Once you register and login, you will see the dashboard.
- First use the filter to find a stock. Type 2 or more characters, and the dropdown on the left should be populated. You can filter further by Mic and Type of stock. 
- Click on the dropdown and then click on the stock you desire, it will be aded to the watchlist table and the charts will be updated.
- When logging out and in again, you should keep your watchlist, being able to update it as you desire.

## Requirements
- A Finnhub API key. You can get one free at https://finnhub.io/dashboard . Without it the app should work, however it will not load the prices for the stocks.
- PHP (version as required by your version of Laravel)
- Composer (for managing PHP dependencies)
- Node.js and npm (for managing front-end assets)
- For databases, you need either MySQL or MariaDB for the app, and SQLite for testing.

## Installation

To install the project, follow these steps:

1. Clone the repository to your local machine:
```
git clone https://github.com/your-username/stockinfo.git
```

2. Navigate to the project directory:
```
cd stockinfo
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


## Testing the Application
To run tests for the application on your local machine, you can use:
```
php artisan test
```
If the connection with the database fails, be sure to have SQLite configured in your `php.ini`. You can check this by runningthe command below and checking if the output contains `pdo_sqlite` and `sqlite3`:
```
php -m | grep sqlite
```

## Debug logs
If you need to check the app logs, in the root directory run:
```
tail -f storage/logs/laravel.log
```
