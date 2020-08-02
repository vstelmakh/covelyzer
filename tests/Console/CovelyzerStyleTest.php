<?php

declare(strict_types=1);

namespace VStelmakh\Covelyzer\Tests\Console;

use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\OutputStyle;
use VStelmakh\Covelyzer\Command\CovelyzerCommand;
use VStelmakh\Covelyzer\Console\CovelyzerStyle;
use PHPUnit\Framework\TestCase;

class CovelyzerStyleTest extends TestCase
{
    /**
     * @var OutputInterface&MockObject
     */
    private $output;

    /**
     * @var CovelyzerStyle
     */
    private $covelyzerStyle;

    public function setUp(): void
    {
        $this->output = $this->createMock(OutputInterface::class);
        $this->covelyzerStyle = new CovelyzerStyle($this->output);
    }

    public function testHeader(): void
    {
        $messages = [
            '                        __                     ',
            '  _________ _   _____  / /_  ______  ___  _____',
            ' / ___/ __ \ | / / _ \/ / / / /_  / / _ \/ ___/',
            '/ /__/ /_/ / |/ /  __/ / /_/ / / /_/  __/ /    ',
            '\___/\____/|___/\___/_/\__, / /___/\___/_/     ',
            '                      /____/                   ',
        ];

        $this->output
            ->expects(self::once())
            ->method('writeln')
            ->with(self::identicalTo($messages), self::identicalTo(OutputStyle::OUTPUT_NORMAL));

        $this->covelyzerStyle->header();
    }

    /**
     * @dataProvider statusDataProvider
     *
     * @param int $status
     * @param string|null $message
     * @param bool $isException
     */
    public function testStatus(int $status, ?string $message, bool $isException): void
    {
        if ($isException) {
            $this->expectException(\RuntimeException::class);
        } else {
            $this->output
                ->expects(self::once())
                ->method('writeln')
                ->with(self::identicalTo($message), self::identicalTo(OutputStyle::OUTPUT_NORMAL));
        }

        $this->covelyzerStyle->status($status);
    }

    /**
     * @return array&array[]
     */
    public function statusDataProvider(): array
    {
        return [
            [CovelyzerCommand::SUCCESS, '<bg=green> SUCCESS </>', false],
            [CovelyzerCommand::FAILURE, '<bg=red> FAIL </>', false],
            [2, null, true],
        ];
    }

    /**
     * @dataProvider titleDataProvider
     *
     * @param string $text
     * @param bool|null $isSuccess
     */
    public function testTitle(string $text, ?bool $isSuccess = null, string $expected): void
    {
        $this->output
            ->expects(self::once())
            ->method('writeln')
            ->with(self::identicalTo($expected), self::identicalTo(OutputStyle::OUTPUT_NORMAL));

        $this->covelyzerStyle->title($text, $isSuccess);
    }

    /**
     * @return array&array[]
     */
    public function titleDataProvider(): array
    {
        return [
            ['text', null, '<options=bold>text</>'],
            ['text', true, '<options=bold>text</> <fg=green>✔</>'],
            ['text', false, '<options=bold>text</> <fg=red>✘</>'],
        ];
    }

    /**
     * @dataProvider coverageDataProvider
     *
     * @param float|null $coverage
     * @param string $coverageText
     * @param float $required
     * @param string $requiredText
     */
    public function testCoverage(?float $coverage, string $coverageText, float $required, string $requiredText): void
    {

        $this->output
            ->expects(self::once())
            ->method('write')
            ->with(self::identicalTo(PHP_EOL));

        $this->output
            ->expects(self::exactly(2))
            ->method('writeln')
            ->withConsecutive(
                [self::identicalTo($coverageText), self::identicalTo(OutputStyle::OUTPUT_NORMAL)],
                [self::identicalTo($requiredText), self::identicalTo(OutputStyle::OUTPUT_NORMAL)]
            );

        $this->covelyzerStyle->coverage($coverage, $required);
    }

    /**
     * @return array&array[]
     */
    public function coverageDataProvider(): array
    {
        return [
            [null, '  Coverage: --', 100, '  Required: 100%'],
            [1, '  Coverage: 1%', 2, '  Required: 2%'],
        ];
    }

    public function testTable(): void
    {
        $headers = ['Header 1', 'Header 2'];
        $rows = [
            ['value 11', 'value 12'],
            ['value 21', 'value 22'],
        ];

        $output = new BufferedOutput();
        $covelyzerStyle = new CovelyzerStyle($output);
        $covelyzerStyle->table($headers, $rows);

        $expected = <<<TXT
 ---------- ---------- 
  Header 1   Header 2  
 ---------- ---------- 
  value 11   value 12  
  value 21   value 22  
 ---------- ---------- 

TXT;

        $actual = $output->fetch();
        self::assertSame($expected, $actual);
    }

    public function testWrite(): void
    {
        $messages = '';
        $newline = true;
        $type = 1;

        $this->output
            ->expects(self::once())
            ->method('write')
            ->with(self::identicalTo($messages), self::identicalTo($newline), self::identicalTo($type));

        $this->covelyzerStyle->write($messages, $newline, $type);
    }

    public function testWriteln(): void
    {
        $messages = '';
        $type = 1;

        $this->output
            ->expects(self::once())
            ->method('writeln')
            ->with(self::identicalTo($messages), self::identicalTo($type));

        $this->covelyzerStyle->writeln($messages, $type);
    }

    public function testNewLine(): void
    {
        $this->output
            ->expects(self::once())
            ->method('write')
            ->with(self::identicalTo(PHP_EOL . PHP_EOL));

        $this->covelyzerStyle->newLine(2);
    }
}
