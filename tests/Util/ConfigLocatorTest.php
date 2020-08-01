<?php

declare(strict_types=1);

namespace VStelmakh\Covelyzer\Tests\Util;

use VStelmakh\Covelyzer\Util\ConfigLocator;
use PHPUnit\Framework\TestCase;

class ConfigLocatorTest extends TestCase
{
    public function testGetConfigPath(): void
    {
        $configLocator = new ConfigLocator();
        $expected = realpath(__DIR__ . '/../../covelyzer.xml');
        $actual = $configLocator->getConfigPath();
        self::assertSame($expected, $actual);
    }
}
