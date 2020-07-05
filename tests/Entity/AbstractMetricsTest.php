<?php

namespace VStelmakh\Covelyzer\Tests\Entity;

use VStelmakh\Covelyzer\Entity\AbstractMetrics;
use PHPUnit\Framework\TestCase;
use VStelmakh\Covelyzer\Dom\XpathElement;

class AbstractMetricsTest extends TestCase
{
    /**
     * @var AbstractMetrics
     */
    private $abstractMetrics;

    public function setUp(): void
    {
        $domDocument = new \DOMDocument();

        $parent = $domDocument->createElement('parent');
        $domDocument->appendChild($parent);

        $metrics = $domDocument->createElement('metrics');
        $metrics->setAttribute('methods', '1');
        $metrics->setAttribute('coveredmethods', '2');
        $metrics->setAttribute('conditionals', '3');
        $metrics->setAttribute('coveredconditionals', '4');
        $metrics->setAttribute('statements', '5');
        $metrics->setAttribute('coveredstatements', '6');
        $metrics->setAttribute('elements', '7');
        $metrics->setAttribute('coveredelements', '8');
        $parent->appendChild($metrics);

        $domXpath = new \DOMXPath($domDocument);
        $xpathElement = new XpathElement($domXpath, $metrics);

        $this->abstractMetrics = $this->createAbstractMetrics($xpathElement);
    }

    public function testGetMethods(): void
    {
        $actual = $this->abstractMetrics->getMethods();
        $this->assertSame(1, $actual);
    }

    public function testGetCoveredMethods(): void
    {
        $actual = $this->abstractMetrics->getCoveredMethods();
        $this->assertSame(2, $actual);
    }

    public function testGetConditionals(): void
    {
        $actual = $this->abstractMetrics->getConditionals();
        $this->assertSame(3, $actual);
    }

    public function testGetCoveredConditionals(): void
    {
        $actual = $this->abstractMetrics->getCoveredConditionals();
        $this->assertSame(4, $actual);
    }

    public function testGetStatements(): void
    {
        $actual = $this->abstractMetrics->getStatements();
        $this->assertSame(5, $actual);
    }

    public function testGetCoveredStatements(): void
    {
        $actual = $this->abstractMetrics->getCoveredStatements();
        $this->assertSame(6, $actual);
    }

    public function testGetElements(): void
    {
        $actual = $this->abstractMetrics->getElements();
        $this->assertSame(7, $actual);
    }

    public function testGetCoveredElements(): void
    {
        $actual = $this->abstractMetrics->getCoveredElements();
        $this->assertSame(8, $actual);
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
