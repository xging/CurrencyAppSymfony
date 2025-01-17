<?php
namespace App\MessageHandler;

use App\Message\AddPairMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use App\Services\Console\CurrencyRateConsole\AddPairCurrencyService;

#[AsMessageHandler]
class AddPairMessageHandler
{
    private AddPairCurrencyService $addPairCurrencyService;

    public function __construct(AddPairCurrencyService $addPairCurrencyService)
    {
        $this->addPairCurrencyService = $addPairCurrencyService;
    }

    public function __invoke(AddPairMessage $message): void
    {
        $content = $message->getMessage();
        $args = $message->getArgs();
        echo "AddPairMessage content: $content\n";
        $this->addPairCurrencyService->execute($args);
    }
}
