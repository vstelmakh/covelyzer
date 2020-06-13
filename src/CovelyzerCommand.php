<?php

declare(strict_types=1);

namespace VStelmakh\Covelyzer;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CovelyzerCommand extends Command
{
    public const SUCCESS = 0;
    public const FAILURE = 1;

    protected function configure(): void
    {
        $this->setName('Covelyzer');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('It works!');
        return self::SUCCESS;
    }
}
