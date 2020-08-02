<?php

declare(strict_types=1);

namespace VStelmakh\Covelyzer\Tests\Command;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use VStelmakh\Covelyzer\Command\HelpCommand;
use PHPUnit\Framework\TestCase;

class HelpCommandTest extends TestCase
{
    /**
     * @var CommandTester
     */
    private $commandTester;

    public function setUp(): void
    {
        $application = new Application('Covelyzer', '1.0.0');

        $command = new HelpCommand();
        $command->setApplication($application);

        $this->commandTester = new CommandTester($command);
    }

    public function testExecute(): void
    {
        $this->commandTester->execute([]);
        $output = $this->commandTester->getDisplay();
        self::assertStringContainsString($this->getHeader(), $output);
        self::assertStringContainsString('Covelyzer 1.0.0', $output);
    }

    /**
     * @return string
     */
    private function getHeader(): string
    {
        return <<<TXT
                        __                     
  _________ _   _____  / /_  ______  ___  _____
 / ___/ __ \ | / / _ \/ / / / /_  / / _ \/ ___/
/ /__/ /_/ / |/ /  __/ / /_/ / / /_/  __/ /    
\___/\____/|___/\___/_/\__, / /___/\___/_/     
                      /____/                   

TXT;
    }
}
