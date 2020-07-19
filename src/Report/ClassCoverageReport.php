<?php

declare(strict_types=1);

namespace VStelmakh\Covelyzer\Report;

use VStelmakh\Covelyzer\Console\CovelyzerStyle;
use VStelmakh\Covelyzer\Entity\Project;

class ClassCoverageReport implements ReportInterface
{
    /**
     * @var Project
     */
    private $project;

    /**
     * @var float
     */
    private $minCoverage;

    /**
     * @var array&float[]
     */
    private $smallCoverageClasses;

    /**
     * @param Project $project
     * @param float $minCoverage
     */
    public function __construct(Project $project, float $minCoverage)
    {
        $this->project = $project;
        $this->minCoverage = $minCoverage;
        $this->smallCoverageClasses = $this->getClassesCoverageLessThan($minCoverage);
    }

    /**
     * @inheritDoc
     */
    public function render(CovelyzerStyle $covelyzerStyle): void
    {
        $isSuccess = empty($this->smallCoverageClasses);
        $covelyzerStyle->title('Class coverage', $isSuccess);
        $covelyzerStyle->coverage(null, $this->minCoverage);

        if (!$isSuccess) {
            $filesCoverage = $this->smallCoverageClasses;
            asort($filesCoverage);

            $headers = ['Class', 'Coverage'];
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
        return empty($this->smallCoverageClasses);
    }

    /**
     * @param float $minCoverage
     * @return array&float[] key - class name, value - coverage percentage
     */
    private function getClassesCoverageLessThan(float $minCoverage): array
    {
        $result = [];

        $classes = $this->project->getClasses();
        foreach ($classes as $class) {
            $className = $class->getName();
            $metrics = $class->getMetrics();

            $coverage = $metrics->getCoverage();
            if ($coverage !== null && $coverage < $minCoverage) {
                $result[$className] = $coverage;
            }
        }

        return $result;
    }
}
