<?php

declare(strict_types=1);

namespace VStelmakh\Covelyzer\Tests\Entity;

use VStelmakh\Covelyzer\Entity\ClassEntity;
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

        $class = $this->domDocument->createElement('class');
        $file->appendChild($class);

        $domXpath = new \DOMXPath($this->domDocument);
        $xpathElement = new XpathElement($domXpath, $file);

        $this->file = new File($xpathElement);
    }

    public function testGetName(): void
    {
        $actual = $this->file->getName();
        self::assertSame(self::FILENAME, $actual);
    }

    public function testGetMetrics(): void
    {
        $actual = $this->file->getMetrics();

        /** @var \DOMElement $metricsNode */
        $metricsNode = $this->domDocument->getElementsByTagName('metrics')->item(0);
        $metricsXpathElement = $this->file->getXpathElement()->createElement($metricsNode);
        $expected = new FileMetrics($metricsXpathElement);

        self::assertEquals($expected, $actual);
    }

    public function testGetClasses(): void
    {
        $actual = $this->file->getClasses();

        /** @var \DOMElement $classNode */
        $classNode = $this->domDocument->getElementsByTagName('class')->item(0);
        $classXpathElement = $this->file->getXpathElement()->createElement($classNode);
        $class = new ClassEntity($classXpathElement);
        $expected = [$class];

        self::assertEquals($expected, iterator_to_array($actual));
    }
}
