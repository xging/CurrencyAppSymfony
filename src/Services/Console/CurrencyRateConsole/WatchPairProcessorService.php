<?php

namespace App\Services\Console\CurrencyRateConsole;

use App\Model\Database\DatabaseModel;
use App\Services\CurrencyRateExternalAPI\CurrencyRateExternalApiService;
use App\Services\Console\CurrencyRateConsole\Interfaces\PairProcessorInterface;

class WatchPairProcessorService
{
    private databaseModel $databaseModel;
    private CurrencyRateExternalApiService $currencyRateApi;

    public function __construct(
        databaseModel $databaseModel,
        CurrencyRateExternalApiService $currencyRateApi,
    ) {
        $this->databaseModel = $databaseModel;
        $this->currencyRateApi = $currencyRateApi;
    }

    //Proceess all currency pairs like USD -> EUR and EUR -> USD
    public function processAllPairs(): void
    {
        $pairs = $this->databaseModel->checkIfPairExistsArray();

        if (empty($pairs)) {
            echo "*** No currency pairs found. \n";
            return;
        }

        foreach ($pairs as $pair) {
            $this->processSinglePair($pair['from_currency'], $pair['to_currency']);
        }
    }

    //Process single currency rate pair like USD -> EUR
    private function processSinglePair(string $from, string $to): void
    {
        $rate = $this->currencyRateApi->fetchExchangeRate($from, $to) ?? 1.0;
        
        if (!$this->databaseModel->checkIfRateExistsBool($from, $to)) {
            if($rate === 0.0) {
                echo "*** Failed to save rate for: {$from} -> {$to}\n";
                return;
            }
            if ($this->databaseModel->saveRate($from, $to, $rate)) {
                echo "*** Saved rate: {$from} -> {$to}, rate: {$rate}\n";
            } else {
                echo "*** Failed to save rate for: {$from} -> {$to}\n";
            }
        } elseif ($this->databaseModel->updateExchangeRate($from, $to, $rate)) {
            echo "*** Updated rate: {$from} -> {$to}, rate: {$rate}\n";
        } else {
            echo "*** Failed to update rate for: {$from} -> {$to}\n";
        }
    }
}
