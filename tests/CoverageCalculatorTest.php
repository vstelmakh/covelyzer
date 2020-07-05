<?php

declare(strict_types=1);

namespace VStelmakh\Covelyzer\Tests;

use PHPUnit\Framework\MockObject\MockObject;
use VStelmakh\Covelyzer\Entity\AbstractMetrics;
use VStelmakh\Covelyzer\CoverageCalculator;
use PHPUnit\Framework\TestCase;

class CoverageCalculatorTest extends TestCase
{
    /**
     * @dataProvider getCoverageDataProvider
     *
     * @param int $elements
     * @param int $coveredElements
     * @param int $precision
     * @param float|null $expected
     */
    public function testGetCoverage(int $elements, int $coveredElements, int $precision, ?float $expected): void
    {
        /** @var AbstractMetrics&MockObject $metrics */
        $metrics = $this->createMock(AbstractMetrics::class);
        $metrics->method('getElements')->willReturn($elements);
        $metrics->method('getCoveredElements')->willReturn($coveredElements);

        $coverageCalculator = new CoverageCalculator($precision);
        $actual = $coverageCalculator->getCoverage($metrics);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array&array[]
     */
    public function getCoverageDataProvider(): array
    {
        return [
            [0, 0, 0, null],
            [0, 1, 0, null],
            [1, 0, 0, 0],
            [2, 1, 0, 50],
            [2, 1, 2, 50],
            [3, 1, 0, 33],
            [3, 1, 1, 33.3],
        ];
    }
}
