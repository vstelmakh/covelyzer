<?php

declare(strict_types=1);

namespace VStelmakh\Covelyzer\Entity;

use VStelmakh\Covelyzer\Dom\XpathElement;

abstract class AbstractEntity
{
    /**
     * @var XpathElement
     */
    private $xpathElement;

    /**
     * @param XpathElement $xpathElement
     */
    public function __construct(XpathElement $xpathElement)
    {
        $this->validateElement($xpathElement);
        $this->xpathElement = $xpathElement;
    }

    /**
     * Return node (tag) name this entity related to
     *
     * @return string
     */
    abstract protected function getTagName(): string;

    /**
     * @return XpathElement
     */
    public function getXpathElement(): XpathElement
    {
        return $this->xpathElement;
    }

    /**
     * @param XpathElement $xpathElement
     */
    protected function validateElement(XpathElement $xpathElement): void
    {
        $elementName = $xpathElement->getTagName();
        $entityName = $this->getTagName();
        if ($elementName !== $entityName) {
            throw new \InvalidArgumentException(sprintf(
                'XML node expected to be "%s", "%s" given',
                $entityName,
                $elementName
            ));
        }
    }
}
