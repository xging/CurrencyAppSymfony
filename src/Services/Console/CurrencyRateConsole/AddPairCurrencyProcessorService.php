<?php

namespace App\Services\Console\CurrencyRateConsole;

use App\Model\Database\DatabaseInterface;

// use App\Models\DatabaseCRUD\DatabaseCRUDModel;
use App\Services\Console\CurrencyRateConsole\Interfaces\PairProcessorInterface;

class AddPairCurrencyProcessorService implements PairProcessorInterface
{
    private DatabaseInterface $databaseService;

    public function __construct(
        DatabaseInterface $databaseService,
    ) {
        $this->databaseService = $databaseService;
    }

    //Process pairs like USD -> GBP and GBP -> USD
    public function processAllPairs($argsFromConsole): void
    {
        $pairs = $argsFromConsole;
        
        if (empty($pairs)) {
            echo "*** No currency pairs found. \n";
            return;
        }
        // $this->databaseService->saveCurrencyPair('USD', 'EUR');
        foreach ($pairs as $index => $pair) {
           $processSinglePair = $this->processSinglePair($pair['from_currency'], $pair['to_currency']);
            
            if(!$processSinglePair) {
                echo "Currency pair ".$pair['from_currency']."-> ".$pair['to_currency']." already exists in the database.\n";
                echo "Currency pair ".$pair['to_currency']."-> ".$pair['from_currency']." already exists in the database.\n";
                break;
            } else if ($index === count($pairs) - 1) {
                echo "Currency pair ".$pair['from_currency']."-> ".$pair['to_currency']." succesfully inserted into database.\n";
                echo "Currency pair ".$pair['to_currency']."-> ".$pair['from_currency']." succesfully inserted into database.\n";
            }
        }
    }

    //Process currency pair and check if pair exists or not.
    private function processSinglePair(string $from, string $to): bool
    {
        $saveCurrencyPair = $this->saveCurrencyPairIfNotExist($from, $to);

        if($saveCurrencyPair) {
            return true;
        }
        return false;
    }

    //Save currency pair, if pair already exist in db, return false, else true.
    private function saveCurrencyPairIfNotExist(string $from, string $to): bool
    {
        return $this->databaseService->saveCurrencyPair($from, $to);
    }
}
