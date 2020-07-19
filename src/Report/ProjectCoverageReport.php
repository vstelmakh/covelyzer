<?php

declare(strict_types=1);

namespace VStelmakh\Covelyzer\Report;

use VStelmakh\Covelyzer\Console\CovelyzerStyle;
use VStelmakh\Covelyzer\Entity\Project;

class ProjectCoverageReport implements ReportInterface
{
    /**
     * @var Project
     */
    private $project;

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

    /**
     * @param Project $project
     * @param float $minCoverage
     */
    public function __construct(Project $project, float $minCoverage)
    {
        $this->project = $project;
        $this->minCoverage = $minCoverage;
        $this->isSuccess = $this->coverage >= $this->minCoverage;
    }

    /**
     * @inheritDoc
     */
    public function render(CovelyzerStyle $covelyzerStyle): void
    {
        $covelyzerStyle->title('Project coverage', $this->isSuccess);
        $metrics = $this->project->getMetrics();
        $covelyzerStyle->coverage($metrics->getCoverage(), $this->minCoverage);

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

            $coverage = $metrics->getCoverage();
            if ($coverage !== null) {
                $result[$fileName] = $coverage;
            }
        }

        return $result;
    }
}
