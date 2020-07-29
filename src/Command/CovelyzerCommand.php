<?php

declare(strict_types=1);

namespace VStelmakh\Covelyzer\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use VStelmakh\Covelyzer\Parser\ConfigParser;
use VStelmakh\Covelyzer\Console\ClassCoverageTableRenderer;
use VStelmakh\Covelyzer\Console\CovelyzerStyle;
use VStelmakh\Covelyzer\CoverageParser;
use VStelmakh\Covelyzer\Report\ClassCoverageReport;
use VStelmakh\Covelyzer\Report\ProjectCoverageReport;
use VStelmakh\Covelyzer\Report\ReportInterface;

class CovelyzerCommand extends Command
{
    public const SUCCESS = 0;
    public const FAILURE = 1;

    /**
     * @var CovelyzerStyle
     */
    private $covelyzerStyle;

    /**
     * @var ConfigParser
     */
    private $configParser;

    /**
     * @var CoverageParser
     */
    private $coverageParser;

    /**
     * @param ConfigParser $configParser
     * @param CoverageParser $coverageParser
     */
    public function __construct(ConfigParser $configParser, CoverageParser $coverageParser)
    {
        $this->configParser = $configParser;
        $this->coverageParser = $coverageParser;
        parent::__construct(null);
    }

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

        $config = $this->configParser->parseConfig('covelyzer.xml');
        $minProjectCoverage = $config->getMinProjectCoverage();
        $minClassCoverage = $config->getMinClassCoverage();

        /** @var string $coverageFilePath */
        $coverageFilePath = $input->getArgument('coverage');
        /** @var string $coverageFilePath */
        $coverageFilePath = realpath($coverageFilePath);
        $this->covelyzerStyle->writeln('  Report:    ' . $coverageFilePath);
        $project = $this->coverageParser->parseCoverage($coverageFilePath);

        $datetime = $project->getTimestamp();
        $displayDateTime = $datetime ? $datetime->format('Y-m-d H:i:s') : '--';
        $this->covelyzerStyle->writeln('  Timestamp: ' . $displayDateTime);

        $status = self::SUCCESS;

        $tableRenderer = new ClassCoverageTableRenderer();
        $reports = [
            new ProjectCoverageReport($project, $minProjectCoverage, $tableRenderer),
            new ClassCoverageReport($project, $minClassCoverage, $tableRenderer),
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
