<?php

namespace App\Services\Console\CurrencyRateConsole;

use App\Model\Database\DatabaseInterface;
use App\Services\Console\CurrencyRateConsole\Interfaces\PairProcessorInterface;

class RemovePairCurrencyProcessorService implements PairProcessorInterface
{
    private DatabaseInterface $databaseService;

    public function __construct(
        DatabaseInterface $databaseService,
    ) {
        $this->databaseService = $databaseService;
    }

    //Processs all pairs like USD -> EUR and EUR -> USD
    public function processAllPairs($args): void
    {
        $pairs = $args;
        
        if (empty($pairs)) {
            echo "*** No currency pairs found. \n";
            return;
        }
    
        foreach ($pairs as $pair) {
            $this->processSinglePair($pair['from_currency'], $pair['to_currency']);
        }
    }

    //Process currency pair like USD -> EUR
    private function processSinglePair(string $from, string $to): void
    {
        $deleteCurrencyPair = $this->deleteCurrencyPair($from, $to);

        if($deleteCurrencyPair) {
            echo "Currency pairs $from -> $to and $to -> $from have been deleted.\n";
        } else {
            echo "Currency pairs $from -> $to and $to -> $from have not been deleted.\n";
        }
    }

    //Delete currency pair from currency_pairs table
    private function deleteCurrencyPair (string $from, string $to): bool
    {  
        $this->deleteExchangeRatePair($from, $to);
        return $this->databaseService->deleteCurrencyPair($from, $to);
        
    }

    //Delete currency pair for exchange_rate table
    private function deleteExchangeRatePair (string $from, string $to): bool {
        return $this->databaseService->deleteRates($from, $to);
    }
}
