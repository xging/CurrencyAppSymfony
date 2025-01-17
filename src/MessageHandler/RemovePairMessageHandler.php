<?php
namespace App\MessageHandler;

use App\Message\RemovePairMessage;
use App\Services\Console\CurrencyRateConsole\RemovePairCurrencyService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class RemovePairMessageHandler
{
    private RemovePairCurrencyService $removePairCurrencyService;

    public function __construct(RemovePairCurrencyService $removePairCurrencyService)
    {
        $this->removePairCurrencyService = $removePairCurrencyService;
    }

    public function __invoke(RemovePairMessage $message): void
    {
        $content = $message->getMessage();
        $args = $message->getArgs();
        echo "RemovePairMessage content: $content\n";
        $this->removePairCurrencyService->execute($args);
    }
}
