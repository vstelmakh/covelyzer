<?php

declare(strict_types=1);

namespace VStelmakh\Covelyzer\Tests\Util;

use VStelmakh\Covelyzer\Util\FileReader;
use PHPUnit\Framework\TestCase;

class FileReaderTest extends TestCase
{
    /**
     * @dataProvider getContentsDataProvider
     */
    public function testGetContents(string $filePath, bool $expectException, string $expected): void
    {
        $fileReader = new FileReader();
        if ($expectException) {
            $this->expectException(\InvalidArgumentException::class);
        }
        $actual = $fileReader->getContents($filePath);
        $this->assertSame($expected, $actual);
    }

    /**
     * @return array|array[]
     */
    public function getContentsDataProvider(): array
    {
        return [
            [__DIR__ . '/nonexistent_file', true, ''],
            [__DIR__ . '/FileReaderTest.txt', false, "Test content\n"],
        ];
    }
}
