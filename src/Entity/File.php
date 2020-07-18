<?php

declare(strict_types=1);

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
