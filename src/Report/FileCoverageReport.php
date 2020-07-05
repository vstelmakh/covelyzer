<?php

namespace VStelmakh\Covelyzer\Report;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;
use VStelmakh\Covelyzer\Entity\Project;

class FileCoverageReport implements ReportInterface
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
    private $smallCoverageFiles;

    /**
     * @param Project $project
     * @param float $minCoverage
     */
    public function __construct(Project $project, float $minCoverage)
    {
        $this->project = $project;
        $this->minCoverage = $minCoverage;
        $this->smallCoverageFiles = $this->getFilesCoverageLessThan($project, $minCoverage);
    }

    /**
     * @inheritDoc
     */
    public function render(OutputInterface $output): void
    {
        $output->writeln('Min coverage per file: ' . $this->minCoverage);
        if (empty($this->smallCoverageFiles)) {
            $output->writeln('OK');
        } else {
            $output->writeln('Files with coverage below minimum:');

            $filesCoverage = $this->smallCoverageFiles;
            asort($filesCoverage);

            $table = new Table($output);
            $table->setHeaders(['File', 'Coverage']);
            foreach ($filesCoverage as $file => $coverage) {
                $table->addRow([$file, $coverage]);
            }
            $table->render();
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

            $elements = $metrics->getElements();
            if ($elements > 0) {
                $coveredElements = $metrics->getCoveredElements();
                $coveragePercentage = (float) ($coveredElements / $elements) * 100;
                if ($coveragePercentage < $minCoverage) {
                    $result[$fileName] = $coveragePercentage;
                }
            }
        }

        return $result;
    }
}
