<?php

namespace App\Entity;

use App\Repository\ExchangeRateHistRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExchangeRateHistRepository::class)]
class ExchangeRateHist
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 3)]
    private ?string $from_currency = null;

    #[ORM\Column(length: 3)]
    private ?string $to_currency = null;

    #[ORM\Column(nullable: true)]
    private ?float $old_rate = null;

    #[ORM\Column(nullable: true)]
    private ?float $new_rate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $last_update_date = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $creation_date = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFromCurrency(): ?string
    {
        return $this->from_currency;
    }

    public function setFromCurrency(string $from_currency): static
    {
        $this->from_currency = $from_currency;

        return $this;
    }

    public function getToCurrency(): ?string
    {
        return $this->to_currency;
    }

    public function setToCurrency(string $to_currency): static
    {
        $this->to_currency = $to_currency;

        return $this;
    }

    public function getOldRate(): ?float
    {
        return $this->old_rate;
    }

    public function setOldRate(?float $old_rate): static
    {
        $this->old_rate = $old_rate;

        return $this;
    }

    public function getNewRate(): ?float
    {
        return $this->new_rate;
    }

    public function setNewRate(?float $new_rate): static
    {
        $this->new_rate = $new_rate;

        return $this;
    }

    public function getLastUpdateDate(): ?\DateTimeInterface
    {
        return $this->last_update_date;
    }

    public function setLastUpdateDate(?\DateTimeInterface $last_update_date): static
    {
        $this->last_update_date = $last_update_date;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creation_date;
    }

    public function setCreationDate(\DateTimeInterface $creation_date): static
    {
        $this->creation_date = $creation_date;

        return $this;
    }
}
