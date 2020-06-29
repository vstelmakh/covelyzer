<?php

namespace VStelmakh\Covelyzer\Tests\Entity;

use VStelmakh\Covelyzer\Entity\File;
use PHPUnit\Framework\TestCase;
use VStelmakh\Covelyzer\Entity\FileMetrics;
use VStelmakh\Covelyzer\Dom\XpathElement;

class FileTest extends TestCase
{
    private const FILENAME = '/example/file/name';

    /**
     * @var \DOMDocument
     */
    private $domDocument;

    /**
     * @var File
     */
    private $file;

    public function setUp(): void
    {
        $this->domDocument = new \DOMDocument();

        $file = $this->domDocument->createElement('file');
        $file->setAttribute('name', self::FILENAME);
        $this->domDocument->appendChild($file);

        $metrics = $this->domDocument->createElement('metrics');
        $file->appendChild($metrics);

        $domXpath = new \DOMXPath($this->domDocument);
        $xpathElement = new XpathElement($domXpath, $file);

        $this->file = new File($xpathElement);
    }

    public function testGetName(): void
    {
        $actual = $this->file->getName();
        $this->assertSame(self::FILENAME, $actual);
    }

    public function testGetMetrics(): void
    {
        $actual = $this->file->getMetrics();

        /** @var \DOMElement $metricsNode */
        $metricsNode = $this->domDocument->getElementsByTagName('metrics')->item(0);
        $metricsXpathElement = $this->file->getXpathElement()->createElement($metricsNode);
        $expected = new FileMetrics($metricsXpathElement);

        $this->assertEquals($expected, $actual);
    }
}
