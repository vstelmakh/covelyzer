<?php

declare(strict_types=1);

namespace VStelmakh\Covelyzer\Tests;

use PHPUnit\Framework\TestCase;
use VStelmakh\Covelyzer\CoverageParser;
use VStelmakh\Covelyzer\Entity\Project;
use VStelmakh\Covelyzer\Util\FileReader;
use VStelmakh\Covelyzer\Dom\DocumentFactory;
use VStelmakh\Covelyzer\Dom\XpathElement;

class CoverageParserTest extends TestCase
{
    /**
     * @dataProvider parseCoverageDataProvider
     *
     * @param string $tagName
     * @param string|null $timestamp
     */
    public function testParseCoverage(string $tagName, ?string $timestamp): void
    {
        $filePath = 'path/to/coverage.xml';

        $fileReader = $this->createMock(FileReader::class);
        $fileReader
            ->expects($this->once())
            ->method('getContents')
            ->with($this->identicalTo($filePath));

        $domDocument = new \DOMDocument();

        $rootElement = $domDocument->createElement('coverage');
        $rootElement->setAttribute('generated', $timestamp ?? '');
        $domDocument->appendChild($rootElement);

        $element = $domDocument->createElement($tagName);
        $element->setAttribute('timestamp', $timestamp ?? '');
        $rootElement->appendChild($element);

        $documentFactory = $this->createMock(DocumentFactory::class);
        $documentFactory
            ->expects($this->once())
            ->method('createDocument')
            ->willReturn($domDocument);

        if ($timestamp === null) {
            $this->expectException(\RuntimeException::class);
        }

        $coverageParser = new CoverageParser($documentFactory, $fileReader);
        $actual = $coverageParser->parseCoverage($filePath);

        $domXpath = new \DOMXPath($domDocument);
        $xpathElement = new XpathElement($domXpath, $element);
        $expected = new Project($xpathElement);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return array[]
     */
    public function parseCoverageDataProvider(): array
    {
        return [
            [
                'project',
                '1590967053',
            ],
            [
                'something_else',
                null,
            ],
        ];
    }
}
