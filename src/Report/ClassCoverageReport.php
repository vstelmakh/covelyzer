<?php

declare(strict_types=1);

namespace VStelmakh\Covelyzer\Report;

use VStelmakh\Covelyzer\Console\ClassCoverageTableRenderer;
use VStelmakh\Covelyzer\Console\CovelyzerStyle;
use VStelmakh\Covelyzer\Entity\ClassEntity;
use VStelmakh\Covelyzer\Entity\Project;
use VStelmakh\Covelyzer\Filter\ClassFilter;

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
     * @var array&ClassEntity[]
     */
    private $smallCoverageClasses;

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
        $this->smallCoverageClasses = $this->getClassesCoverageLessThan($minCoverage);
        $this->tableRenderer = $classCoverageTableRenderer;
    }

    /**
     * @inheritDoc
     */
    public function render(CovelyzerStyle $covelyzerStyle): void
    {
        $isSuccess = $this->isSuccess();
        $covelyzerStyle->title('Class coverage', $isSuccess);
        $covelyzerStyle->coverage(null, $this->minCoverage);

        if (!$isSuccess) {
            $covelyzerStyle->newLine();
            $this->tableRenderer->render($covelyzerStyle, $this->smallCoverageClasses);
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
     * @return array&ClassEntity[]
     */
    private function getClassesCoverageLessThan(float $minCoverage): array
    {
        $classes = $this->project->getClasses();
        $filter = new ClassFilter($classes);
        $filter->setCoverageBelow($minCoverage);
        return $filter->getResult();
    }
}
