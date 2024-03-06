<?php

namespace Database\Seeders;
use App\Models\User;
use App\Models\Stock;
use Illuminate\Support\Facades\File;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::where('email', 'admin@example.com')->delete();

        User::firstOrCreate([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('admin'),
        ]);

        Stock::truncate();

        $json = File::get(database_path('seeders/stocks.json'));
        $stocks = json_decode($json, true);
        foreach ($stocks as $stockData) {
            Stock::createStock($stockData);
        }
    }
}
