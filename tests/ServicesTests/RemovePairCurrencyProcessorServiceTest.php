<?php

namespace App\Tests\Services\Console\CurrencyRateConsole;

use App\Services\Console\CurrencyRateConsole\RemovePairCurrencyProcessorService;
use App\Model\Database\DatabaseInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class RemovePairCurrencyProcessorServiceTest extends TestCase
{
    private RemovePairCurrencyProcessorService $removePairCurrencyProcessorService;
    private DatabaseInterface|MockObject $databaseService;

    protected function setUp(): void
    {
        $this->databaseService = $this->createMock(DatabaseInterface::class);
        $this->removePairCurrencyProcessorService = new RemovePairCurrencyProcessorService($this->databaseService);
    }

    public function testProcessAllPairsSuccess(): void
    {
        $this->databaseService->expects($this->exactly(2))
            ->method('deleteCurrencyPair')
            ->with($this->anything(), $this->anything())
            ->willReturn(true);

        $this->databaseService->expects($this->exactly(2))
            ->method('deleteRates')
            ->with($this->anything(), $this->anything())
            ->willReturn(true);

        $pairs = [
            ['from_currency' => 'USD', 'to_currency' => 'EUR'],
            ['from_currency' => 'EUR', 'to_currency' => 'GBP']
        ];

        ////Fail Case:
        // $pairs = [
        //     ['from_currency' => 'USDX', 'to_currency' => 'EUR'],
        //     ['from_currency' => 'EUR', 'to_currency' => 'GBPX']
        // ];

        $this->expectOutputString(
            "Currency pairs USD -> EUR and EUR -> USD have been deleted.\n" . 
            "Currency pairs EUR -> GBP and GBP -> EUR have been deleted.\n"
        );

        $this->removePairCurrencyProcessorService->processAllPairs($pairs);
    }

    public function testProcessAllPairsEmpty(): void
    {
        $pairs = [];

        ////Fail Case:
        // $pairs = [
        //     ['from_currency' => 'USD', 'to_currency' => 'EUR'],
        //     ['from_currency' => 'EUR', 'to_currency' => 'GBP']
        // ];

        $this->expectOutputString("*** No currency pairs found. \n");
        $this->removePairCurrencyProcessorService->processAllPairs($pairs);
    }

    public function testProcessSinglePairFailure(): void
    {
        $this->databaseService->expects($this->once())
            ->method('deleteCurrencyPair')
            ->with('USD', 'EUR')
            ->willReturn(false);

        $this->databaseService->expects($this->once())
            ->method('deleteRates')
            ->with('USD', 'EUR')
            ->willReturn(true);

        $pairs = [
            ['from_currency' => 'USD', 'to_currency' => 'EUR']
        ];

        ////Fail Case:
        // $pairs = [];

        $this->expectOutputString(
            "Currency pairs USD -> EUR and EUR -> USD have not been deleted.\n"
        );

        $this->removePairCurrencyProcessorService->processAllPairs($pairs);
    }
}
