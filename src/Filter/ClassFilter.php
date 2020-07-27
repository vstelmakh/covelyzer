<?php

declare(strict_types=1);

namespace VStelmakh\Covelyzer\Filter;

use VStelmakh\Covelyzer\Entity\ClassEntity;

class ClassFilter
{
    /**
     * @var iterable|ClassEntity[]
     */
    private $classes;

    /**
     * @var int|null
     */
    private $limit;

    /**
     * @var float|null
     */
    private $coverageBelow;

    /**
     * @param iterable|ClassEntity[] $classes
     */
    public function __construct(iterable $classes)
    {
        $this->classes = $classes;
    }

    /**
     * @param int|null $limit
     * @return self
     */
    public function setLimit(?int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @param float|null $coverageBelow
     * @return self
     */
    public function setCoverageBelow(?float $coverageBelow): self
    {
        $this->coverageBelow = $coverageBelow;
        return $this;
    }

    /**
     * @return array&ClassEntity[]
     */
    public function getResult(): array
    {
        $result = [];
        $names = [];
        $coverages = [];

        foreach ($this->classes as $class) {
            $coverage = $class->getMetrics()->getCoverage();

            if ($this->coverageBelow !== null && ($coverage === null || $coverage >= $this->coverageBelow)) {
                continue;
            }

            $result[] = $class;
            $names[] = $class->getName();
            $coverages[] = $coverage;
        }

        array_multisort(
            $coverages,
            SORT_NUMERIC,
            SORT_ASC,
            $names,
            SORT_NATURAL,
            SORT_ASC,
            $result
        );

        return array_slice($result, 0, $this->limit);
    }
}
