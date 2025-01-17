<?php

namespace App\Model\Database;

use App\Entity\CurrencyPairs;
use App\Entity\ExchangeRate;
use App\Entity\ExchangeRateHist;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class DatabaseModel implements DatabaseInterface
{
    private EntityManagerInterface $em;
    private LoggerInterface $logger;

    public function __construct(EntityManagerInterface $em, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->logger = $logger;
    }
    public function saveCurrencyPair(string $from, string $to): bool
    {
        if (!$this->checkIfPairExistsBool($from, $to)) {
            $currencyPair = new CurrencyPairs();
            $currencyPair->setFromCurrency($from);
            $currencyPair->setToCurrency($to);
            $currencyPair->setCreationDate(new \DateTime());

            $this->em->persist($currencyPair);
            $this->em->flush();

            $this->logger->info("Currency pair saved: {$from} -> {$to}");
            return true;
        }
        return false;
    }

    public function deleteCurrencyPair(string $from, string $to): bool
    {
        $currencyPair = $this->em->getRepository(CurrencyPairs::class)
            ->findOneBy(['from_currency' => $from, 'to_currency' => $to]);

        if ($currencyPair) {
            $this->em->remove($currencyPair);
            $this->em->flush();

            $this->logger->info("Currency pair deleted: {$from} -> {$to}");
            return true;
        } else {
            $this->logger->warning("Currency pair not found: {$from} -> {$to}");
            return false;
        }
    }

    public function saveRate(string $from, string $to, float $rate): bool
    {
        if (!$this->checkIfRateExistsBool($from, $to)) {
            $exchangeRate = new ExchangeRate();
            $exchangeRate->setFromCurrency($from);
            $exchangeRate->setToCurrency($to);
            $exchangeRate->setRate($rate);
            $exchangeRate->setCreationDate(new \DateTime());

            $this->em->persist($exchangeRate);
            $this->em->flush();

            $this->logger->info("Exchange rate saved: {$from} -> {$to} with rate {$rate}");
            return true;
        }
        return false;
    }

    public function deleteRates(string $from, string $to): bool
    {
        $exchangeRate = $this->em->getRepository(ExchangeRate::class)
            ->findOneBy(['from_currency' => $from, 'to_currency' => $to]);

        if ($exchangeRate) {
            $this->em->remove($exchangeRate);
            $this->em->flush();

            $this->logger->info("Exchange rate deleted: {$from} -> {$to}");
            return true;
        } else {
            $this->logger->warning("Exchange rate not found: {$from} -> {$to}");
            return false;
        }
    }
    public function checkIfPairExistsBool(string $from, string $to): bool
    {
        $currencyPair = $this->em->getRepository(CurrencyPairs::class)
            ->findOneBy(['from_currency' => $from, 'to_currency' => $to]);

        return $currencyPair !== null;
    }

    public function checkIfPairExistsArray(): array
    {
        $currencyPairs = $this->em->getRepository(CurrencyPairs::class)
            ->findAll();
        if ($currencyPairs) {
            return array_map(function ($currencyPair) {
                return [
                    'from_currency' => $currencyPair->getFromCurrency(),
                    'to_currency' => $currencyPair->getToCurrency(),
                ];
            }, $currencyPairs);
        }
        return [];
    }

    public function checkIfRateExistsBool(string $from, string $to): bool
    {
        $exchangeRate = $this->em->getRepository(ExchangeRate::class)
            ->findOneBy(['from_currency' => $from, 'to_currency' => $to]);

        return $exchangeRate !== null;
    }

    public function showRatesPair(string $from, string $to): array
    {
        $rates = $this->em->getRepository(ExchangeRate::class)
            ->findBy(
                ['from_currency' => $from, 'to_currency' => $to],
                ['id' => 'DESC']
            );

        $result = [];
        foreach ($rates as $rate) {
            $result[] = [
                'from_currency' => $rate->getFromCurrency(),
                'to_currency' => $rate->getToCurrency(),
                'rate' => $rate->getRate(),
            ];
        }

        return $result;
    }

    public function showRatesPairHist(string $from, string $to, ?string $date, ?string $time): array
    {
        $qb = $this->em->createQueryBuilder();

        $qb->select('e')
            ->from(ExchangeRateHist::class, 'e')
            ->where('e.from_currency = :from_currency')
            ->andWhere('e.to_currency = :to_currency')
            ->setParameter('from_currency', $from)
            ->setParameter('to_currency', $to)
            ->orderBy('e.id', 'DESC');

        if ($time === null && $date !== null) {
            $startOfDay = new \DateTime($date . ' 00:00:00');
            $endOfDay = new \DateTime($date . ' 23:59:59');

            $qb->andWhere('e.creation_date BETWEEN :start AND :end')
                ->setParameter('start', $startOfDay)
                ->setParameter('end', $endOfDay);
        } elseif ($time !== null && $date !== null) {
            $dateTime = \DateTime::createFromFormat('Y-m-d H:i:s', $date . ' ' . $time);

            if (!$dateTime) {
                throw new \InvalidArgumentException('Invalid date or time format.');
            }

            $qb->andWhere('e.creation_date = :creation_date')
                ->setParameter('creation_date', $dateTime);
        } else {
            $qb->andWhere('e.creation_date IS NOT NULL');
        }

        $rates = $qb->getQuery()->getResult();

        $result = [];
        foreach ($rates as $rate) {
            $result[] = [
                'from_currency' => $rate->getFromCurrency(),
                'to_currency' => $rate->getToCurrency(),
                'old_rate' => $rate->getOldRate(),
                'last_rate' => $rate->getNewRate(),
                'update_date' => $rate->getLastUpdateDate()->format('Y-m-d H:i:s'),
                'creation_date' => $rate->getCreationDate()->format('Y-m-d H:i:s'),
            ];
        }

        return $result;
    }


    public function saveRateHistory(string $from, string $to, float $old_rate, float $new_rate): bool
    {
        $exchangeRateHist = new ExchangeRateHist();
        $exchangeRateHist->setFromCurrency($from);
        $exchangeRateHist->setToCurrency($to);
        $exchangeRateHist->setOldRate($old_rate);
        $exchangeRateHist->setNewRate($new_rate);
        $exchangeRateHist->setCreationDate(new \DateTime());
        $exchangeRateHist->setLastUpdateDate(new \DateTime());
        $this->em->persist($exchangeRateHist);
        $this->em->flush();

        $this->logger->info("Exchange rate history saved: {$from} -> {$to} from {$old_rate} to {$new_rate}");
        return true;
    }

    public function updateExchangeRate(string $from, string $to, float $new_rate): bool
    {
        $exchangeRate = $this->em->getRepository(ExchangeRate::class)
            ->findOneBy(['from_currency' => $from, 'to_currency' => $to]);

        if ($exchangeRate) {
            $oldRate = $exchangeRate->getRate();
            $exchangeRate->setRate($new_rate);
            $exchangeRate->setLastUpdateDate(new \DateTime());
            $this->em->flush();
            $this->saveRateHistory($from, $to, $oldRate, $new_rate);

            $this->logger->info("Exchange rate updated: {$from} -> {$to} to {$new_rate}");
            return true;
        }

        return false;
    }

    public function getCurrencyRates(string $from, string $to): array
    {
        return $this->em->getRepository(ExchangeRate::class)
            ->findBy(['from_currency' => $from, 'to_currency' => $to], ['id' => 'DESC'], 2);
    }

    public function getCurrencyRatesHist(string $from, string $to, $date, $dateFormat): array
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('e')
            ->from(ExchangeRateHist::class, 'e')
            ->where($qb->expr()->in('e.from_currency', ':from'))
            ->andWhere($qb->expr()->in('e.to_currency', ':to'))
            ->andWhere("DATE_FORMAT(e.creationDate, :dateFormat) = :date")
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->setParameter('date', $date)
            ->setParameter('dateFormat', $dateFormat)
            ->orderBy('e.creationDate', 'ASC');

        return $qb->getQuery()->getResult();
    }

}
