<?php

namespace VStelmakh\Covelyzer\Tests\Dom;

use VStelmakh\Covelyzer\Dom\XpathElement;
use PHPUnit\Framework\TestCase;

class XpathElementTest extends TestCase
{
    /**
     * @var \DOMDocument
     */
    private $domDocument;

    /**
     * @var \DOMXPath
     */
    private $domXpath;

    public function setUp(): void
    {
        $this->domDocument = new \DOMDocument();

        $domElementA = $this->domDocument->createElement('a');
        $domElementA->setAttribute('x', '1');
        $this->domDocument->appendChild($domElementA);

        $domElementB = $this->domDocument->createElement('b');
        $domElementA->appendChild($domElementB);

        $this->domXpath = new \DOMXPath($this->domDocument);
    }

    public function testConstruct(): void
    {
        $domElement = new \DOMElement('test');
        $this->expectException(\InvalidArgumentException::class);
        new XpathElement($this->domXpath, $domElement);
    }

    public function testCreateElement(): void
    {
        $domElementA = $this->queryFirsrtElement('//a');
        $domElementB = $this->queryFirsrtElement('//b');

        $xpathElementA = new XpathElement($this->domXpath, $domElementA);
        $xpathElementB = $xpathElementA->createElement($domElementB);

        $nameA = $xpathElementA->getTagName();
        $nameB = $xpathElementB->getTagName();

        $this->assertSame('a', $nameA);
        $this->assertSame('b', $nameB);
    }

    public function testXpath(): void
    {
        $domElementA = $this->queryFirsrtElement('//a');
        $xpathElementA = new XpathElement($this->domXpath, $domElementA);

        $documentDomElementB = $this->queryFirsrtElement('//b');
        $domElementB = $xpathElementA->xpath('b')->item(0);

        $this->assertSame($documentDomElementB, $domElementB);
    }

    public function testGetTagName(): void
    {
        $domElement = $this->queryFirsrtElement('//a');
        $xpathElement = new XpathElement($this->domXpath, $domElement);

        $name = $xpathElement->getTagName();
        $this->assertSame('a', $name);
    }

    public function testGetAttribute(): void
    {
        $domElement = $this->queryFirsrtElement('//a');
        $xpathElement = new XpathElement($this->domXpath, $domElement);

        $attribute = $xpathElement->getAttribute('x');
        $this->assertSame('1', $attribute);

        $attribute = $xpathElement->getAttribute('y');
        $this->assertNull($attribute);
    }

    public function testGetParent(): void
    {
        $domElementA = $this->queryFirsrtElement('//a');
        $domElementB = $this->queryFirsrtElement('//b');

        $xpathElementA = new XpathElement($this->domXpath, $domElementA);
        $xpathElementB = new XpathElement($this->domXpath, $domElementB);

        $parentA = $xpathElementA->getParent();
        $this->assertSame($parentA, $this->domDocument);

        $parentB = $xpathElementB->getParent();
        $this->assertSame($domElementA, $parentB);
    }

    /**
     * @param string $xpath
     * @return \DOMElement
     */
    private function queryFirsrtElement(string $xpath): \DOMElement
    {
        /** @var \DOMNodeList<\DOMElement> $list */
        $list = $this->domXpath->query($xpath);
        /** @var \DOMElement $element */
        $element = $list->item(0);
        return $element;
    }
}
