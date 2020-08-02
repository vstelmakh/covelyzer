<?php

declare(strict_types=1);

namespace VStelmakh\Covelyzer\Tests\Console;

use PHPUnit\Framework\MockObject\MockObject;
use VStelmakh\Covelyzer\Console\ClassCoverageTableRenderer;
use PHPUnit\Framework\TestCase;
use VStelmakh\Covelyzer\Console\CovelyzerStyle;
use VStelmakh\Covelyzer\Entity\ClassEntity;
use VStelmakh\Covelyzer\Entity\ClassMetrics;

class ClassCoverageTableRendererTest extends TestCase
{
    public function testRender(): void
    {
        $classes = [
            $this->createClassMock('Class1', 1.0),
            $this->createClassMock('Class2', 2.0)
        ];

        $expectedRows = [
            ['Class1', 1.0],
            ['Class2', 2.0],
        ];

        $covelyzerStyle = $this->createMock(CovelyzerStyle::class);
        $covelyzerStyle
            ->expects(self::once())
            ->method('table')
            ->with(self::identicalTo(['Class', 'Coverage']), self::identicalTo($expectedRows));

        $tableRenderer = new ClassCoverageTableRenderer();
        $tableRenderer->render($covelyzerStyle, $classes);
    }

    /**
     * @param string $name
     * @param float $coverage
     * @return MockObject&ClassEntity
     */
    private function createClassMock(string $name, float $coverage): MockObject
    {
        $metrics = $this->createMock(ClassMetrics::class);
        $metrics->method('getCoverage')->willReturn($coverage);

        $class = $this->createMock(ClassEntity::class);
        $class->method('getName')->willReturn($name);
        $class->method('getMetrics')->willReturn($metrics);

        return $class;
    }
}
