<?php

declare(strict_types=1);

namespace VStelmakh\Covelyzer;

use VStelmakh\Covelyzer\Entity\Project;
use VStelmakh\Covelyzer\Util\FileReader;
use VStelmakh\Covelyzer\Dom\DocumentFactory;
use VStelmakh\Covelyzer\Dom\XpathElement;

class CoverageParser
{
    private const XSD_PATH = __DIR__ . '/../resources/clover-phpunit.xsd';

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
     * @return Project
     */
    public function parseCoverage(string $filePath): Project
    {
        $xml = $this->fileReader->getContents($filePath);
        $domDocument = $this->documentFactory->createDocument($xml, self::XSD_PATH);
        $domXpath = new \DOMXPath($domDocument);
        return $this->getProject($domXpath);
    }

    /**
     * @param \DOMXPath $domXpath
     * @return Project
     */
    private function getProject(\DOMXPath $domXpath): Project
    {
        /** @var \DOMNodeList<\DOMElement> $projectNodeList */
        $projectNodeList = $domXpath->query('//project');
        if ($projectNodeList->item(0) === null) {
            throw new \RuntimeException('XML parse error. Project node not found');
        }

        /** @var \DOMElement $projectElement */
        $projectElement = $projectNodeList->item(0);

        $xpathElement = new XpathElement($domXpath, $projectElement);
        return new Project($xpathElement);
    }
}
