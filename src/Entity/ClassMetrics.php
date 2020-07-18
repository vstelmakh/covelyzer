<?php

declare(strict_types=1);

namespace VStelmakh\Covelyzer\Entity;

class ClassMetrics extends AbstractMetrics
{
    /**
     * @return int
     */
    public function getComplexity(): int
    {
        return (int) $this->getXpathElement()->getAttribute('complexity');
    }

    /**
     * @inheritDoc
     */
    protected function getExpectedParentTagName(): string
    {
        return ClassEntity::NAME;
    }
}
