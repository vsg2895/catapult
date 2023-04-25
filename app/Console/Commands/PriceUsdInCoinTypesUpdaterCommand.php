<?php

namespace App\Console\Commands;

use App\Models\CoinType;
use App\Models\UserWallet;
use App\Services\CoinMarketService;
use Illuminate\Console\Command;

class PriceUsdInCoinTypesUpdaterCommand extends Command
{
    protected $signature = 'coins:update-rates';
    protected $description = 'Update price_usd column for coins';

    public function handle(CoinMarketService $service): void
    {
        $coinTypes = CoinType::query()->get();
        $exchangeRates = $service->getExchangeRates($coinTypes->pluck('name')->toArray());
        if (! count($exchangeRates)) {
            return;
        }

        CoinType::upsert($coinTypes->map(function (CoinType $coinType) use ($exchangeRates) {
            return [
                'id' => $coinType->id,
                'name' => $coinType->name,
                'price_usd' => isset($exchangeRates[$coinType->name]) ? $exchangeRates[$coinType->name] : null,
            ];
        })->toArray(), 'id');
    }
}
