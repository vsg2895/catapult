<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CoinMarketService
{
    /**
     * @param array $nameOfCoins
     * @example ["BTC", "ETH", "BNB"]
     * @return array
     * @example ["BNB" => 308.42296756438, "BTC" => 21697.362521488, "ETH" => 1521.2267908695]
     */
    public function getExchangeRates(array $nameOfCoins): array
    {
        $stringOfCoins = implode(",", $nameOfCoins);

        $response = Http::withHeaders([
                'X-CMC_PRO_API_KEY' => config('services.coin_market_cap.token')
            ])->get(config('services.coin_market_cap.endpoint') . "?symbol=$stringOfCoins,USD");
        $responseData = $response->json();
        $exchangeRates = $responseData['data'];
        if (empty($exchangeRates) || !$response->ok()) {
            return [];
        }
        array_walk($exchangeRates, function (&$item) {
            $item = $item['quote']['USD']['price'];
        });

        return $exchangeRates;
    }
}
