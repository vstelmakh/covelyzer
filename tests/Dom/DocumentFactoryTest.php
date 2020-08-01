<?php

declare(strict_types=1);

namespace VStelmakh\Covelyzer\Tests\Dom;

use VStelmakh\Covelyzer\Dom\DocumentFactory;
use PHPUnit\Framework\TestCase;

class DocumentFactoryTest extends TestCase
{
    /**
     * @dataProvider createDocumentDataProvider
     *
     * @param string $xml
     * @param string $tag
     * @param string|null $xsdPath
     * @param bool $isException
     */
    public function testCreateDocument(string $xml, string $tag, ?string $xsdPath, bool $isException): void
    {
        if ($isException) {
            $this->expectException(\RuntimeException::class);
        }

        $documentFactory = new DocumentFactory();
        $actual = $documentFactory->createDocument($xml, $xsdPath);

        $expected = new \DOMDocument();
        $tag = $expected->createElement($tag);
        $expected->appendChild($tag);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return array|array[]
     */
    public function createDocumentDataProvider(): array
    {
        return [
            'valid xml, not validate' => [
                '<?xml version="1.0" encoding="UTF-8"?><tag></tag>',
                'tag',
                null,
                false,
            ],
            'invalid xml, not validate' => [
                '<?xml version="1.0" encoding="UTF-8"?><!<tag></tag>',
                'tag',
                null,
                true,
            ],
            'unexpected xml, not validate' => [
                '<?xml version="1.0" encoding="UTF-8"?><another></another>',
                'another',
                null,
                false,
            ],
            'valid xml, validate' => [
                '<?xml version="1.0" encoding="UTF-8"?><tag></tag>',
                'tag',
                __DIR__ . '/DocumentFactoryTest.xsd',
                false,
            ],
            'unexpected xml, validate' => [
                '<?xml version="1.0" encoding="UTF-8"?><another></another>',
                'another',
                __DIR__ . '/DocumentFactoryTest.xsd',
                true,
            ],
        ];
    }
}
