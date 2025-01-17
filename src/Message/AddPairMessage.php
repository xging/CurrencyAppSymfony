<?php
namespace App\Message;

class AddPairMessage
{
    private string $message;
    private array $args;

    public function __construct(
        string $message,
        array $args
    ) {
        $this->message = $message;
        $this->args = $args;
    }

    public function getArgs(): array
    {
        return $this->args;
    }
    public function getMessage(): string
    {
        return $this->message;
    }
}