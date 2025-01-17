<?php
namespace App\Services\Console\CurrencyRateConsole;

use App\Model\Database\DatabaseModel;
use App\Services\Console\CurrencyRateConsole\Interfaces\CurrencyRateConsoleInterface;

class ShowPairRateService implements CurrencyRateConsoleInterface
{
    private $DatabaseModel;

    public function __construct(DatabaseModel $databaseModel)
    {
        $this->DatabaseModel = $databaseModel;
    }

    //Show info about currency rate pair
    public function execute(array $args): void
    {
        foreach ($args as $argsKeys) {
            $showPairRates = $this->DatabaseModel->showRatesPair($argsKeys['from_currency'], $argsKeys['to_currency']);
            if ($showPairRates) {
                foreach ($showPairRates as $rate) {
                    echo "From: " . $rate['from_currency'] . "\n";
                    echo "To: " . $rate['to_currency'] . "\n";
                    echo "Rate: " . $rate['rate'] . "\n";
                    echo str_repeat("-", 40) . "\n";
                }
            } else {
                echo "No rates found for the pair " . $argsKeys['from_currency'] . " ->" . $argsKeys['to_currency'] . ".\n";
            }
        }
    }
}
