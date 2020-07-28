<?php

declare(strict_types=1);

namespace VStelmakh\Covelyzer\Report;

use VStelmakh\Covelyzer\Console\ClassCoverageTableRenderer;
use VStelmakh\Covelyzer\Console\CovelyzerStyle;
use VStelmakh\Covelyzer\Entity\ClassEntity;
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
     * @var ClassCoverageTableRenderer
     */
    private $tableRenderer;

    /**
     * @param Project $project
     * @param float $minCoverage
     * @param ClassCoverageTableRenderer $classCoverageTableRenderer
     */
    public function __construct(
        Project $project,
        float $minCoverage,
        ClassCoverageTableRenderer $classCoverageTableRenderer
    ) {
        $this->project = $project;
        $this->minCoverage = $minCoverage;
        $this->tableRenderer = $classCoverageTableRenderer;
    }

    /**
     * @inheritDoc
     */
    public function render(CovelyzerStyle $covelyzerStyle): void
    {
        $covelyzerStyle->title('Project coverage', $this->isSuccess());
        $metrics = $this->project->getMetrics();
        $covelyzerStyle->coverage($metrics->getCoverage(), $this->minCoverage);

        $leastCoveredClasses = $this->getLeastCoveredClasses();

        $covelyzerStyle->newLine();
        $this->tableRenderer->render($covelyzerStyle, $leastCoveredClasses);
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
     * @return array&ClassEntity[]
     */
    private function getLeastCoveredClasses(): array
    {
        $classes = $this->project->getClasses();
        $filter = new ClassFilter($classes);
        $filter->setLimit(self::CLASS_COUNT);
        return $filter->getResult();
    }
}
