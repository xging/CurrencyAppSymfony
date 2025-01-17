<?php
namespace App\MessageHandler;

use App\Message\ShowPairMessage;
use App\Services\Console\CurrencyRateConsole\ShowPairRateService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;



#[AsMessageHandler]
class ShowPairMessageHandler
{
    private ShowPairRateService $showPairRateService;

    public function __construct(ShowPairRateService $showPairRateService)
    {
        $this->showPairRateService = $showPairRateService;
    }

    public function __invoke(ShowPairMessage $message): void
    {
        $content = $message->getMessage();
        $args = $message->getArgs();
        // echo json_encode($args);
        echo "ShowPairMessage content: $content\n";
        $this->showPairRateService->execute($args);
    }
}
