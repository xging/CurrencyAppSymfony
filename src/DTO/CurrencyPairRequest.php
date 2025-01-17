<?php
namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CurrencyPairRequest
{
    #[Assert\NotBlank(message: "From currency is required")]
    #[Assert\Length(min: 3, max: 3, exactMessage: "From currency must be 3 letters")]
    #[Assert\Regex("/^[A-Za-z]+$/", message: "From currency must contain only letters")]
    public string $from_currency;

    #[Assert\NotBlank(message: "To currency is required")]
    #[Assert\Length(min: 3, max: 3, exactMessage: "To currency must be 3 letters")]
    #[Assert\Regex("/^[A-Za-z]+$/", message: "To currency must contain only letters")]
    public string $to_currency;

    #[Assert\Date(message: "Invalid date format")]
    public ?string $to_date;

    #[Assert\Time(message: "Invalid time format")]
    public ?string $to_time;
}