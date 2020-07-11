<?php

declare(strict_types=1);

namespace VStelmakh\Covelyzer;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableStyle;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\OutputStyle;

class CovelyzerStyle
{
    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @param OutputInterface $output
     */
    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function header(): void
    {
        $this->writeln([
            '                        __                     ',
            '  _________ _   _____  / /_  ______  ___  _____',
            ' / ___/ __ \ | / / _ \/ / / / /_  / / _ \/ ___/',
            '/ /__/ /_/ / |/ /  __/ / /_/ / / /_/  __/ /    ',
            '\___/\____/|___/\___/_/\__, / /___/\___/_/     ',
            '                      /____/                   ',
            '',
        ]);
    }

    /**
     * @param int $status
     */
    public function status(int $status): void
    {
        $this->newLine();
        switch ($status) {
            case CovelyzerCommand::SUCCESS:
                $this->writeln('<bg=green> SUCCESS </>');
                break;
            case CovelyzerCommand::FAILURE:
                $this->writeln('<bg=red> FAIL </>');
                break;
            default:
                throw new \RuntimeException(sprintf('Unexpected status code "%s"', $status));
        }
    }

    /**
     * @param string $text
     * @param bool|null $isSuccess
     */
    public function title(string $text, ?bool $isSuccess = null): void
    {
        $status = $isSuccess ? ' <fg=green>✔</>' : ' <fg=red>✘</>';
        $status = $isSuccess !== null ? $status : '';
        $this->writeln('<options=bold>' . $text . '</>' . $status);
    }

    /**
     * @param float|null $coverage
     * @param float $required
     */
    public function coverage(?float $coverage, float $required): void
    {
        $coverageValue = $coverage !== null ? $coverage . '%' : '--';
        $this->newLine();
        $this->writeln('  Coverage: ' . $coverageValue);
        $this->writeln('  Required: ' . $required . '%');
    }

    /**
     * @param mixed[] $headers
     * @param mixed[] $rows
     */
    public function table(array $headers, array $rows): void
    {
        $style = new TableStyle();
        $style
            ->setHorizontalBorderChars('-')
            ->setVerticalBorderChars(' ')
            ->setDefaultCrossingChar(' ')
            ->setHeaderTitleFormat('<fg=default;bg=default>%s</>')
        ;

        $table = new Table($this->output);
        $table->setHeaders($headers);
        $table->setRows($rows);
        $table->setStyle($style);

        $table->render();
    }

    /**
     * @param string|iterable<mixed> $messages
     * @param bool $newline
     * @param int $type
     */
    public function write($messages, bool $newline = false, int $type = OutputStyle::OUTPUT_NORMAL): void
    {
        $this->output->write($messages, $newline, $type);
    }

    /**
     * @param string|iterable<mixed> $messages
     * @param int $type
     */
    public function writeln($messages, int $type = OutputStyle::OUTPUT_NORMAL): void
    {
        $this->output->writeln($messages, $type);
    }

    /**
     * @param int $count
     */
    public function newLine(int $count = 1): void
    {
        $this->output->write(str_repeat(PHP_EOL, $count));
    }
}
