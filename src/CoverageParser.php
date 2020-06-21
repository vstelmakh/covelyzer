<?php

namespace VStelmakh\Covelyzer;

use VStelmakh\Covelyzer\Entity\Project;
use VStelmakh\Covelyzer\Xml\XpathElement;

class CoverageParser
{
    private const XSD_PATH = __DIR__ . '/../resources/clover-phpunit.xsd';

    /**
     * @param string $filePath
     * @return Project
     */
    public function parseCoverage(string $filePath): Project
    {
        $xml = $this->getFileContents($filePath);
        $domDocument = $this->getDomDocument($xml, self::XSD_PATH);
        $domXpath = new \DOMXPath($domDocument);
        return $this->getProject($domXpath);
    }

    /**
     * @param \DOMXPath $domXpath
     * @return Project
     */
    private function getProject(\DOMXPath $domXpath): Project
    {
        $projectNodeList = $domXpath->query('//project');
        if (!$projectNodeList) {
            throw new \RuntimeException('XML parse error. Project node not found');
        }

        /** @var \DOMElement $projectElement */
        $projectElement = $projectNodeList->item(0);

        $xpathElement = new XpathElement($domXpath, $projectElement);
        return new Project($xpathElement);
    }

    /**
     * @param string $filePath
     * @return string
     */
    private function getFileContents(string $filePath): string
    {
        if (!is_file($filePath)) {
            throw new \InvalidArgumentException(sprintf('File "%s" not exist', $filePath));
        }

        if (!is_readable($filePath)) {
            throw new \RuntimeException(sprintf('File "%s" not readable', $filePath));
        }

        $contents = @file_get_contents($filePath);

        if ($contents === false) {
            throw new \RuntimeException(sprintf('File "%s" get contents error', $filePath));
        }

        return $contents;
    }

    /**
     * @param string $xml
     * @param string|null $xsdFilePath
     * @return \DOMDocument
     */
    private function getDomDocument(string $xml, ?string $xsdFilePath = null): \DOMDocument
    {
        $originalErrorState = \libxml_use_internal_errors(true);

        $domDocument = new \DOMDocument();
        @$domDocument->loadXML($xml, LIBXML_PARSEHUGE);
        if ($xsdFilePath) {
            @$domDocument->schemaValidate($xsdFilePath);
        }

        $errors = \libxml_get_errors();
        if (!empty($errors)) {
            $errorMessage = 'XML parse error' . PHP_EOL;

            foreach ($errors as $error) {
                $errorMessage .= sprintf(' - Line %s, column %s: %s', $error->line, $error->column, $error->message);
            }

            \libxml_clear_errors();
            throw new \RuntimeException($errorMessage);
        }

        \libxml_use_internal_errors($originalErrorState);
        return $domDocument;
    }
}
