<?php

namespace VStelmakh\Covelyzer\Entity;

use VStelmakh\Covelyzer\Dom\XpathElement;

abstract class AbstractMetrics extends AbstractEntity
{
    public const NAME = 'metrics';

    /**
     * Parent tag name for XpathElement validation
     *
     * @return string
     */
    abstract protected function getExpectedParentTagName(): string;

    /**
     * @return int
     */
    public function getMethods(): int
    {
        return (int) $this->getXpathElement()->getAttribute('methods');
    }

    /**
     * @return int
     */
    public function getCoveredMethods(): int
    {
        return (int) $this->getXpathElement()->getAttribute('coveredMethods');
    }

    /**
     * @return int
     */
    public function getConditionals(): int
    {
        return (int) $this->getXpathElement()->getAttribute('conditionals');
    }

    /**
     * @return int
     */
    public function getCoveredConditionals(): int
    {
        return (int) $this->getXpathElement()->getAttribute('coveredConditionals');
    }

    /**
     * @return int
     */
    public function getStatements(): int
    {
        return (int) $this->getXpathElement()->getAttribute('statements');
    }

    /**
     * @return int
     */
    public function getCoveredStatements(): int
    {
        return (int) $this->getXpathElement()->getAttribute('coveredStatements');
    }

    /**
     * @return int
     */
    public function getElements(): int
    {
        return (int) $this->getXpathElement()->getAttribute('elements');
    }

    /**
     * @return int
     */
    public function getCoveredElements(): int
    {
        return (int) $this->getXpathElement()->getAttribute('coveredElements');
    }

    /**
     * @inheritDoc
     */
    protected function getTagName(): string
    {
        return self::NAME;
    }

    /**
     * @param XpathElement $xpathElement
     */
    protected function validateElement(XpathElement $xpathElement): void
    {
        parent::validateElement($xpathElement);

        $parentElement = $xpathElement->getParent();
        $parentElementName = $parentElement ? $parentElement->nodeName : null;
        $expectedParentTagName = $this->getExpectedParentTagName();

        if ($parentElementName !== $expectedParentTagName) {
            throw new \InvalidArgumentException(sprintf(
                'XML node expected to be ancestor of "%s", "%s" given',
                $expectedParentTagName,
                $parentElementName
            ));
        }
    }
}
