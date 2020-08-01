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
    private const NAME = 'name';
    private const ATTRIBUTES = 'attributes';

    /**
     * @dataProvider parseConfigDataProvider
     *
     * @param array&array[] $nodes
     * @param bool $expectException
     */
    public function testParseConfig(array $nodes, bool $expectException): void
    {
        $filePath = 'path/to/covelyzer.xml';

        $domDocument = new \DOMDocument();
        $rootElement = $domDocument->createElement('covelyzer');
        $domDocument->appendChild($rootElement);

        foreach ($nodes as $node) {
            $element = $domDocument->createElement($node[self::NAME]);
            $rootElement->appendChild($element);
            foreach ($node[self::ATTRIBUTES] as $attribute => $value) {
                $element->setAttribute($attribute, $value);
            }
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
            'no nodes' => [
                [],
                false
            ],
            'unknown node' => [
                [
                    [
                        self::NAME => 'unknown',
                        self::ATTRIBUTES => [],
                    ],
                ],
                true
            ],
            'unknown attribute' => [
                [
                    [
                        self::NAME => 'project',
                        self::ATTRIBUTES => ['unknown' => '100'],
                    ],
                ],
                true
            ],

            'project only' => [
                [
                    [
                        self::NAME => 'project',
                        self::ATTRIBUTES => ['minCoverage' => '100'],
                    ],
                ],
                false
            ],
            'class only' => [
                [
                    [
                        self::NAME => 'class',
                        self::ATTRIBUTES => ['minCoverage' => '100'],
                    ],
                ],
                false
            ],
            'project and class' => [
                [
                    [
                        self::NAME => 'project',
                        self::ATTRIBUTES => ['minCoverage' => '100'],
                    ],
                    [
                        self::NAME => 'class',
                        self::ATTRIBUTES => ['minCoverage' => '100'],
                    ],
                ],
                false
            ],
            'project and project' => [
                [
                    [
                        self::NAME => 'project',
                        self::ATTRIBUTES => ['minCoverage' => '100'],
                    ],
                    [
                        self::NAME => 'project',
                        self::ATTRIBUTES => ['minCoverage' => '100'],
                    ],
                ],
                true
            ],
            'class and class' => [
                [
                    [
                        self::NAME => 'class',
                        self::ATTRIBUTES => ['minCoverage' => '100'],
                    ],
                    [
                        self::NAME => 'class',
                        self::ATTRIBUTES => ['minCoverage' => '100'],
                    ],
                ],
                true
            ],

            'project minCoverage -1' => [
                [
                    [
                        self::NAME => 'project',
                        self::ATTRIBUTES => ['minCoverage' => '-1'],
                    ],
                ],
                true
            ],
            'project minCoverage 101' => [
                [
                    [
                        self::NAME => 'project',
                        self::ATTRIBUTES => ['minCoverage' => '101'],
                    ],
                ],
                true
            ],
            'project minCoverage string' => [
                [
                    [
                        self::NAME => 'project',
                        self::ATTRIBUTES => ['minCoverage' => 'string'],
                    ],
                ],
                true
            ],

            'class minCoverage -1' => [
                [
                    [
                        self::NAME => 'class',
                        self::ATTRIBUTES => ['minCoverage' => '-1'],
                    ],
                ],
                true
            ],
            'class minCoverage 101' => [
                [
                    [
                        self::NAME => 'class',
                        self::ATTRIBUTES => ['minCoverage' => '101'],
                    ],
                ],
                true
            ],
            'class minCoverage string' => [
                [
                    [
                        self::NAME => 'class',
                        self::ATTRIBUTES => ['minCoverage' => 'string'],
                    ],
                ],
                true
            ],
        ];
    }
}
