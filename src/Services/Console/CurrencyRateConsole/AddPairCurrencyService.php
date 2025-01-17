<?php
namespace App\Services\Console\CurrencyRateConsole;

use App\Services\Console\CurrencyRateConsole\Interfaces\CurrencyRateConsoleInterface;
class AddPairCurrencyService implements CurrencyRateConsoleInterface
{

    private $addPairCurrencyProcessorService;
    public function __construct(AddPairCurrencyProcessorService $addPairCurrencyProcessorService)
    {
        $this->addPairCurrencyProcessorService = $addPairCurrencyProcessorService;
    }

    public function execute(array $args): void
    {
        echo "*** Adding currency pairs into the queue.\n";
        $this->addPairCurrencyProcessorService->processAllPairs($args);
    }
}
