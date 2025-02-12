<?php

namespace App\Command;

use App\Message\ShowPairMessage;
use App\Model\Database\DatabaseModel;
use App\Services\Console\CurrencyRateConsole\ShowPairRateService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;


class ShowPairCommand extends Command
{
    use HandleTrait;
    protected static $defaultName = 'app:show-pair';
    private ShowPairRateService $showPairRateService;
    private DatabaseModel $databaseModel;

    public function __construct(ShowPairRateService $showPairRateService, DatabaseModel $databaseModel, MessageBusInterface $messageBus)
    {
        parent::__construct();
        $this->showPairRateService = $showPairRateService;
        $this->databaseModel = $databaseModel;
        $this->messageBus = $messageBus;
    }

    //Setup console command
    protected function configure(): void
    {
        $this
            ->setName('app:show-pair')
            ->setDescription('Check exchange rate for selected currency pair')
            ->addArgument('argument', InputArgument::OPTIONAL, 'args');
    }

    //Process console command
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $argument = $input->getArgument('argument');

        if (count(explode(' ', $argument)) !== 2) {
            $output->writeln('Usage: php bin/console app:show-pair "<from_currency> <to_currency>"');
            return Command::FAILURE;
        }
        [$from, $to] = explode(' ', $argument);

        $argsFromConsole = [
            ['from_currency' => $from, 'to_currency' => $to],
            ['from_currency' => $to, 'to_currency' => $from]
        ];


        $message = new ShowPairMessage('ShowPairCommand', $argsFromConsole);
        $result = $this->handle($message);
        $output->writeln("{$result}");

        // $this->showPairRateService->execute($argsFromConsole);
        return Command::SUCCESS;
    }
}
