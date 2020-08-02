<?php

declare(strict_types=1);

namespace VStelmakh\Covelyzer\Tests\Report;

use VStelmakh\Covelyzer\Console\ClassCoverageTableRenderer;
use VStelmakh\Covelyzer\Console\CovelyzerStyle;
use VStelmakh\Covelyzer\Dom\XpathElement;
use VStelmakh\Covelyzer\Entity\ClassEntity;
use VStelmakh\Covelyzer\Entity\Project;
use VStelmakh\Covelyzer\Report\ClassCoverageReport;
use PHPUnit\Framework\TestCase;

class ClassCoverageReportTest extends TestCase
{
    /**
     * @dataProvider renderDataProvider
     *
     * @param float $minCoverage
     * @param int $count
     * @param bool $isSuccess
     */
    public function testRender(float $minCoverage, int $count, bool $isSuccess): void
    {
        $classes = [
            $this->createClass('Class1', '', 50),
            $this->createClass('Class2', '', 90),
        ];

        $project = $this->createMock(Project::class);
        $project->method('getClasses')->willReturn($this->getGenerator($classes));

        $tableRenderer = $this->createMock(ClassCoverageTableRenderer::class);
        $report = new ClassCoverageReport($project, $minCoverage, $tableRenderer);

        $covelyzerStyle = $this->createMock(CovelyzerStyle::class);

        $covelyzerStyle
            ->expects(self::once())
            ->method('title')
            ->with(self::identicalTo('Class coverage'), self::identicalTo($isSuccess));

        $covelyzerStyle
            ->expects(self::once())
            ->method('coverage')
            ->with(self::identicalTo(null), self::identicalTo($minCoverage));

        if (!$isSuccess) {
            $covelyzerStyle
                ->expects(self::once())
                ->method('newLine');

            $tableRenderer
                ->expects(self::once())
                ->method('render')
                ->with(self::identicalTo($covelyzerStyle), self::countOf($count));
        }

        $report->render($covelyzerStyle);
    }

    /**
     * @return array&array[]
     */
    public function renderDataProvider(): array
    {
        return [
            [100, 2, false],
            [91, 2, false],
            [90, 1, false],
            [50, 0, true],
            [0, 0, true],
        ];
    }

    /**
     * @dataProvider isSuccessDataProvider
     *
     * @param float $minCoverage
     * @param bool $expected
     */
    public function testIsSuccess(float $minCoverage, bool $expected): void
    {
        $classes = [
            $this->createClass('Class1', '', 50),
            $this->createClass('Class2', '', 90),
        ];

        $project = $this->createMock(Project::class);
        $project->method('getClasses')->willReturn($this->getGenerator($classes));

        $tableRenderer = $this->createMock(ClassCoverageTableRenderer::class);
        $report = new ClassCoverageReport($project, $minCoverage, $tableRenderer);

        $actual = $report->isSuccess();
        self::assertSame($expected, $actual);
    }

    /**
     * @return array&array[]
     */
    public function isSuccessDataProvider(): array
    {
        return [
            [100, false],
            [91, false],
            [90, false],
            [50, true],
            [0, true],
        ];
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
