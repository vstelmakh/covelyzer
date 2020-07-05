<?php

declare(strict_types=1);

namespace VStelmakh\Covelyzer;

use VStelmakh\Covelyzer\Entity\AbstractMetrics;

class CoverageCalculator
{
    /**
     * @var int
     */
    private $precision;

    /**
     * @param int $precision number of digits after the decimal point
     */
    public function __construct(int $precision = 2)
    {
        $this->precision = $precision;
    }

    /**
     * Return coverage percentage or null if metrics don't have any elements (e.g. interface)
     *
     * @param AbstractMetrics $metrics
     * @return float|null
     */
    public function getCoverage(AbstractMetrics $metrics): ?float
    {
        $elements = $metrics->getElements();
        $coveredElements = $metrics->getCoveredElements();
        return $elements > 0
            ? round(($coveredElements / $elements) * 100, $this->precision, PHP_ROUND_HALF_UP)
            : null;
    }
}
