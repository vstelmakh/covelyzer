<?php

declare(strict_types=1);

namespace VStelmakh\Covelyzer\Report;

use VStelmakh\Covelyzer\CovelyzerStyle;
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
     * @var float
     */
    private $minCoverage;

    /**
     * @var bool
     */
    private $isSuccess;

    public function __construct(Project $project, CoverageCalculator $coverageCalculator, float $minCoverage)
    {
        $this->project = $project;
        $this->coverageCalculator = $coverageCalculator;
        $this->coverage = $coverageCalculator->getCoverage($project->getMetrics());
        $this->minCoverage = $minCoverage;
        $this->isSuccess = $this->coverage >= $this->minCoverage;
    }

    /**
     * @inheritDoc
     */
    public function render(CovelyzerStyle $covelyzerStyle): void
    {
        $covelyzerStyle->title('Project coverage', $this->isSuccess);
        $covelyzerStyle->coverage($this->coverage, $this->minCoverage);

        $filesCoverage = $this->getFilesCoverage($this->project);
        asort($filesCoverage);

        $headers = ['File', 'Coverage'];
        $rows = [];
        foreach ($filesCoverage as $file => $coverage) {
            $rows[] = [$file, $coverage];
        }
        $covelyzerStyle->newLine();
        $covelyzerStyle->table($headers, $rows);
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
