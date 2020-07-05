<?php

namespace VStelmakh\Covelyzer\Report;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;
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
     * @var bool
     */
    private $isSuccess;

    public function __construct(Project $project, float $minCoverage = 0)
    {
        $this->project = $project;
        // TODO: handle null case
        $this->coverage = $this->getCoverage($project);
        $this->isSuccess = $this->coverage >= $minCoverage;
    }

    /**
     * @inheritDoc
     */
    public function render(OutputInterface $output): void
    {
        $output->writeln(sprintf('Project coverage: %s%%', $this->coverage));

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
     * Return coverage percentage or null if file don't have any elements (e.g. interface)
     *
     * @param Project $project
     * @return float|null
     */
    private function getCoverage(Project $project): ?float
    {
        $metrics = $project->getMetrics();
        $elements = $metrics->getElements();
        $coveredElements = $metrics->getCoveredElements();
        return $elements > 0
            ? (float) ($coveredElements / $elements) * 100
            : null;
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

            $elements = $metrics->getElements();
            if ($elements > 0) {
                $coveredElements = $metrics->getCoveredElements();
                $coveragePercentage = (float) ($coveredElements / $elements) * 100;
                $result[$fileName] = $coveragePercentage;
            }
        }

        return $result;
    }
}
