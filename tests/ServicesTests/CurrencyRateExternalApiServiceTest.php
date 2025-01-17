<?php
namespace App\Tests\Services\CurrencyRateExternalAPI;

use PHPUnit\Framework\TestCase;
use App\Services\CurrencyRateExternalAPI\CurrencyRateExternalApiService;
use App\Services\CurrencyRateExternalAPI\CurlWrapper;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
class CurrencyRateExternalApiServiceTest extends TestCase
{
    public function testFetchExchangeRateSuccess(): void
    {
        $mockCurl = $this->createMock(CurlWrapper::class);
        $paramsMock = $this->createMock(ParameterBagInterface::class);
        $mockCurl->method('exec')->willReturn(json_encode([
            'data' => [
                'EUR' => 1.15,
            ],
        ]));

        $service = new CurrencyRateExternalApiService($mockCurl, $paramsMock);

        ////Fail Case:
        // $rate = $service->fetchExchangeRate('GBP', 'USD');
        // $this->assertEquals(1.16, $rate);

        $rate = $service->fetchExchangeRate('GBP', 'EUR');
        $this->assertEquals(1.15, $rate);
    }

    public function testFetchExchangeRateCurlError(): void
    {
        $mockCurl = $this->createMock(CurlWrapper::class);
        $paramsMock = $this->createMock(ParameterBagInterface::class);
        $mockCurl->method('exec')->willReturn(false);
        $mockCurl->method('error')->willReturn('Mocked cURL error');

        ////Fail Case:
        // $mockCurl->method('exec')->willReturn(json_encode([
        //     'data' => [
        //         'EUR' => 1.15,
        //     ],
        // ]));

        $service = new CurrencyRateExternalApiService($mockCurl, $paramsMock);
        $rate = $service->fetchExchangeRate('GBP', 'EUR');
        $this->assertNull($rate);
    }

    public function testFetchExchangeRateNoData(): void
    {
        $mockCurl = $this->createMock(CurlWrapper::class);
        $paramsMock = $this->createMock(ParameterBagInterface::class);
        $mockCurl->method('exec')->willReturn(json_encode([
            'data' => [],
        ]));

        ////Fail Case:
        // $mockCurl->method('exec')->willReturn(json_encode([
        //     'data' => ['EUR' => 1.15,],
        // ]));


        $service = new CurrencyRateExternalApiService($mockCurl, $paramsMock);
        $rate = $service->fetchExchangeRate('GBP', 'EUR');
        $this->assertNull($rate);
    }
}
