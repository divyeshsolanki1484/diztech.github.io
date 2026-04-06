<?php

declare(strict_types=1);

namespace Zeobv\AbandonedCart\Command\AbandonedCart;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Zeobv\AbandonedCart\ScheduledTasks\Handlers\AbandonedCart\CollectTaskHandler;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'zeo:abandoned-cart:collect',
    description: 'Perform the Abandoned Cart CollectTask',
)]
class Collect extends Command
{
    public function __construct(
        private CollectTaskHandler $collectTaskHandler,
        ?string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setName('zeo:abandoned-cart:collect');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Running Abandoned Cart CollectTask...');

        $this->collectTaskHandler->run();

        $output->writeln('Abandoned Cart CollectTask finished.');

        return 0;
    }
}
