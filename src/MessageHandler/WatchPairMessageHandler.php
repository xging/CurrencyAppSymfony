<?php
namespace App\MessageHandler;

use App\Message\WatchPairMessage;
use App\Services\Console\CurrencyRateConsole\WatchPairService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;




#[AsMessageHandler]
class WatchPairMessageHandler
{
    private WatchPairService $watchPairService;

    public function __construct(WatchPairService $watchPairService)
    {
        $this->watchPairService = $watchPairService;
    }

    public function __invoke(WatchPairMessage $message): void
    {
        $content = $message->getMessage();
        echo "WatchPairMessage content: $content\n";
        $this->watchPairService->execute([]);
    }
}
