<?php
namespace App\Services\CurrencyRateExternalAPI;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CurrencyRateExternalApiService
{
    private $curl;
    private $params;

    public function __construct(CurlWrapper $curl, ParameterBagInterface $params)
    {
        $this->curl = $curl;
        $this->params = $params; 
    }

    public function fetchExchangeRate(string $from, string $to): ?float
    {
        $key = $this->params->get('CURRENCY_API_KEY');  // Получаем ключ из параметров
        $url = "https://api.freecurrencyapi.com/v1/latest?apikey={$key}&base_currency={$from}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $response = $this->curl->exec($ch);

        if ($response === false) {
            $error = $this->curl->error($ch);
            $this->curl->close($ch);
            return null;
        }

        $this->curl->close($ch);

        $data = json_decode($response, true);

        if (isset($data['data'][$to])) {
            return (float) $data['data'][$to];
        } else {
            return null;
        }
    }
}
