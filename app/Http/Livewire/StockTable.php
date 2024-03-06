<?php
namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\StockPriceService;
use App\Models\Stock;

use Illuminate\Support\Facades\Storage;


class StockTable extends Component
{
    public $stocks = [];
    public $selectedSymbol = null;
    public $symbols = [];
    protected $stockPriceService;
    public $availableSymbols = [];
    public $filteredSymbols = [];
    public $selectedExchange = 'US';
    public $micList = [];
    public $typeList = [];
    public $selectedMic = null;
    public $selectedType = null;
    public $chart_type = 'line';
    public $filter = '';
    public $previousFilter = '';

    public function mount(StockPriceService $stockPriceService)
    {
        $this->stockPriceService = $stockPriceService;
        $this->fetchTableData($stockPriceService);
        $this->stocks = auth()->user()->stocks ?? [];
        $this->dispatch('chart-data', [$this->chart_type, $this->stocks]);
    }

    public function updated($propertyName)
    {
        // Log::info('updated: ' . print_r($propertyName, true));
        if ($propertyName === 'selectedMic' || $propertyName === 'selectedType') {
            $this->filterTypeAndMic();
        }

        $this->dispatch('chart-data', [$this->chart_type, $this->stocks]);
    }

    public function filterStocks()
    {
        if($this->filter == $this->previousFilter || strlen($this->filter) < 3)
        {
            $this->availableSymbols = [];
            $this->filteredSymbols = [];
            return;
        }

        $this->previousFilter = $this->filter;

        Log::info('Filtering stocks with: ' . $this->filter);

        $this->availableSymbols = Stock::where('symbol', 'like', '%' . $this->filter . '%')
                ->orWhere('description', 'like', '%' . $this->filter . '%')
                ->get();

        $this->filteredSymbols = $this->availableSymbols->toArray();

        $this->filterTypeAndMic();
        
    }

    public function filterTypeAndMic()
    {
        $availableSymbols = $this->availableSymbols;

        if($this->selectedMic && strlen($this->selectedMic) > 0) {
            $availableSymbols = collect($availableSymbols)->filter(function ($symbol) {
                $matchesMic = $this->selectedMic ? $symbol->mic === $this->selectedMic : true;
                return $matchesMic;
            });
        }
        if($this->selectedType && strlen($this->selectedType) > 0) {
            $availableSymbols = collect($availableSymbols)->filter(function ($symbol) {
                $matchesType = $this->selectedType ? $symbol->type === $this->selectedType : true;
                return $matchesType;
            });
        }

        foreach ($availableSymbols as $symbol => $details) {
            if (!in_array($details->mic, $this->micList)) {
                $this->micList[] = $details->mic;
            }
            if (!in_array($details->type, $this->typeList)) {
                $this->typeList[] = $details->type;
            }
        }

        $this->filteredSymbols = $availableSymbols;
    }

    public function fetchTableData(StockPriceService $stockPriceService)
    {
        foreach ($this->symbols as $symbol) {
            $this->fetchStockData($symbol, $stockPriceService);
        }
    }

    public function fetchStockData($symbol, StockPriceService $stockPriceService)
    {
        Log::info('fetchStockData symbol: ' . print_r($symbol, true));
        $price_data = $stockPriceService->getRealTimePrice($symbol);
        $details_data = Stock::where('symbol', $symbol)->first();

        if ($price_data && $details_data) {
            $this->stocks[$symbol] = [
                'current' => $price_data['c'] ?? 'N/A', // Current price
                'change' => $price_data['d'] ?? 'N/A', // Change
                'percent_change' => $price_data['dp'] ?? 'N/A', // Percent change
                'high' => $price_data['h'] ?? 'N/A', // High price of the day
                'low' => $price_data['l'] ?? 'N/A', // Low price of the day
                'mic' => $details_data->mic ?? 'N/A', // Market Identifier Code
                'type' => $details_data->type ?? 'N/A', // Type of stock
                'description' => $details_data->description ?? 'N/A', // Type of stock
            ];
        } else {
            $this->stocks[$symbol] = [
                'current' => 'N/A',
                'change' => 'N/A',
                'percent_change' => 'N/A',
                'high' => 'N/A',
                'low' => 'N/A',
                'mic' => 'N/A',
                'type' => 'N/A',
                'description' => 'N/A',
            ];
        }
    }

    public function changeMic()
    {
        $this->filterTypeAndMic();
    }

    public function changeType()
    {
        $this->filterTypeAndMic();
    }

    public function addSymbol(StockPriceService $stockPriceService) 
    {
        Log::info('addSymbol: ' . print_r($this->selectedSymbol, true));
        if ($this->selectedSymbol && !in_array($this->selectedSymbol, $this->symbols)) {
            $symbol = strtoupper($this->selectedSymbol);
            $this->symbols[] = $symbol;
            $this->selectedSymbol = null;
            $this->fetchStockData($symbol, $stockPriceService);
            $this->saveStocks();
        }
        $this->reset('selectedSymbol');
    }

    public function removeSymbol($symbol)
    {
        Log::info('removeSymbol: ' . print_r($symbol, true));
        if ($this->stocks[$symbol]) {
            unset($this->symbols[$symbol]); 
            unset($this->stocks[$symbol]); 
            $this->saveStocks();
        }
    }

    public function updateChart()
    {
        $this->dispatch('chart-data', [$this->chart_type, $this->stocks]);
    }

    public function saveStocks()
    {
        $user = auth()->user();
        $user->stocks = $this->stocks;
        $user->save();
        Log::info('saveStocks: ' . print_r($user->stocks, true));
        $this->dispatch('chart-data', [$this->chart_type, $this->stocks]);
        $this->dispatch('notify', 'Stocks updated successfully!');
    }

    public function render()
    {
        return view('livewire.stock-table');
    }
}


