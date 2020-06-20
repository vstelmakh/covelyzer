<?php

namespace VStelmakh\Covelyzer\Tests\Entity;

use VStelmakh\Covelyzer\Entity\AbstractEntity;
use PHPUnit\Framework\TestCase;
use VStelmakh\Covelyzer\Xml\XpathElement;

class AbstractEntityTest extends TestCase
{
    public function testGetXpathElement(): void
    {
        $domDocument = new \DOMDocument();

        $tag = $domDocument->createElement('tag');
        $domDocument->appendChild($tag);

        $domXpath = new \DOMXPath($domDocument);
        $xpathElement = new XpathElement($domXpath, $tag);

        $abstractEntity = $this->createAbstractEntity($xpathElement);
        $actual = $abstractEntity->getXpathElement();
        $this->assertSame($xpathElement, $actual);
    }

    public function testValidateElement(): void
    {
        $domDocument = new \DOMDocument();

        $tag = $domDocument->createElement('unexpected_tag');
        $domDocument->appendChild($tag);

        $domXpath = new \DOMXPath($domDocument);
        $xpathElement = new XpathElement($domXpath, $tag);

        $this->expectException(\InvalidArgumentException::class);
        $this->createAbstractEntity($xpathElement);
    }

    private function createAbstractEntity(XpathElement $xpathElement): AbstractEntity
    {
        return new class ($xpathElement) extends AbstractEntity {
            protected function getTagName(): string
            {
                return 'tag';
            }
        };
    }
}
