<?php

declare(strict_types=1);

namespace VStelmakh\Covelyzer\Parser;

use VStelmakh\Covelyzer\Dom\DocumentFactory;
use VStelmakh\Covelyzer\Dom\XpathElement;
use VStelmakh\Covelyzer\Entity\Config;
use VStelmakh\Covelyzer\Util\FileReader;

class ConfigParser
{
    // TODO: add xsd
    private const XSD_PATH = null;

    /**
     * @var DocumentFactory
     */
    private $documentFactory;

    /**
     * @var FileReader
     */
    private $fileReader;

    /**
     * @param DocumentFactory $documentFactory
     * @param FileReader $fileReader
     */
    public function __construct(DocumentFactory $documentFactory, FileReader $fileReader)
    {
        $this->documentFactory = $documentFactory;
        $this->fileReader = $fileReader;
    }

    /**
     * @param string $filePath
     * @return Config
     */
    public function parseConfig(string $filePath): Config
    {
        $xml = $this->fileReader->getContents($filePath);
        $domDocument = $this->documentFactory->createDocument($xml, self::XSD_PATH);
        $domXpath = new \DOMXPath($domDocument);
        return $this->getConfig($domXpath);
    }

    /**
     * @param \DOMXPath $domXpath
     * @return Config
     */
    private function getConfig(\DOMXPath $domXpath): Config
    {
        /** @var \DOMNodeList<\DOMElement> $covelyzerNodeList */
        $covelyzerNodeList = $domXpath->query('//covelyzer');

        /** @var \DOMElement|null $covelyzerElement */
        $covelyzerElement = $covelyzerNodeList->item(0);

        if ($covelyzerElement === null) {
            throw new \RuntimeException('XML parse error. Covelyzer node not found');
        }

        $xpathElement = new XpathElement($domXpath, $covelyzerElement);
        return new Config($xpathElement);
    }
}
