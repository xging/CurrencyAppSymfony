<?php

namespace App\Tests\Services\Console\CurrencyRateConsole;

use App\Services\Console\CurrencyRateConsole\ShowPairRateService;
use App\Model\Database\DatabaseModel;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ShowPairRateServiceTest extends TestCase
{
    private ShowPairRateService $showPairRateService;
    private DatabaseModel|MockObject $databaseModel;

    protected function setUp(): void
    {
        $this->databaseModel = $this->createMock(DatabaseModel::class);
        $this->showPairRateService = new ShowPairRateService($this->databaseModel);
    }

    public function testExecuteSuccess(): void
    {
        $this->databaseModel->expects($this->once())
            ->method('showRatesPair')
            ->with('USD', 'EUR')
            ->willReturn([
                ['from_currency' => 'USD', 'to_currency' => 'EUR', 'rate' => 1.1],
                ['from_currency' => 'USD', 'to_currency' => 'EUR', 'rate' => 1.2]
            ]);

        $pairs = [
            ['from_currency' => 'USD', 'to_currency' => 'EUR']
        ];

        ////Fail Case:
        // $pairs = [];

        $this->expectOutputString(
            "From: USD\nTo: EUR\nRate: 1.1\n----------------------------------------\n" .
            "From: USD\nTo: EUR\nRate: 1.2\n----------------------------------------\n"
        );

        $this->showPairRateService->execute($pairs);
    }

    public function testExecuteNoRatesFound(): void
    {
        $this->databaseModel->expects($this->once())
            ->method('showRatesPair')
            ->with('USD', 'EUR')
            ->willReturn([]);

        $pairs = [
            ['from_currency' => 'USD', 'to_currency' => 'EUR']
        ];
        
         ////Fail Case:
        // $pairs = [];

        $this->expectOutputString(
            "No rates found for the pair USD ->EUR.\n"
        );
        $this->showPairRateService->execute($pairs);
    }
}
