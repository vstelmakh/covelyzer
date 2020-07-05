<?php

declare(strict_types=1);

namespace VStelmakh\Covelyzer;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use VStelmakh\Covelyzer\Report\FileCoverageReport;
use VStelmakh\Covelyzer\Report\ProjectCoverageReport;
use VStelmakh\Covelyzer\Report\ReportInterface;
use VStelmakh\Covelyzer\Util\FileReader;
use VStelmakh\Covelyzer\Dom\DocumentFactory;

class CovelyzerCommand extends Command
{
    public const SUCCESS = 0;
    public const FAILURE = 1;

    protected function configure(): void
    {
        $this->setName('covelyzer');
        $this->addArgument('coverage', InputArgument::REQUIRED, 'Path to coverage report file .xml');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $documentFactory = new DocumentFactory();
        $fileReader = new FileReader();
        $coverageParser = new CoverageParser($documentFactory, $fileReader);

        /** @var string $coverageFilePath */
        $coverageFilePath = $input->getArgument('coverage');
        $project = $coverageParser->parseCoverage($coverageFilePath);

        $datetime = $project->getTimestamp();
        $displayDateTime = $datetime ? $datetime->format('Y-m-d H:i:s') : 'not defined';
        $output->writeln('Coverage timestamp: ' . $displayDateTime);

        $status = self::SUCCESS;

        $coverageCalculator = new CoverageCalculator(2);
        $reports = [
            new ProjectCoverageReport($project, $coverageCalculator, 100),
            new FileCoverageReport($project, $coverageCalculator, 100),
        ];

        /** @var ReportInterface $report */
        foreach ($reports as $report) {
            $output->writeln('');
            $report->render($output);
            $status = $report->isSuccess() === false ? self::FAILURE : $status;
        }

        return $status;
    }
}
