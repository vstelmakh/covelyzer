<?php

namespace VStelmakh\Covelyzer\Report;

use Symfony\Component\Console\Output\OutputInterface;

interface ReportInterface
{
    /**
     * Render report details to output
     *
     * @param OutputInterface $output
     */
    public function render(OutputInterface $output): void;

    /**
     * True if satisfy configuration constraints
     *
     * @return bool
     */
    public function isSuccess(): bool;
}
