<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Services\StockPriceService;


class StockPriceController extends Controller
{
    public function getRealTimePrice($symbol)
    {
        return $this->stockPriceService->getRealTimePrice($symbol);
    }

    public function getSymbols($exchange = 'US')
    {
        return $this->stockPriceService->getRealTimePrice($exchange);
    }
}

