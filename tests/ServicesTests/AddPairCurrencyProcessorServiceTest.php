<?php

namespace App\Tests\Services\Console\CurrencyRateConsole;

use App\Services\Console\CurrencyRateConsole\AddPairCurrencyProcessorService;
use App\Model\Database\DatabaseInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class AddPairCurrencyProcessorServiceTest extends TestCase
{
    private AddPairCurrencyProcessorService $service;
    private MockObject & DatabaseInterface $databaseService;

    protected function setUp(): void
    {
        $this->databaseService = $this->createMock(DatabaseInterface::class);
        $this->service = new AddPairCurrencyProcessorService($this->databaseService);
    }

    public function testProcessAllPairsSuccess(): void
    {
        $this->databaseService->method('saveCurrencyPair')->willReturn(true);

        $argsFromConsole = [
            ['from_currency' => 'GBP', 'to_currency' => 'EUR'],
            ['from_currency' => 'EUR', 'to_currency' => 'GBP']
        ];
        
        ////Fail Case:
        // $argsFromConsole = [
        //     ['from_currency' => 'GBPX', 'to_currency' => 'EUR'],
        //     ['from_currency' => 'EURX', 'to_currency' => 'GBP']
        // ];

        ob_start();
        $this->service->processAllPairs($argsFromConsole);
        $output = ob_get_clean();

        $this->assertStringContainsString('Currency pair EUR-> GBP succesfully inserted into database.', $output);
        $this->assertStringContainsString('Currency pair GBP-> EUR succesfully inserted into database.', $output);
    }

    public function testProcessAllPairsAlreadyExists(): void
    {
        $this->databaseService->method('saveCurrencyPair')->willReturn(false);
        $argsFromConsole = [
            ['from_currency' => 'USD', 'to_currency' => 'EUR'],
            ['from_currency' => 'EUR', 'to_currency' => 'USD']
        ];

        ////Fail Case:
        // $argsFromConsole = [
        //     ['from_currency' => 'USDX', 'to_currency' => 'EUR'],
        //     ['from_currency' => 'EURX', 'to_currency' => 'USD']
        // ];

        ob_start();
        $this->service->processAllPairs($argsFromConsole);
        $output = ob_get_clean();

        $this->assertStringContainsString('Currency pair USD-> EUR already exists in the database.', $output);
        $this->assertStringContainsString('Currency pair EUR-> USD already exists in the database.', $output);
    }

    public function testProcessAllPairsEmpty(): void
    {   
        ////Fail Case:
        // $argsFromConsole = [
        //     ['from_currency' => 'USD', 'to_currency' => 'EUR'],
        //     ['from_currency' => 'EUR', 'to_currency' => 'USD']
        // ];
        $argsFromConsole = [];

        ob_start();
        $this->service->processAllPairs($argsFromConsole);
        $output = ob_get_clean();

        $this->assertStringContainsString("*** No currency pairs found. \n", $output);
    }
}
