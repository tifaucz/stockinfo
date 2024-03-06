<?php

namespace Tests\Feature;

use App\Http\Livewire\StockTable;
use App\Models\Stock;
use App\Models\User;
use App\Services\StockPriceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_component_renders()
    {
        Livewire::test(StockTable::class)
            ->assertStatus(200);
    }

    /** @test */
    public function can_filter_stocks()
    {
        // Given we have a few stocks in the database
        Stock::factory()->create(['symbol' => 'AAPL', 'description' => 'Apple Inc.', "mic" => "XNAS", "type" => "Common Stock"]);
        Stock::factory()->create(['symbol' => 'GOOG', 'description' => 'Alphabet Inc.', "mic" => "XNAS", "type" => "Common Stock"]);

        // When we filter stocks by a keyword
        Livewire::test(StockTable::class)
            ->set('filter', 'Apple')
            ->call('filterStocks')
            ->assertSee('AAPL') // Assert that the filtered stock is visible
            ->assertDontSee('GOOG'); // Assert that the non-matching stock is not visible
    }

    /** @test */
    public function can_filter_stocks_by_type_and_mic()
    {
        // Given we have a few stocks in the database
        Stock::factory()->create(['symbol' => 'AAPL', 'description' => 'Apple Corp.', 'mic' => 'XNAS', 'type' => 'Common Stock']);
        Stock::factory()->create(['symbol' => 'MSFT', 'description' => 'Microsoft Corp.', 'mic' => 'XNAS', 'type' => 'Preferred Stock']);
        Stock::factory()->create(['symbol' => 'GOOG', 'description' => 'Alphabet Corp.', 'mic' => 'BATS', 'type' => 'Common Stock']);

        // When we filter stocks by type and mic
        Livewire::test(StockTable::class)
            ->set('filter', 'Corp')
            ->set('selectedType', 'Common Stock')
            ->set('selectedMic', 'XNAS')
            ->call('filterStocks') // Assuming 'filterStocks' applies the type and mic filters
            ->assertSee('AAPL') // Assert that the filtered stock is visible
            ->assertDontSee('MSFT') // Assert that the non-matching stock by type is not visible
            ->assertDontSee('GOOG'); // Assert that the non-matching stock by mic is not visible
    }


    /** @test */
    public function user_can_add_a_symbol_to_their_stock_list()
    {
        // Assuming you have a user factory and the user is logged in
        $user = User::factory()->create();
        $this->actingAs($user);

        Stock::factory()->create(['symbol' => 'AAPL', 'description' => 'Apple Inc.', "mic" => "XNAS", "type" => "Common Stock"]);

        // Mock the StockPriceService to prevent actual API calls
        $mockedService = \Mockery::mock(StockPriceService::class);
        $mockedService->shouldReceive('getRealTimePrice')->andReturn([
            'c' => 150, // Current price
            'd' => 2, // Change
            'dp' => 1.5, // Percent change
            'h' => 155, // High price of the day
            'l' => 145, // Low price of the day
        ]);

        $this->app->instance(StockPriceService::class, $mockedService);

        // When we add a symbol
        Livewire::test(StockTable::class)
            ->set('selectedSymbol', 'AAPL')
            ->call('addSymbol')
            ->assertSee('AAPL') // Assert that the stock is added to the table
            ->assertSee('150'); // Assert that the current price is displayed

        // Check if the stock is saved to the user's stocks
        $user->refresh(); // Refresh user model to get updated data
        $this->assertArrayHasKey('AAPL', $user->stocks); // Assert that 'AAPL' is in the user's stocks
    }

    /** @test */
    public function user_can_remove_a_symbol_from_their_stock_list()
    {
        // Assuming you have a user factory and the user is logged in
        $user = User::factory()->create();
        $this->actingAs($user);

        Stock::factory()->create(['symbol' => 'AAPL', 'description' => 'Apple Inc.', "mic" => "XNAS", "type" => "Common Stock"]);

        // Add a stock to the user's stock list
        $user->stocks = ['AAPL' => ['current' => 150,]];
        $user->save();

        // Test the removal of the symbol
        Livewire::test(StockTable::class)
            ->call('removeSymbol', 'AAPL')
            ->assertDontSee('AAPL'); // Assert that the stock is removed from the table

        // Check if the stock is removed from the user's stocks
        $user->refresh(); // Refresh user model to get updated data
        $this->assertArrayNotHasKey('AAPL', $user->stocks); // Assert that 'AAPL' is not in the user's stocks
    }

}
