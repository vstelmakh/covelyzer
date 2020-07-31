<?php

declare(strict_types=1);

namespace VStelmakh\Covelyzer\Console;

use VStelmakh\Covelyzer\Entity\ClassEntity;

class ClassCoverageTableRenderer
{
    /**
     * @param CovelyzerStyle $covelyzerStyle
     * @param iterable|ClassEntity[] $classes
     */
    public function render(CovelyzerStyle $covelyzerStyle, iterable $classes): void
    {
        $headers = ['Class', 'Coverage'];
        $rows = [];
        foreach ($classes as $class) {
            $rows[] = $this->buildRow($class);
        }
        $covelyzerStyle->table($headers, $rows);
    }

    /**
     * @param ClassEntity $class
     * @return array<int|float|string|null>
     */
    private function buildRow(ClassEntity $class): array
    {
        return  [
            $class->getName(),
            $class->getMetrics()->getCoverage()
        ];
    }
}
