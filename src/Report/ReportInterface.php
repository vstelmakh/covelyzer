<?php

namespace VStelmakh\Covelyzer\Report;

use VStelmakh\Covelyzer\CovelyzerStyle;

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
