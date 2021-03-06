<?php

declare(strict_types=1);

namespace VStelmakh\Covelyzer\Tests\Command;

use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Console\Tester\CommandTester;
use VStelmakh\Covelyzer\Command\CovelyzerCommand;
use PHPUnit\Framework\TestCase;
use VStelmakh\Covelyzer\Dom\DocumentFactory;
use VStelmakh\Covelyzer\Entity\Config;
use VStelmakh\Covelyzer\Parser\ConfigParser;
use VStelmakh\Covelyzer\Parser\CoverageParser;
use VStelmakh\Covelyzer\Util\ConfigLocator;
use VStelmakh\Covelyzer\Util\FileReader;

class CovelyzerCommandTest extends TestCase
{
    private const REPORT_PATH = __DIR__ . '/coverage.xml';

    /**
     * @var CommandTester
     */
    private $commandTester;

    /**
     * @var ConfigLocator&MockObject
     */
    private $configLocator;

    /**
     * @var ConfigParser&MockObject
     */
    private $configParser;

    /**
     * @var CoverageParser
     */
    private $coverageParser;

    public function setUp(): void
    {
        $this->configLocator = $this->createMock(ConfigLocator::class);
        $this->configParser = $this->createMock(ConfigParser::class);
        $this->coverageParser = new CoverageParser(new DocumentFactory(), new FileReader());

        $command = new CovelyzerCommand($this->configLocator, $this->configParser, $this->coverageParser);
        $this->commandTester = new CommandTester($command);
    }

    /**
     * @dataProvider executeNoCoverageArgDataProvider
     *
     * @param array&string[] $input
     */
    public function testExecuteCoverageArg(array $input): void
    {
        $this->expectException(\RuntimeException::class);
        $this->commandTester->execute($input);
    }

    /**
     * @return array[]
     */
    public function executeNoCoverageArgDataProvider(): array
    {
        return [
            [[]],
            [[CovelyzerCommand::ARG_COVERAGE => '/dev/null/coverage.xml']],
        ];
    }

    /**
     * @dataProvider executeConfigOptionDataProvider
     *
     * @param string|null $configPath
     * @param bool $isSuccess
     */
    public function testExecuteConfigOption(?string $configPath, bool $isSuccess): void
    {
        $input = [CovelyzerCommand::ARG_COVERAGE => self::REPORT_PATH];
        if ($configPath !== null) {
            $input['--' . CovelyzerCommand::OPT_CONFIG] = $configPath;
        }

        if (!$isSuccess) {
            $this->expectException(\RuntimeException::class);
        }

        $defaultConfig = 'resources/default-config.xml';
        $this->configLocator->method('getConfigPath')->willReturn($defaultConfig);
        $this->commandTester->execute($input);

        $output = $this->commandTester->getDisplay();

        if ($configPath !== null) {
            self::assertStringContainsString('Config:    ' . realpath($configPath), $output);
        } else {
            self::assertStringContainsString('Config:    ' . $defaultConfig, $output);
        }
    }

    /**
     * @return array[]
     */
    public function executeConfigOptionDataProvider(): array
    {
        return [
            [null, true],
            ['covelyzer.xml', true],
            ['./covelyzer.xml', true],
            ['/dev/null/covelyzer.xml', false],
        ];
    }

    /**
     * @dataProvider executeTimezoneOptionDataProvider
     *
     * @param string $timezone
     * @throws \Exception
     */
    public function testExecuteTimezoneOption(string $timezone): void
    {
        $input = [
            CovelyzerCommand::ARG_COVERAGE => self::REPORT_PATH,
            '--' . CovelyzerCommand::OPT_TIMEZONE => $timezone
        ];

        $this->commandTester->execute($input);
        $output = $this->commandTester->getDisplay();

        $datetime = new \DateTime('2020-08-02 19:20:43');
        $datetime->setTimezone(new \DateTimeZone($timezone));
        $datetimeFormat = $datetime->format('Y-m-d H:i:s T');

        self::assertStringContainsString('Timestamp: ' . $datetimeFormat, $output);
    }

    /**
     * @return array[]
     */
    public function executeTimezoneOptionDataProvider(): array
    {
        return [
            ['GMT+3'],
            ['Europe/Berlin'],
        ];
    }

    /**
     * @dataProvider executeDataProvider
     *
     * @param float|null $minProjectCoverage
     * @param float|null $minClassCoverage
     * @param bool $isSuccess
     */
    public function testExecute(?float $minProjectCoverage, ?float $minClassCoverage, bool $isSuccess): void
    {
        $config = $this->createMock(Config::class);
        $config->method('getMinProjectCoverage')->willReturn($minProjectCoverage);
        $config->method('getMinClassCoverage')->willReturn($minClassCoverage);
        $this->configLocator->method('getConfigPath')->willReturn('');
        $this->configParser->method('parseConfig')->willReturn($config);

        $this->commandTester->execute([CovelyzerCommand::ARG_COVERAGE => self::REPORT_PATH]);
        $output = $this->commandTester->getDisplay();

        $reportPath = realpath(self::REPORT_PATH);
        self::assertStringContainsString('Report:    ' . $reportPath, $output);
        self::assertStringContainsString('Timestamp: 2020-08-02 19:20:43', $output);

        if ($minProjectCoverage !== null) {
            self::assertStringContainsString('Project coverage', $output);
        }

        if ($minClassCoverage !== null) {
            self::assertStringContainsString('Class coverage', $output);
        }

        if ($isSuccess) {
            self::assertStringContainsString(' SUCCESS ', $output);
        } else {
            self::assertStringContainsString(' FAIL ', $output);
        }
    }

    /**
     * @return array[]
     */
    public function executeDataProvider(): array
    {
        return [
            [null, null, true],
            [90, 90, true],
            [90, null, true],
            [null, 90, true],
            [91, 91, false],
            [91, null, false],
            [null, 91, false],
        ];
    }
}
