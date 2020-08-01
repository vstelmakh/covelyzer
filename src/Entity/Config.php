<?php

declare(strict_types=1);

namespace VStelmakh\Covelyzer\Entity;

class Config extends AbstractEntity
{
    public const NAME = 'covelyzer';

    /**
     * @inheritDoc
     */
    protected function getTagName(): string
    {
        return self::NAME;
    }

    /**
     * @return float|null
     */
    public function getMinProjectCoverage(): ?float
    {
        $minCoverage = $this->getChildAttribute('project', 'minCoverage');
        return $minCoverage !== null ? (float) $minCoverage : null;
    }

    /**
     * @return float|null
     */
    public function getMinClassCoverage(): ?float
    {
        $minCoverage = $this->getChildAttribute('class', 'minCoverage');
        return $minCoverage !== null ? (float) $minCoverage : null;
    }

    /**
     * @param string $nodeName
     * @param string $attributeName
     * @return string|null
     */
    private function getChildAttribute(string $nodeName, string $attributeName): ?string
    {
        /** @var \DOMElement|null $node */
        $node = $this->getXpathElement()->xpath($nodeName)->item(0);
        if ($node === null) {
            return null;
        }

        $element = $this->getXpathElement()->createElement($node);
        return $element->getAttribute($attributeName);
    }
}
