<?php
namespace App\Controller\API;

use App\DTO\CurrencyPairRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Model\Database\DatabaseInterface;
use App\Services\Cache\CurrencyCacheService;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GetCurrencyRatesHistAPI extends AbstractController
{
    private DatabaseInterface $database;
    private CurrencyCacheService $currencyCacheService;
    private ValidatorInterface $validator;

    public function __construct(
        DatabaseInterface $database,
        CurrencyCacheService $currencyCacheService,
        ValidatorInterface $validator
    ) {
        $this->database = $database;
        $this->currencyCacheService = $currencyCacheService;
        $this->validator = $validator;
    }

    #[Route(
        '/get-currency-hist/{from_currency}/{to_currency}/{to_date?}/{to_time?}',
        name: 'get_currency_hist',
        methods: ['GET']
    )]
    public function showPair(
        string $from_currency,
        string $to_currency,
        ?string $to_date = null,
        ?string $to_time = null
    ): JsonResponse {
        $currencyPairRequest = new CurrencyPairRequest();
        $currencyPairRequest->from_currency = $from_currency;
        $currencyPairRequest->to_currency = $to_currency;
        $currencyPairRequest->to_date = $to_date;
        $currencyPairRequest->to_time = $to_time;

        $errors = $this->validator->validate($currencyPairRequest);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }

            return new JsonResponse(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $cacheKey = sprintf('CurrencyRateHist_%s%s%s%s', $from_currency, $to_currency, str_replace('-', '', $to_date), str_replace(':', '', $to_time));
        $currencyPairs = $this->currencyCacheService->getOrSetCache(
            $cacheKey,
            function () use ($from_currency, $to_currency, $to_date, $to_time) {
                return $this->database->showRatesPairHist($from_currency, $to_currency, $to_date, $to_time);
            }
        );

        if (empty($currencyPairs)) {
            return new JsonResponse(['error' => 'No data found for the specified currency pair.'], Response::HTTP_NOT_FOUND);
        }

        return $this->json(['data' => $currencyPairs]);
    }
}