{{-- resources/views/livewire/stock-table.blade.php --}}
<div>
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/chartjs-chart-financial"></script>
    <script>
        document.addEventListener('notify', event => {
            console.log('Event received:', event);
            // alert(event.detail);
            document.getElementById('filter').value = "";
        });

        document.addEventListener('filteredSymbols-updated', event => {
            const symbolSelect = document.getElementById('symbol-select');
            if (symbolSelect) {
                const event = new MouseEvent('mousedown', {
                    bubbles: true,
                    cancelable: true,
                    view: window
                });
                symbolSelect.dispatchEvent(event);
            }
        });

        function getRandomColor() {
            const r = Math.floor(Math.random() * 155) +100;
            const g = Math.floor(Math.random() * 155) +100;
            const b = Math.floor(Math.random() * 155) +100;
            return `rgb(${r}, ${g}, ${b})`;
        }

        let chart = null;
        let chart_type = 'line';

        function pieChart(stockData) {
            const labels = Object.keys(stockData);
            const data = labels.map(symbol => stockData[symbol].percent_change);
            const backgroundColors = labels.map(() => getRandomColor());

            console.log("pieChart data:", data);

            return {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data, 
                        backgroundColor: backgroundColors,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true
                        }
                    }
                }
            };
        }

        function lineChart (stockData) {
            const datasets = Object.keys(stockData).map(symbol => ({
                label: symbol,
                data: [stockData[symbol].low, stockData[symbol].current, stockData[symbol].high],
                fill: false,
                borderColor: getRandomColor(),
                tension: 0.1
            }));

            console.log("pieChart data:", datasets);

            return {
                type: 'line',
                data: {
                    labels: ['Low', 'Current', 'High'], 
                    datasets: datasets
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: false
                        }
                    }
                }
            };
        }

        function barChart(stockData) {
            const datasets = Object.keys(stockData).map(symbol => ({
                label: symbol,
                data: [stockData[symbol].change],
                backgroundColor: getRandomColor(),
            }));

            console.log("barChart data:", datasets);

            return {
                type: 'bar',
                data: {
                    labels: ['% Change'], 
                    datasets: datasets
                },
                options: {
                    indexAxis: 'x',
                    scales: {
                        y: {
                            beginAtZero: true 
                        }
                    },
                    plugins: {
                        legend: {
                            display: true
                        }
                    }
                }
            };
        }

        document.addEventListener('chart-data', event => {
            const stockData = event.detail[0][1];
            const chart_type = event.detail[0][0];

            console.log("chart-data event:", event.detail);

            let config;
            switch (chart_type) {
                case 'bar':
                    config = barChart(stockData);
                    break;
                case 'pie':
                    config = pieChart(stockData);
                    break;
                default:
                    config = lineChart(stockData);
                    break;
            }

            if (chart) {
                chart.destroy();
            }

            chart = new Chart(document.getElementById('chart').getContext('2d'), config);
        });
    </script>
    @endpush
    <div class="flex flex-wrap -mx-3 mb-3">
        {{-- Exchange Select Dropdown --}}
        <div class="w-full md:w-1/3 px-3 mb-3 md:mb-0">
            <div class="chart-type-selector">
                <label class="block uppercase tracking-wide text-gray-100 text-xs font-bold mb-2">Chart Type:</label>
                <div class="flex items-center space-x-4 mb-4"> {{-- Use flex and space-x-4 for inline display and spacing --}}
                    <div class="flex items-center p-2">
                        <input type="radio" id="line" name="chart_type" value="line" wire:model="chart_type" wire:change="updateChart">
                        <label for="line" class="ml-2 text-sm text-gray-200">Price</label>
                    </div>
                    <div class="flex items-center p-2">
                        <input type="radio" id="bar" name="chart_type" value="bar" wire:model="chart_type" wire:change="updateChart">
                        <label for="bar" class="ml-2 text-sm text-gray-200">Change</label>
                    </div>
                    <div class="flex items-center p-2">
                        <input type="radio" id="pie" name="chart_type" value="pie" wire:model="chart_type" wire:change="updateChart">
                        <label for="pie" class="ml-2 text-sm text-gray-200">% Change</label>
                    </div>
                </div>
            </div>
        </div>

        {{-- MIC Select Dropdown --}}
        <div class="w-full md:w-1/3 px-3 mb-3 md:mb-0">
            <label for="mic-select" class="block uppercase tracking-wide text-gray-100 text-xs font-bold mb-2">Select MIC:</label>
            @if(count($micList) < 1)
                <select id="stock-select" disabled class="block appearance-none w-full bg-gray-100 border border-gray-200 text-gray-500 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                    <option>Please filter the stocks</option>
                </select>
            @else
                <select id="mic-select" wire:model="selectedMic" wire:change="changeMic" class="block appearance-none w-full bg-white border border-gray-400 hover:border-gray-500 px-4 py-2 pr-8 rounded shadow leading-tight focus:outline-none focus:shadow-outline text-gray-500">
                    <option value="">Select a MIC</option>
                    @foreach ($micList as $mic)
                        <option value="{{ $mic }}">{{ $mic }}</option>
                    @endforeach
                </select>
            @endif
        </div>

        {{-- Type Select Dropdown --}}
        <div class="w-full md:w-1/3 px-3 mb-3 md:mb-0">
            <label for="type-select" class="block uppercase tracking-wide text-gray-100 text-xs font-bold mb-2">Select Type:</label>
            @if(count($typeList) < 1)
                <select id="stock-select" disabled class="block appearance-none w-full bg-gray-100 border border-gray-200 text-gray-500 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                    <option>Please filter the stocks</option>
                </select>
            @else
                <select id="type-select" wire:model="selectedType" wire:change="changeType" class="block appearance-none w-full bg-white border border-gray-400 hover:border-gray-500 px-4 py-2 pr-8 rounded shadow leading-tight focus:outline-none focus:shadow-outline text-gray-500">
                    <option value="">Select a Type</option>
                    @foreach ($typeList as $type)
                        <option value="{{ $type }}">{{ $type }}</option>
                    @endforeach
                </select>
            @endif
        </div>
    </div>

    <div class="flex flex-wrap -mx-3 mb-3">
        <div class="w-full md:w-1/3 px-3 mb-3 md:mb-0">
        {{-- Filter Input --}}
            <div class="w-full px-3 mb-3">
                <label for="filter" class="block uppercase tracking-wide text-gray-100 text-xs font-bold mb-2">Filter Stocks:</label>
                <input type="text" id="filter" wire:model="filter" wire:keyup="filterStocks" class="block appearance-none w-full bg-white border border-gray-400 hover:border-gray-500 px-4 py-2 rounded shadow leading-tight focus:outline-none focus:shadow-outline text-gray-500" placeholder="Enter 2 characters or more...">
            </div>
        </div>

        <div class="w-full md:w-2/3 px-3 mb-3 md:mb-0">
            {{-- Symbol Select Dropdown --}}
            <div class="w-full px-3">
                <label for="symbol-select" class="block uppercase tracking-wide text-gray-100 text-xs font-bold mb-2">Add a Stock to Watchlist:</label>
                @if(count($filteredSymbols) < 1)
                    <select id="stock-select" disabled class="block appearance-none w-full bg-gray-100 border border-gray-200 text-gray-500 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                        <option>Please filter the stocks</option>
                    </select>
                @else
                    <select id="symbol-select" wire:model="selectedSymbol" wire:change="addSymbol" class="block appearance-none w-full bg-white border border-gray-400 hover:border-gray-500 px-4 py-2 pr-8 rounded shadow leading-tight focus:outline-none focus:shadow-outline text-black font-bold">
                        <option value="" class="">Filtered {{ count($filteredSymbols) }} symbols, please select.</option>
                        @foreach ($filteredSymbols as $stockDetails)
                            <option value="{{ (string) $stockDetails['symbol'] }}">{{ (string) $stockDetails['symbol'] }} - {{ (string) $stockDetails['description'] }}</option>
                        @endforeach
                    </select>
                @endif
            </div>
        </div>


    </div>

    <canvas id="chart" width="400" height="300" style="max-height: 400px !important;"></canvas>

    <table class="min-w-full divide-y divide-gray-200" style="min-width: 100% !important;">
        <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Symbol
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    MIC
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Type
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Price
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Change
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    % Change
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    High
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Low
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                </th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach ($stocks as $symbol => $data)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    {{ $symbol }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $data['mic'] ?? 'N/A' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $data['type'] ?? 'N/A' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $data['current'] ?? 'N/A'  }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $data['change'] ?? 'N/A'  }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $data['percent_change'] ?? 'N/A'  }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $data['high'] ?? 'N/A'  }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $data['low'] ?? 'N/A'  }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <button wire:click="removeSymbol('{{ $symbol }}')" class="text-red-600 hover:text-red-900">Remove</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
<div>


