<?php

declare(strict_types=1);

namespace VStelmakh\Covelyzer\Entity;

use VStelmakh\Covelyzer\Dom\XpathElement;

class Project extends AbstractEntity
{
    public const NAME = 'project';

    /**
     * @var \DateTimeImmutable|null
     */
    private $timestamp;

    /**
     * @param XpathElement $xpathElement
     */
    public function __construct(XpathElement $xpathElement)
    {
        parent::__construct($xpathElement);

        $timestamp = $this->getXpathElement()->getAttribute('timestamp');
        $datetime = $timestamp ? \DateTimeImmutable::createFromFormat('U', $timestamp) : null;
        $this->timestamp = $datetime ?: null;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getTimestamp(): ?\DateTimeImmutable
    {
        return $this->timestamp;
    }

    /**
     * @return ProjectMetrics
     */
    public function getMetrics(): ProjectMetrics
    {
        /** @var \DOMElement $metricsNode */
        $metricsNode = $this->getXpathElement()->xpath('metrics')->item(0);
        $metricsXpathElement = $this->getXpathElement()->createElement($metricsNode);
        return new ProjectMetrics($metricsXpathElement);
    }

    /**
     * @return \Generator|File[]
     */
    public function getFiles(): \Generator
    {
        $fileElements = $this->getXpathElement()->xpath('.//file');
        foreach ($fileElements as $fileElement) {
            $fileXpathElement = $this->getXpathElement()->createElement($fileElement);
            yield new File($fileXpathElement);
        }
    }

    /**
     * @return \Generator|ClassEntity[]
     */
    public function getClasses(): \Generator
    {
        $classElements = $this->getXpathElement()->xpath('.//class');
        foreach ($classElements as $classElement) {
            $fileXpathElement = $this->getXpathElement()->createElement($classElement);
            yield new ClassEntity($fileXpathElement);
        }
    }

    /**
     * @inheritDoc
     */
    protected function getTagName(): string
    {
        return self::NAME;
    }
}
