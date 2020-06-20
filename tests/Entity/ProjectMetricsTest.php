<?php

namespace VStelmakh\Covelyzer\Tests\Entity;

use VStelmakh\Covelyzer\Entity\ProjectMetrics;
use PHPUnit\Framework\TestCase;
use VStelmakh\Covelyzer\Xml\XpathElement;

class ProjectMetricsTest extends TestCase
{
    /**
     * @var ProjectMetrics
     */
    private $projectMetrics;

    public function setUp(): void
    {
        $domDocument = new \DOMDocument();

        $parent = $domDocument->createElement('project');
        $domDocument->appendChild($parent);

        $metrics = $domDocument->createElement('metrics');
        $metrics->setAttribute('files', '4');
        $metrics->setAttribute('loc', '3');
        $metrics->setAttribute('ncloc', '2');
        $metrics->setAttribute('classes', '1');
        $parent->appendChild($metrics);

        $domXpath = new \DOMXPath($domDocument);
        $xpathElement = new XpathElement($domXpath, $metrics);

        $this->projectMetrics = new ProjectMetrics($xpathElement);
    }

    public function testGetFiles(): void
    {
        $actual = $this->projectMetrics->getFiles();
        $this->assertSame(4, $actual);
    }

    public function testGetLoc(): void
    {
        $actual = $this->projectMetrics->getLoc();
        $this->assertSame(3, $actual);
    }

    public function testGetNcloc(): void
    {
        $actual = $this->projectMetrics->getNcloc();
        $this->assertSame(2, $actual);
    }

    public function testGetClasses(): void
    {
        $actual = $this->projectMetrics->getClasses();
        $this->assertSame(1, $actual);
    }
}
