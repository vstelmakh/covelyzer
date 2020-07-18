<?php

declare(strict_types=1);

namespace VStelmakh\Covelyzer\Tests\Entity;

use VStelmakh\Covelyzer\Dom\XpathElement;
use VStelmakh\Covelyzer\Entity\ClassMetrics;
use PHPUnit\Framework\TestCase;

class ClassMetricsTest extends TestCase
{
    /**
     * @var ClassMetrics
     */
    private $classMetrics;

    public function setUp(): void
    {
        $domDocument = new \DOMDocument();

        $parent = $domDocument->createElement('class');
        $domDocument->appendChild($parent);

        $metrics = $domDocument->createElement('metrics');
        $metrics->setAttribute('complexity', '1');
        $parent->appendChild($metrics);

        $domXpath = new \DOMXPath($domDocument);
        $xpathElement = new XpathElement($domXpath, $metrics);

        $this->classMetrics = new ClassMetrics($xpathElement);
    }

    public function testGetComplexity(): void
    {
        $actual = $this->classMetrics->getComplexity();
        $this->assertSame(1, $actual);
    }
}
