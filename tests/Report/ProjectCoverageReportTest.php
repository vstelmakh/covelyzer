<?php

declare(strict_types=1);

namespace VStelmakh\Covelyzer\Tests\Report;

use VStelmakh\Covelyzer\Console\ClassCoverageTableRenderer;
use VStelmakh\Covelyzer\Console\CovelyzerStyle;
use VStelmakh\Covelyzer\Dom\XpathElement;
use VStelmakh\Covelyzer\Entity\ClassEntity;
use VStelmakh\Covelyzer\Entity\Project;
use VStelmakh\Covelyzer\Entity\ProjectMetrics;
use VStelmakh\Covelyzer\Report\ProjectCoverageReport;
use PHPUnit\Framework\TestCase;

class ProjectCoverageReportTest extends TestCase
{
    /**
     * @dataProvider renderDataProvider
     *
     * @param float $coverage
     * @param float $minCoverage
     * @param bool $isSuccess
     */
    public function testRender(float $coverage, float $minCoverage, bool $isSuccess): void
    {
        $metrics = $this->createMock(ProjectMetrics::class);
        $metrics->method('getCoverage')->willReturn($coverage);

        $classes = [
            $this->createClass('Class1', '', 100),
            $this->createClass('Class2', '', 100),
        ];

        $project = $this->createMock(Project::class);
        $project->method('getMetrics')->willReturn($metrics);
        $project->method('getClasses')->willReturn($this->getGenerator($classes));

        $tableRenderer = $this->createMock(ClassCoverageTableRenderer::class);
        $report = new ProjectCoverageReport($project, $minCoverage, $tableRenderer);

        $covelyzerStyle = $this->createMock(CovelyzerStyle::class);

        $covelyzerStyle
            ->expects(self::once())
            ->method('title')
            ->with(self::identicalTo('Project coverage'), self::identicalTo($isSuccess));

        $covelyzerStyle
            ->expects(self::once())
            ->method('coverage')
            ->with(self::identicalTo($coverage), self::identicalTo($minCoverage));

        $covelyzerStyle
            ->expects(self::once())
            ->method('newLine');

        $tableRenderer
            ->expects(self::once())
            ->method('render')
            ->with(self::identicalTo($covelyzerStyle), self::identicalTo($classes));

        $report->render($covelyzerStyle);
    }

    /**
     * @return array&array[]
     */
    public function renderDataProvider(): array
    {
        return [
            [100, 100, true],
            [100, 99, true],
            [99, 100, false],
            [0, 0, true],
            [0, 1, false],
        ];
    }

    /**
     * @dataProvider renderDataProvider
     *
     * @param float $coverage
     * @param float $minCoverage
     * @param bool $expected
     */
    public function testIsSuccess(float $coverage, float $minCoverage, bool $expected): void
    {
        $metrics = $this->createMock(ProjectMetrics::class);
        $metrics->method('getCoverage')->willReturn($coverage);

        $project = $this->createMock(Project::class);
        $project->method('getMetrics')->willReturn($metrics);

        $tableRenderer = $this->createMock(ClassCoverageTableRenderer::class);
        $report = new ProjectCoverageReport($project, $minCoverage, $tableRenderer);

        $actual = $report->isSuccess();
        self::assertSame($expected, $actual);
    }

    /**
     * @param string $name
     * @param string $namespace
     * @param int $coverage
     * @return ClassEntity
     */
    private function createClass(string $name, string $namespace, int $coverage): ClassEntity
    {
        $domDocument = new \DOMDocument();

        $class = $domDocument->createElement('class');
        $class->setAttribute('name', $name);
        $class->setAttribute('namespace', $namespace);
        $domDocument->appendChild($class);

        $metrics = $domDocument->createElement('metrics');
        $metrics->setAttribute('elements', '100');
        $metrics->setAttribute('coveredelements', (string) $coverage);
        $class->appendChild($metrics);

        $domXpath = new \DOMXPath($domDocument);
        $xpathElement = new XpathElement($domXpath, $class);

        return new ClassEntity($xpathElement);
    }

    /**
     * @param array|mixed[] $array
     * @return \Generator|mixed[]
     */
    private function getGenerator(array $array): \Generator
    {
        foreach ($array as $item) {
            yield $item;
        }
    }
}
