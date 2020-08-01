<?php

declare(strict_types=1);

namespace VStelmakh\Covelyzer\Report;

use VStelmakh\Covelyzer\Console\CovelyzerStyle;

interface ReportInterface
{
    /**
     * Render report details
     *
     * @param CovelyzerStyle $covelyzerStyle
     */
    public function render(CovelyzerStyle $covelyzerStyle): void;

    /**
     * True if satisfy configuration constraints
     *
     * @return bool
     */
    public function isSuccess(): bool;
}
