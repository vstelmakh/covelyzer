<?php

declare(strict_types=1);

namespace VStelmakh\Covelyzer\Command;

use Symfony\Component\Console\Command\HelpCommand as SymfonyHelpCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use VStelmakh\Covelyzer\Console\CovelyzerStyle;

class HelpCommand extends SymfonyHelpCommand
{
    /**
     * @var CovelyzerStyle
     */
    private $covelyzerStyle;

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->covelyzerStyle = new CovelyzerStyle($output);
        parent::initialize($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->covelyzerStyle->header();

        $application = $this->getApplication();
        if ($application !== null) {
            $version = $application->getLongVersion();
            $this->covelyzerStyle->writeln($version);
            $this->covelyzerStyle->newLine();
        }

        return (int) parent::execute($input, $output);
    }
}
