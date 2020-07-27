<?php

declare(strict_types=1);

namespace VStelmakh\Covelyzer\Report;

use VStelmakh\Covelyzer\Console\CovelyzerStyle;
use VStelmakh\Covelyzer\Entity\Project;
use VStelmakh\Covelyzer\Filter\ClassFilter;

class ProjectCoverageReport implements ReportInterface
{
    private const CLASS_COUNT = 10;

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

        $classes = $this->project->getClasses();
        $filter = new ClassFilter($classes);
        $filter->setLimit(self::CLASS_COUNT);
        $lessCoveredClasses = $filter->getResult();

        $headers = ['Class', 'Coverage'];
        $rows = [];
        foreach ($lessCoveredClasses as $class) {
            $rows[] = [$class->getName(), $class->getMetrics()->getCoverage()];
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
}
