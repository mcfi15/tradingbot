<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use App\Services\FuturesServices;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FuturesServicesTest extends TestCase
{
    use RefreshDatabase;

    public function test_future_tickers_falls_back_to_binance_us_when_geo_blocked(): void
    {
        Cache::forget('active_market_provider');
        Cache::forget('futures_market_tickers_unified');

        Http::fake([
            'https://fapi.binance.com/*' => Http::response(['message' => 'Service unavailable from restricted jurisdiction'], 451),
            'https://api.binance.com/*' => Http::response(['message' => 'Service unavailable from restricted jurisdiction'], 451),
            'https://api.binance.us/*' => Http::response([
                [
                    'symbol' => 'BTCUSDT',
                    'lastPrice' => '96500.00',
                    'priceChangePercent' => '2.50',
                    'highPrice' => '97000.00',
                    'lowPrice' => '95000.00',
                    'volume' => '500000.0',
                ],
                [
                    'symbol' => 'SOLUSDT',
                    'lastPrice' => '195.50',
                    'priceChangePercent' => '4.20',
                    'highPrice' => '202.00',
                    'lowPrice' => '188.00',
                    'volume' => '380000.0',
                ],
            ], 200),
        ]);

        $service = new FuturesServices();
        $response = $service->futureTickers();

        $this->assertEquals('success', $response['status']);
        $this->assertEquals('binance_us', $response['provider']);
        $this->assertNotEmpty($response['data']);
        $this->assertEquals('BTCUSDT', $response['data'][0]['ticker']);
        $this->assertEquals(96500.00, $response['data'][0]['current_price']);
    }

    public function test_future_tickers_falls_back_to_bybit_when_binance_down(): void
    {
        Cache::forget('active_market_provider');
        Cache::forget('futures_market_tickers_unified');

        Http::fake([
            'https://fapi.binance.com/*' => Http::response([], 500),
            'https://api.binance.com/*' => Http::response([], 500),
            'https://api.binance.us/*' => Http::response([], 500),
            'https://api.bybit.com/*' => Http::response([
                'retCode' => 0,
                'result' => [
                    'list' => [
                        [
                            'symbol' => 'BTCUSDT',
                            'lastPrice' => '96800.00',
                            'price24hPcnt' => '0.0350',
                            'highPrice24h' => '97500.00',
                            'lowPrice24h' => '94500.00',
                            'volume24h' => '55000.0',
                        ],
                    ],
                ],
            ], 200),
        ]);

        $service = new FuturesServices();
        $response = $service->futureTickers();

        $this->assertEquals('success', $response['status']);
        $this->assertEquals('bybit_linear', $response['provider']);
        $this->assertEquals('BTCUSDT', $response['data'][0]['ticker']);
        $this->assertEquals(96800.00, $response['data'][0]['current_price']);
        $this->assertEquals(3.50, $response['data'][0]['change_1d_percentage']);
    }

    public function test_future_ticker_returns_individual_pair(): void
    {
        Cache::forget('active_market_provider');

        Http::fake([
            '*' => Http::response([
                [
                    'symbol' => 'SOLUSDT',
                    'lastPrice' => '195.50',
                    'priceChangePercent' => '4.20',
                    'highPrice' => '202.00',
                    'lowPrice' => '188.00',
                    'volume' => '380000.0',
                ],
            ], 200),
        ]);

        $service = new FuturesServices();
        $response = $service->futureTicker('SOLUSDT');

        $this->assertEquals('success', $response['status']);
        $this->assertEquals('SOLUSDT', $response['data']['ticker']);
        $this->assertEquals(195.50, $response['data']['current_price']);
    }

    public function test_copy_trading_terminal_loads_with_futures_service(): void
    {
        Http::fake([
            '*' => Http::response([
                'data' => ['converted_amount' => 1, 'exchange_rate' => 1],
                'symbol' => 'BTCUSDT',
                'lastPrice' => '96500.00',
                'priceChangePercent' => '2.50',
                'highPrice' => '97000.00',
                'lowPrice' => '95000.00',
                'volume' => '12000.5',
            ], 200),
        ]);

        $user = User::factory()->create(['status' => 'active']);

        $response = $this->actingAs($user)
            ->get(route('user.copy-trading.index'));

        $response->assertStatus(200);
    }
}
