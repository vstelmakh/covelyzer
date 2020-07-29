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
        $minProjectCoverage = $this->getXpathElement()->getAttribute('minProjectCoverage');
        return $minProjectCoverage !== null ? (float) $minProjectCoverage : null;
    }

    /**
     * @return float|null
     */
    public function getMinClassCoverage(): ?float
    {
        $minProjectCoverage = $this->getXpathElement()->getAttribute('minClassCoverage');
        return $minProjectCoverage !== null ? (float) $minProjectCoverage : null;
    }
}
