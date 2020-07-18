<?php

declare(strict_types=1);

namespace VStelmakh\Covelyzer\Tests\Entity;

use VStelmakh\Covelyzer\Dom\XpathElement;
use VStelmakh\Covelyzer\Entity\ClassEntity;
use PHPUnit\Framework\TestCase;
use VStelmakh\Covelyzer\Entity\ClassMetrics;

class ClassEntityTest extends TestCase
{
    private const NAME = 'Example\ClassNamespace\Name';
    private const NAMESPACE = 'Example\ClassNamespace';

    /**
     * @var \DOMDocument
     */
    private $domDocument;

    /**
     * @var ClassEntity
     */
    private $class;

    public function setUp(): void
    {
        $this->domDocument = new \DOMDocument();

        $file = $this->domDocument->createElement('class');
        $file->setAttribute('name', self::NAME);
        $file->setAttribute('namespace', self::NAMESPACE);
        $this->domDocument->appendChild($file);

        $metrics = $this->domDocument->createElement('metrics');
        $file->appendChild($metrics);

        $domXpath = new \DOMXPath($this->domDocument);
        $xpathElement = new XpathElement($domXpath, $file);

        $this->class = new ClassEntity($xpathElement);
    }

    public function testGetName(): void
    {
        $actual = $this->class->getName();
        $this->assertSame(self::NAME, $actual);
    }

    public function testGetNamespace(): void
    {
        $actual = $this->class->getNamespace();
        $this->assertSame(self::NAMESPACE, $actual);
    }

    public function testGetMetrics(): void
    {
        $actual = $this->class->getMetrics();

        /** @var \DOMElement $metricsNode */
        $metricsNode = $this->domDocument->getElementsByTagName('metrics')->item(0);
        $metricsXpathElement = $this->class->getXpathElement()->createElement($metricsNode);
        $expected = new ClassMetrics($metricsXpathElement);

        $this->assertEquals($expected, $actual);
    }
}
