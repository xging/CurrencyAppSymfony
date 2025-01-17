<?php
namespace App\Services\Console\CurrencyRateConsole\Interfaces;

interface PairProcessorInterface {
    public function processAllPairs($argsFromConsole): void;
}