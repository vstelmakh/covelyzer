<?php

namespace VStelmakh\Covelyzer\Dom;

class DocumentFactory
{
    /**
     * Create DOMDocument. Throw exception on error.
     *
     * @param string $xml
     * @param string|null $xsdFilePath
     * @return \DOMDocument
     */
    public function createDocument(string $xml, ?string $xsdFilePath = null): \DOMDocument
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
