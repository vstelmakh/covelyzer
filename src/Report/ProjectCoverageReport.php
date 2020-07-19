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
     * @var float
     */
    private $minCoverage;

    /**
     * @param Project $project
     * @param float $minCoverage
     */
    public function __construct(Project $project, float $minCoverage)
    {
        $this->project = $project;
        $this->minCoverage = $minCoverage;
    }

    /**
     * @inheritDoc
     */
    public function render(CovelyzerStyle $covelyzerStyle): void
    {
        $covelyzerStyle->title('Project coverage', $this->isSuccess());
        $metrics = $this->project->getMetrics();
        $covelyzerStyle->coverage($metrics->getCoverage(), $this->minCoverage);

        $classesCoverage = $this->getClassesCoverage();
        asort($classesCoverage);

        $headers = ['Class', 'Coverage'];
        $rows = [];
        foreach ($classesCoverage as $class => $coverage) {
            $rows[] = [$class, $coverage];
        }
        $covelyzerStyle->newLine();
        $covelyzerStyle->table($headers, $rows);
    }

    /**
     * @inheritDoc
     */
    public function isSuccess(): bool
    {
        $metrics = $this->project->getMetrics();
        $coverage = $metrics->getCoverage();
        return $coverage >= $this->minCoverage;
    }

    /**
     * @return array&float[] key - class name, value - coverage percentage
     */
    private function getClassesCoverage(): array
    {
        $result = [];

        $classes = $this->project->getClasses();
        foreach ($classes as $class) {
            $className = $class->getName();
            $metrics = $class->getMetrics();

            $coverage = $metrics->getCoverage();
            if ($coverage !== null) {
                $result[$className] = $coverage;
            }
        }

        return $result;
    }
}
