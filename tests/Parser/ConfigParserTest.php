<?php

declare(strict_types=1);

namespace VStelmakh\Covelyzer\Tests\Parser;

use PHPUnit\Framework\MockObject\MockObject;
use VStelmakh\Covelyzer\Dom\DocumentFactory;
use VStelmakh\Covelyzer\Dom\XpathElement;
use VStelmakh\Covelyzer\Entity\Config;
use VStelmakh\Covelyzer\Parser\ConfigParser;
use PHPUnit\Framework\TestCase;
use VStelmakh\Covelyzer\Util\FileReader;

class ConfigParserTest extends TestCase
{
    /**
     * @dataProvider parseConfigDataProvider
     *
     * @param string|null $attributeName
     * @param mixed $attributeValue
     * @param bool $expectException
     */
    public function testParseConfig(?string $attributeName, $attributeValue, bool $expectException): void
    {
        $filePath = 'path/to/covelyzer.xml';

        $domDocument = new \DOMDocument();
        $rootElement = $domDocument->createElement('covelyzer');
        $domDocument->appendChild($rootElement);
        if ($attributeName !== null) {
            $rootElement->setAttribute($attributeName, $attributeValue);
        }
        $xml = $domDocument->saveXML();

        /** @var FileReader&MockObject $fileReader */
        $fileReader = $this->createMock(FileReader::class);
        $fileReader
            ->expects(self::once())
            ->method('getContents')
            ->with(self::identicalTo($filePath))
            ->willReturn($xml);

        if ($expectException) {
            $this->expectException(\RuntimeException::class);
        }

        $documentFactory = new DocumentFactory();
        $configParser = new ConfigParser($documentFactory, $fileReader);
        $actual = $configParser->parseConfig($filePath);

        $domXpath = new \DOMXPath($domDocument);
        $xpathElement = new XpathElement($domXpath, $rootElement);
        $expected = new Config($xpathElement);

        self::assertEquals($expected, $actual);
    }

    /**
     * @return array[]
     */
    public function parseConfigDataProvider(): array
    {
        return [
            ['minProjectCoverage', '0', false],
            ['minProjectCoverage', '100', false],
            ['minProjectCoverage', '-1', true],
            ['minProjectCoverage', '101', true],
            ['minProjectCoverage', 'string', true],

            ['minClassCoverage', '0', false],
            ['minClassCoverage', '100', false],
            ['minClassCoverage', '-1', true],
            ['minClassCoverage', '101', true],
            ['minClassCoverage', 'string', true],

            [null, null, false],
            ['unknownAttribute', 'string', true],
        ];
    }
}
