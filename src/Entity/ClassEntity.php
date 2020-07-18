<?php

declare(strict_types=1);

namespace VStelmakh\Covelyzer\Entity;

class ClassEntity extends AbstractEntity
{
    public const NAME = 'class';

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->getXpathElement()->getAttribute('name') ?? '';
    }

    /**
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->getXpathElement()->getAttribute('namespace') ?? '';
    }

    /**
     * @return ClassMetrics
     */
    public function getMetrics(): ClassMetrics
    {
        /** @var \DOMElement $metricsNode */
        $metricsNode = $this->getXpathElement()->xpath('metrics')->item(0);
        $metricsXpathElement = $this->getXpathElement()->createElement($metricsNode);
        return new ClassMetrics($metricsXpathElement);
    }

    /**
     * @inheritDoc
     */
    protected function getTagName(): string
    {
        return self::NAME;
    }
}
