<?php

declare(strict_types=1);

namespace VStelmakh\Covelyzer\Tests\Entity;

use VStelmakh\Covelyzer\Entity\AbstractMetrics;
use PHPUnit\Framework\TestCase;
use VStelmakh\Covelyzer\Dom\XpathElement;

class AbstractMetricsTest extends TestCase
{
    public function testGetMethods(): void
    {
        $xpathElement = $this->createXpathElement(1, 2, 3, 4, 5, 6, 7, 8);
        $abstractMetrics = $this->createAbstractMetrics($xpathElement);
        $actual = $abstractMetrics->getMethods();
        self::assertSame(1, $actual);
    }

    public function testGetCoveredMethods(): void
    {
        $xpathElement = $this->createXpathElement(1, 2, 3, 4, 5, 6, 7, 8);
        $abstractMetrics = $this->createAbstractMetrics($xpathElement);
        $actual = $abstractMetrics->getCoveredMethods();
        self::assertSame(2, $actual);
    }

    public function testGetConditionals(): void
    {
        $xpathElement = $this->createXpathElement(1, 2, 3, 4, 5, 6, 7, 8);
        $abstractMetrics = $this->createAbstractMetrics($xpathElement);
        $actual = $abstractMetrics->getConditionals();
        self::assertSame(3, $actual);
    }

    public function testGetCoveredConditionals(): void
    {
        $xpathElement = $this->createXpathElement(1, 2, 3, 4, 5, 6, 7, 8);
        $abstractMetrics = $this->createAbstractMetrics($xpathElement);
        $actual = $abstractMetrics->getCoveredConditionals();
        self::assertSame(4, $actual);
    }

    public function testGetStatements(): void
    {
        $xpathElement = $this->createXpathElement(1, 2, 3, 4, 5, 6, 7, 8);
        $abstractMetrics = $this->createAbstractMetrics($xpathElement);
        $actual = $abstractMetrics->getStatements();
        self::assertSame(5, $actual);
    }

    public function testGetCoveredStatements(): void
    {
        $xpathElement = $this->createXpathElement(1, 2, 3, 4, 5, 6, 7, 8);
        $abstractMetrics = $this->createAbstractMetrics($xpathElement);
        $actual = $abstractMetrics->getCoveredStatements();
        self::assertSame(6, $actual);
    }

    public function testGetElements(): void
    {
        $xpathElement = $this->createXpathElement(1, 2, 3, 4, 5, 6, 7, 8);
        $abstractMetrics = $this->createAbstractMetrics($xpathElement);
        $actual = $abstractMetrics->getElements();
        self::assertSame(7, $actual);
    }

    public function testGetCoveredElements(): void
    {
        $xpathElement = $this->createXpathElement(1, 2, 3, 4, 5, 6, 7, 8);
        $abstractMetrics = $this->createAbstractMetrics($xpathElement);
        $actual = $abstractMetrics->getCoveredElements();
        self::assertSame(8, $actual);
    }

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
        $xpathElement = $this->createXpathElement(0, 0, 0, 0, 0, 0, $elements, $coveredElements);
        $abstractMetrics = $this->createAbstractMetrics($xpathElement);
        $actual = $abstractMetrics->getCoverage($precision);
        self::assertSame($expected, $actual);
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

    public function testValidateElement(): void
    {
        $domDocument = new \DOMDocument();

        $parent = $domDocument->createElement('unexpected_parent');
        $domDocument->appendChild($parent);

        $metrics = $domDocument->createElement('metrics');
        $parent->appendChild($metrics);

        $domXpath = new \DOMXPath($domDocument);
        $xpathElement = new XpathElement($domXpath, $metrics);

        $this->expectException(\InvalidArgumentException::class);
        $this->createAbstractMetrics($xpathElement);
    }

    /**
     * @param int $methods
     * @param int $coveredmethods
     * @param int $conditionals
     * @param int $coveredconditionals
     * @param int $statements
     * @param int $coveredstatements
     * @param int $elements
     * @param int $coveredelements
     * @return XpathElement
     */
    private function createXpathElement(
        int $methods,
        int $coveredmethods,
        int $conditionals,
        int $coveredconditionals,
        int $statements,
        int $coveredstatements,
        int $elements,
        int $coveredelements
    ): XpathElement {
        $domDocument = new \DOMDocument();

        $parent = $domDocument->createElement('parent');
        $domDocument->appendChild($parent);

        $metrics = $domDocument->createElement('metrics');
        $metrics->setAttribute('methods', (string) $methods);
        $metrics->setAttribute('coveredmethods', (string) $coveredmethods);
        $metrics->setAttribute('conditionals', (string) $conditionals);
        $metrics->setAttribute('coveredconditionals', (string) $coveredconditionals);
        $metrics->setAttribute('statements', (string) $statements);
        $metrics->setAttribute('coveredstatements', (string) $coveredstatements);
        $metrics->setAttribute('elements', (string) $elements);
        $metrics->setAttribute('coveredelements', (string) $coveredelements);
        $parent->appendChild($metrics);

        $domXpath = new \DOMXPath($domDocument);
        return new XpathElement($domXpath, $metrics);
    }

    /**
     * @param XpathElement $xpathElement
     * @return AbstractMetrics
     */
    private function createAbstractMetrics(XpathElement $xpathElement): AbstractMetrics
    {
        return new class ($xpathElement) extends AbstractMetrics {
            protected function getExpectedParentTagName(): string
            {
                return 'parent';
            }
        };
    }
}
