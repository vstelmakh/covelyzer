<?php

namespace VStelmakh\Covelyzer\Entity;

class File extends AbstractEntity
{
    public const NAME = 'file';

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->getXpathElement()->getAttribute('name') ?? '';
    }

    /**
     * @return FileMetrics
     */
    public function getMetrics(): FileMetrics
    {
        /** @var \DOMElement $metricsNode */
        $metricsNode = $this->getXpathElement()->xpath('metrics')->item(0);
        $metricsXpathElement = $this->getXpathElement()->createElement($metricsNode);
        return new FileMetrics($metricsXpathElement);
    }

    /**
     * @inheritDoc
     */
    protected function getTagName(): string
    {
        return self::NAME;
    }
}
