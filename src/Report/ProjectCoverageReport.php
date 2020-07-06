<?php

declare(strict_types=1);

namespace VStelmakh\Covelyzer\Report;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;
use VStelmakh\Covelyzer\Entity\Project;
use VStelmakh\Covelyzer\CoverageCalculator;

class ProjectCoverageReport implements ReportInterface
{
    /**
     * @var Project
     */
    private $project;

    /**
     * @var CoverageCalculator
     */
    private $coverageCalculator;

    /**
     * @var float|null
     */
    private $coverage;

    /**
     * @var bool
     */
    private $isSuccess;

    public function __construct(Project $project, CoverageCalculator $coverageCalculator, float $minCoverage)
    {
        $this->project = $project;
        $this->coverageCalculator = $coverageCalculator;
        $this->coverage = $coverageCalculator->getCoverage($project->getMetrics());
        $this->isSuccess = $this->coverage >= $minCoverage;
    }

    /**
     * @inheritDoc
     */
    public function render(OutputInterface $output): void
    {
        $coverage = $this->coverage !== null ? $this->coverage . '%' : '--';
        $output->writeln('Project coverage: ' . $coverage);

        $filesCoverage = $this->getFilesCoverage($this->project);
        asort($filesCoverage);

        $table = new Table($output);
        $table->setHeaders(['File', 'Coverage']);
        foreach ($filesCoverage as $file => $coverage) {
            $table->addRow([$file, $coverage]);
        }
        $table->render();
    }

    /**
     * @inheritDoc
     */
    public function isSuccess(): bool
    {
        return $this->isSuccess;
    }

    /**
     * @param Project $project
     * @return array&float[] key - filename, value - coverage percentage
     */
    private function getFilesCoverage(Project $project): array
    {
        $result = [];

        $files = $project->getFiles();
        foreach ($files as $file) {
            $fileName = $file->getName();
            $metrics = $file->getMetrics();

            $coverage = $this->coverageCalculator->getCoverage($metrics);
            if ($coverage !== null) {
                $result[$fileName] = $coverage;
            }
        }

        return $result;
    }
}
