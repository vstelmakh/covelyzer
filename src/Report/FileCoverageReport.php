<?php

declare(strict_types=1);

namespace VStelmakh\Covelyzer\Report;

use VStelmakh\Covelyzer\Console\CovelyzerStyle;
use VStelmakh\Covelyzer\Entity\Project;

class FileCoverageReport implements ReportInterface
{
    /**
     * @var float
     */
    private $minCoverage;

    /**
     * @var array&float[]
     */
    private $smallCoverageFiles;

    /**
     * @param Project $project
     * @param float $minCoverage
     */
    public function __construct(Project $project, float $minCoverage)
    {
        $this->minCoverage = $minCoverage;
        $this->smallCoverageFiles = $this->getFilesCoverageLessThan($project, $minCoverage);
    }

    /**
     * @inheritDoc
     */
    public function render(CovelyzerStyle $covelyzerStyle): void
    {
        $isSuccess = empty($this->smallCoverageFiles);
        $covelyzerStyle->title('File coverage', $isSuccess);
        $covelyzerStyle->coverage(null, $this->minCoverage);

        if (!$isSuccess) {
            $filesCoverage = $this->smallCoverageFiles;
            asort($filesCoverage);

            $headers = ['File', 'Coverage'];
            $rows = [];
            foreach ($filesCoverage as $file => $coverage) {
                $rows[] = [$file, $coverage];
            }
            $covelyzerStyle->newLine();
            $covelyzerStyle->table($headers, $rows);
        }
    }

    /**
     * @inheritDoc
     */
    public function isSuccess(): bool
    {
        return empty($this->smallCoverageFiles);
    }

    /**
     * @param Project $project
     * @param float $minCoverage
     * @return array&float[] key - filename, value - coverage percentage
     */
    private function getFilesCoverageLessThan(Project $project, float $minCoverage): array
    {
        $result = [];

        $files = $project->getFiles();
        foreach ($files as $file) {
            $fileName = $file->getName();
            $metrics = $file->getMetrics();

            $coverage = $metrics->getCoverage();
            if ($coverage !== null && $coverage < $minCoverage) {
                $result[$fileName] = $coverage;
            }
        }

        return $result;
    }
}
