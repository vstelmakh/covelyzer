<?php

namespace VStelmakh\Covelyzer\Tests\Entity;

use VStelmakh\Covelyzer\Entity\FileMetrics;
use PHPUnit\Framework\TestCase;
use VStelmakh\Covelyzer\Xml\XpathElement;

class FileMetricsTest extends TestCase
{
    /**
     * @var FileMetrics
     */
    private $fileMetrics;

    public function setUp(): void
    {
        $domDocument = new \DOMDocument();

        $parent = $domDocument->createElement('file');
        $domDocument->appendChild($parent);

        $metrics = $domDocument->createElement('metrics');
        $metrics->setAttribute('loc', '3');
        $metrics->setAttribute('ncloc', '2');
        $metrics->setAttribute('classes', '1');
        $parent->appendChild($metrics);

        $domXpath = new \DOMXPath($domDocument);
        $xpathElement = new XpathElement($domXpath, $metrics);

        $this->fileMetrics = new FileMetrics($xpathElement);
    }

    public function testGetLoc(): void
    {
        $actual = $this->fileMetrics->getLoc();
        $this->assertSame(3, $actual);
    }

    public function testGetNcloc(): void
    {
        $actual = $this->fileMetrics->getNcloc();
        $this->assertSame(2, $actual);
    }

    public function testGetClasses(): void
    {
        $actual = $this->fileMetrics->getClasses();
        $this->assertSame(1, $actual);
    }
}
