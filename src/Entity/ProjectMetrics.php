<?php

declare(strict_types=1);

namespace VStelmakh\Covelyzer\Entity;

class ProjectMetrics extends AbstractMetrics
{
    /**
     * @return int
     */
    public function getFiles(): int
    {
        return (int) $this->getXpathElement()->getAttribute('files');
    }

    /**
     * @return int
     */
    public function getLoc(): int
    {
        return (int) $this->getXpathElement()->getAttribute('loc');
    }

    /**
     * @return int
     */
    public function getNcloc(): int
    {
        return (int) $this->getXpathElement()->getAttribute('ncloc');
    }

    /**
     * @return int
     */
    public function getClasses(): int
    {
        return (int) $this->getXpathElement()->getAttribute('classes');
    }

    /**
     * @inheritDoc
     */
    protected function getExpectedParentTagName(): string
    {
        return Project::NAME;
    }
}
