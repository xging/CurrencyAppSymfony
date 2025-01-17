<?php
namespace App\Tests\Services\Console\CurrencyRateConsole;

use App\Services\Console\CurrencyRateConsole\WatchPairProcessorService;
use App\Model\Database\DatabaseModel;
use App\Services\CurrencyRateExternalAPI\CurrencyRateExternalApiService;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class WatchPairProcessorServiceTest extends TestCase
{
    private WatchPairProcessorService $watchPairProcessorService;
    private DatabaseModel|MockObject $databaseModel;
    private CurrencyRateExternalApiService|MockObject $currencyRateApi;

    protected function setUp(): void
    {
        $this->databaseModel = $this->createMock(DatabaseModel::class);
        $this->currencyRateApi = $this->createMock(CurrencyRateExternalApiService::class);
        $this->watchPairProcessorService = new WatchPairProcessorService(
            $this->databaseModel,
            $this->currencyRateApi
        );
    }

    public function testProcessAllPairs(): void
    {
        $this->currencyRateApi->expects($this->exactly(2))
            ->method('fetchExchangeRate')
            ->willReturnOnConsecutiveCalls(1.2, 0.9);

        $this->databaseModel->method('checkIfPairExistsArray')
            ->willReturn([
                ['from_currency' => 'USD', 'to_currency' => 'EUR'],
                ['from_currency' => 'EUR', 'to_currency' => 'GBP']
            ]);

        ////Fail Case:    
        // $this->databaseModel->method('checkIfPairExistsArray')
        //     ->willReturn([]);


        $this->databaseModel->method('checkIfRateExistsBool')
            ->willReturn(false);

        $this->databaseModel->method('saveRate')
            ->willReturn(true);


        ob_start();
        $this->watchPairProcessorService->processAllPairs();
        $output = ob_get_clean();

        $this->assertStringContainsString('Saved rate: USD -> EUR', $output);
        $this->assertStringContainsString('Saved rate: EUR -> GBP', $output);

    }

    public function testProcessAllPairsNoPairs(): void
    {
        $this->databaseModel->expects($this->once())
            ->method('checkIfPairExistsArray')
            ->willReturn([]);

        ////Fail Case:  
        // $this->databaseModel->method('checkIfPairExistsArray')
        //     ->willReturn([
        //         ['from_currency' => 'USD', 'to_currency' => 'EUR'],
        //         ['from_currency' => 'EUR', 'to_currency' => 'GBP']
        //     ]);


        $this->expectOutputString("*** No currency pairs found. \n");
        $this->watchPairProcessorService->processAllPairs();
    }

    public function testProcessSinglePairRateUpdate(): void
    {
        $this->databaseModel->expects($this->once())
            ->method('checkIfPairExistsArray')
            ->willReturn([['from_currency' => 'USD', 'to_currency' => 'EUR']]);

        $this->currencyRateApi->expects($this->once())
            ->method('fetchExchangeRate')
            ->with('USD', 'EUR')
            ->willReturn(1.3);

        $this->databaseModel->expects($this->once())
            ->method('checkIfRateExistsBool')
            ->with('USD', 'EUR')
            ->willReturn(true);

        $this->databaseModel->expects($this->once())
            ->method('updateExchangeRate')
            ->with('USD', 'EUR', 1.3)
            ->willReturn(true);

        ////Fail Case:
        // $this->databaseModel->expects($this->once())
        //     ->method('updateExchangeRate')
        //     ->with('USDX', 'EURX', 1.3)
        //     ->willReturn(true);    


        $this->expectOutputString("*** Updated rate: USD -> EUR, rate: 1.3\n");
        $this->watchPairProcessorService->processAllPairs();
    }

    public function testProcessSinglePairRateFail(): void
    {
        $this->databaseModel->expects($this->once())
            ->method('checkIfPairExistsArray')
            ->willReturn([['from_currency' => 'USD', 'to_currency' => 'EUR']]);

        $this->currencyRateApi->expects($this->once())
            ->method('fetchExchangeRate')
            ->with('USD', 'EUR')
            ->willReturn(0.0);

        $this->databaseModel->expects($this->once())
            ->method('checkIfRateExistsBool')
            ->with('USD', 'EUR')
            ->willReturn(false);

         ////Fail Case:
        //  $this->databaseModel->expects($this->once())
        //  ->method('checkIfRateExistsBool')
        //  ->with('USD', 'EUR')
        //  ->willReturn(true);

        $this->databaseModel->expects($this->never())
            ->method('saveRate');

        $this->expectOutputString("*** Failed to save rate for: USD -> EUR\n");
        $this->watchPairProcessorService->processAllPairs();
    }
}
