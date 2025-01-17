<?php

namespace App\Command;

use App\Message\AddPairMessage;
use App\Model\Database\DatabaseModel;
use App\Services\Console\CurrencyRateConsole\AddPairCurrencyService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

class AddPairCommand extends Command
{
    private MessageBusInterface $messageBus;
    protected static $defaultName = 'app:add-pair';
    private AddPairCurrencyService $addPairCurrencyService;
    private DatabaseModel $databaseModel;

    public function __construct(
        AddPairCurrencyService $addPairCurrencyService,
        DatabaseModel $databaseModel,
        MessageBusInterface $messageBus
    ) {
        parent::__construct();
        $this->addPairCurrencyService = $addPairCurrencyService;
        $this->databaseModel = $databaseModel;
        $this->messageBus = $messageBus;
    }
    //Setup console command
    protected function configure(): void
    {
        $this
            ->setName('app:add-pair')
            ->setDescription('Add Currency pair into queue')
            ->addArgument('argument', InputArgument::OPTIONAL, 'args');
    }

    //Process console command
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $argument = $input->getArgument('argument');

        if (count(explode(' ', $argument)) !== 2) {
            $output->writeln('Usage: php bin/console app:add-pair "<from_currency> <to_currency>"');
            return Command::FAILURE;
        }
        [$from, $to] = explode(' ', $argument);

        $argsFromConsole = [
            ['from_currency' => $from, 'to_currency' => $to],
            ['from_currency' => $to, 'to_currency' => $from]
        ];

        $this->messageBus->dispatch(new AddPairMessage('AddPairCommand',$argsFromConsole));
        $output->writeln("Currencies pair {$from} - {$to} have been sent to the queue for processing.");
        return Command::SUCCESS;
    }
}
