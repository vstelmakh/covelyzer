<?php

declare(strict_types=1);

namespace VStelmakh\Covelyzer\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use VStelmakh\Covelyzer\Console\CovelyzerStyle;
use VStelmakh\Covelyzer\CoverageParser;
use VStelmakh\Covelyzer\Report\ClassCoverageReport;
use VStelmakh\Covelyzer\Report\ProjectCoverageReport;
use VStelmakh\Covelyzer\Report\ReportInterface;
use VStelmakh\Covelyzer\Util\FileReader;
use VStelmakh\Covelyzer\Dom\DocumentFactory;

class CovelyzerCommand extends Command
{
    public const SUCCESS = 0;
    public const FAILURE = 1;

    /**
     * @var CovelyzerStyle
     */
    private $covelyzerStyle;

    protected function configure(): void
    {
        $this->setName('covelyzer');
        $this->addArgument('coverage', InputArgument::REQUIRED, 'Path to coverage report file .xml');
        $this->addUsage('path/to/coverage.xml');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->covelyzerStyle = new CovelyzerStyle($output);
        parent::initialize($input, $output);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->covelyzerStyle->title('Covelyzer');
        $this->covelyzerStyle->newLine();
        $minProjectCoverage = 100;
        $minClassCoverage = 100;

        $documentFactory = new DocumentFactory();
        $fileReader = new FileReader();
        $coverageParser = new CoverageParser($documentFactory, $fileReader);

        /** @var string $coverageFilePath */
        $coverageFilePath = $input->getArgument('coverage');
        /** @var string $coverageFilePath */
        $coverageFilePath = realpath($coverageFilePath);
        $this->covelyzerStyle->writeln('  Report:    ' . $coverageFilePath);
        $project = $coverageParser->parseCoverage($coverageFilePath);

        $datetime = $project->getTimestamp();
        $displayDateTime = $datetime ? $datetime->format('Y-m-d H:i:s') : '--';
        $this->covelyzerStyle->writeln('  Timestamp: ' . $displayDateTime);

        $status = self::SUCCESS;

        $reports = [
            new ProjectCoverageReport($project, $minProjectCoverage),
            new ClassCoverageReport($project, $minClassCoverage),
        ];

        /** @var ReportInterface $report */
        foreach ($reports as $report) {
            $this->covelyzerStyle->newLine();
            $report->render($this->covelyzerStyle);
            $status = $report->isSuccess() === false ? self::FAILURE : $status;
        }

        $this->covelyzerStyle->status($status);
        return $status;
    }
}
