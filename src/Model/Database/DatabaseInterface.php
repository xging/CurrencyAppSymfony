<?PHP
namespace App\Model\Database;

use App\Entity\Divisions;
use App\Entity\MatchesHist;
use App\Entity\TeamsMatch;

interface DatabaseInterface
{

     /***** db methods list*****/
     public function saveCurrencyPair(string $from, string $to): bool;
     public function deleteCurrencyPair(string $from, string $to): bool;
     public function saveRate(string $from, string $to, float $rate): bool;
     public function deleteRates(string $from, string $to): bool;
     public function checkIfPairExistsArray():array;
     public function checkIfPairExistsBool(string $from, string $to): bool;
     public function checkIfRateExistsBool(string $from, string $to): bool;
     public function showRatesPair(string $from, string $to): array;
     public function showRatesPairHist(string $from, string $to, ?string $date, ?string $time): array;
     public function saveRateHistory(string $from, string $to, float $old_rate, float $new_rate):bool;
     public function updateExchangeRate(string $from, string $to, float $new_rate):bool;
     public function getCurrencyRates(string $from, string $to): array;
     public function getCurrencyRatesHist(string $from, string $to, $date, $dateFormat): array;
}