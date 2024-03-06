<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'symbol',
        'mic',
        'type',
        'description',
    ];

    /**
     * Adds a new stock entry to the database with validation.
     *
     * @param array $stockData Array containing the stock information.
     * @return bool True if the stock was created or already exists, false on validation failure.
     * @throws ValidationException If validation fails.
     */
    public static function createStock(array $stockData): bool
    {
        $rules = [
            'symbol' => 'required|string|max:255|unique:stocks,symbol',
            'mic' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
            'description' => 'required|string',
        ];

        $validator = Validator::make($stockData, $rules);

        if ($validator->fails()) {
            Log::error('Validation failed for stock: ' . $validator->errors());
            return false;
        }

        $stock = self::firstOrCreate(
            ['symbol' => $validator->validated()['symbol']],
            $validator->validated()
        );

        if ($stock->wasRecentlyCreated) {
            Log::info('New stock added: ' . $stock->symbol);
        } else {
            Log::info('Stock already exists: ' . $stock->symbol);
        }

        return true;
    }
}
