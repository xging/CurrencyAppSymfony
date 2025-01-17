<?php
namespace App\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Model\Database\DatabaseInterface;
use App\Services\Cache\CurrencyCacheService;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\DTO\CurrencyPairRequest;

class GetCurrencyRatesAPI extends AbstractController
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
        '/get-currency-rate/{from_currency}/{to_currency}',
        name: 'get_currency_rate',
        methods: ['GET']
    )]
    public function showPair(string $from_currency, string $to_currency): JsonResponse
    {
        $currencyPairRequest = new CurrencyPairRequest();
        $currencyPairRequest->from_currency = $from_currency;
        $currencyPairRequest->to_currency = $to_currency;

        $errors = $this->validator->validate($currencyPairRequest);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }

            return new JsonResponse(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $cacheKey = sprintf('CurrencyRate_%s%s', $from_currency, $to_currency);
        $currencyPairs = $this->currencyCacheService->getOrSetCache(
            $cacheKey,
            function () use ($from_currency, $to_currency) {
                return $this->database->showRatesPair($from_currency, $to_currency);
            }
        );

        return $this->json($currencyPairs);
    }
}
