<?php

declare(strict_types=1);

namespace VStelmakh\Covelyzer\Entity;

use VStelmakh\Covelyzer\Dom\XpathElement;

class Project extends AbstractEntity
{
    public const NAME = 'project';

    /**
     * @var \DateTime|null
     */
    private $timestamp;

    /**
     * @param XpathElement $xpathElement
     */
    public function __construct(XpathElement $xpathElement)
    {
        parent::__construct($xpathElement);

        $timestamp = $this->getXpathElement()->getAttribute('timestamp');
        $datetime = $timestamp ? \DateTime::createFromFormat('U', $timestamp) : null;
        $this->timestamp = $datetime ?: null;
    }

    /**
     * @param string|null $timezone
     * @return \DateTime|null see supported timezones https://www.php.net/manual/en/timezones.php
     */
    public function getTimestamp(string $timezone = null): ?\DateTime
    {
        if ($this->timestamp === null) {
            // @codeCoverageIgnoreStart
            return null;
            // @codeCoverageIgnoreEnd
        }

        $datetime = clone $this->timestamp;
        if ($timezone !== null) {
            $datetime->setTimezone(new \DateTimeZone($timezone));
        }
        return $datetime;
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
