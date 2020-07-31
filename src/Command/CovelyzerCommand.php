<?php

declare(strict_types=1);

namespace VStelmakh\Covelyzer\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use VStelmakh\Covelyzer\Entity\Config;
use VStelmakh\Covelyzer\Entity\Project;
use VStelmakh\Covelyzer\Parser\ConfigParser;
use VStelmakh\Covelyzer\Console\ClassCoverageTableRenderer;
use VStelmakh\Covelyzer\Console\CovelyzerStyle;
use VStelmakh\Covelyzer\Parser\CoverageParser;
use VStelmakh\Covelyzer\Report\ClassCoverageReport;
use VStelmakh\Covelyzer\Report\ProjectCoverageReport;
use VStelmakh\Covelyzer\Report\ReportInterface;

class CovelyzerCommand extends Command
{
    public const SUCCESS = 0;
    public const FAILURE = 1;
    private const ARG_COVERAGE = 'coverage';

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
        $this->addArgument(self::ARG_COVERAGE, InputArgument::REQUIRED, 'Path to coverage report file .xml');
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

        $coverageFilePath = $this->getCoverageFilePath($input);
        $project = $this->coverageParser->parseCoverage($coverageFilePath);
        $this->renderCoverageSummary($coverageFilePath, $project);

        $reports = $this->initReports($project, $config);
        $status = $this->renderReports($reports);

        $this->covelyzerStyle->status($status);
        return $status;
    }

    /**
     * @param InputInterface $input
     * @return string
     */
    private function getCoverageFilePath(InputInterface $input): string
    {
        /** @var string $filePath */
        $filePath = $input->getArgument(self::ARG_COVERAGE);
        $realPath = realpath($filePath);

        if ($realPath === false) {
            throw new \RuntimeException(sprintf('Unable to resolve coverage path "%s"', $filePath));
        }

        return $realPath;
    }

    /**
     * @param string $coverageFilePath
     * @param Project $project
     */
    private function renderCoverageSummary(string $coverageFilePath, Project $project): void
    {
        $this->covelyzerStyle->writeln('  Report:    ' . $coverageFilePath);

        $datetime = $project->getTimestamp();
        $displayDateTime = $datetime ? $datetime->format('Y-m-d H:i:s') : '--';
        $this->covelyzerStyle->writeln('  Timestamp: ' . $displayDateTime);
    }

    /**
     * @param Project $project
     * @param Config $config
     * @return array&ReportInterface[]
     */
    private function initReports(Project $project, Config $config): array
    {
        $reports = [];
        $tableRenderer = new ClassCoverageTableRenderer();

        $minProjectCoverage = $config->getMinProjectCoverage();
        if ($minProjectCoverage !== null) {
            $reports[] = new ProjectCoverageReport($project, $minProjectCoverage, $tableRenderer);
        }

        $minClassCoverage = $config->getMinClassCoverage();
        if ($minClassCoverage !== null) {
            $reports[] = new ClassCoverageReport($project, $minClassCoverage, $tableRenderer);
        }

        return $reports;
    }

    /**
     * @param array&ReportInterface[] $reports
     * @return int
     */
    private function renderReports(array $reports): int
    {
        $status = self::SUCCESS;

        foreach ($reports as $report) {
            $this->covelyzerStyle->newLine();
            $report->render($this->covelyzerStyle);
            $status = $report->isSuccess() === false ? self::FAILURE : $status;
        }

        return $status;
    }
}
