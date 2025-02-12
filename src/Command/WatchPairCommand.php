<?php

namespace App\Command;

use App\Message\WatchPairMessage;
use App\Model\Database\DatabaseModel;
use App\Services\Console\CurrencyRateConsole\WatchPairService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

class WatchPairCommand extends Command
{
    use HandleTrait;
    protected static $defaultName = 'app:watch-pair';
    private WatchPairService $watchPairService;
    private DatabaseModel $databaseModel;

    public function __construct(WatchPairService $watchPairService, DatabaseModel $databaseModel, MessageBusInterface $messageBus)
    {
        parent::__construct();
        $this->watchPairService = $watchPairService;
        $this->databaseModel = $databaseModel;
        $this->messageBus = $messageBus;
    }

    //Setup console command
    protected function configure(): void
    {
        $this
            ->setName('app:watch-pair')
            ->setDescription('Watch and process currency pairs')
            ->addArgument('argument', InputArgument::OPTIONAL, 'args');
    }

    //Process console command
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $argument = $input->getArgument('argument');

        if (!empty($argument)) {
            $output->writeln('Usage: php bin/console app:watch-pair"');
            return Command::FAILURE;
        }

        $message = new WatchPairMessage('WatchPairCommand');
        $result = $this->handle($message);
        $output->writeln("{$result}");

        // $this->watchPairService->execute([]);
        return Command::SUCCESS;
    }
}
