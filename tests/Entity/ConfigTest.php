<?php

declare(strict_types=1);

namespace VStelmakh\Covelyzer\Tests\Entity;

use VStelmakh\Covelyzer\Dom\XpathElement;
use VStelmakh\Covelyzer\Entity\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    /**
     * @var \DOMDocument
     */
    private $domDocument;

    /**
     * @var Config
     */
    private $config;

    public function setUp(): void
    {
        $this->domDocument = new \DOMDocument();

        $rootElement = $this->domDocument->createElement('covelyzer');
        $this->domDocument->appendChild($rootElement);

        $projectElement = $this->domDocument->createElement('project');
        $projectElement->setAttribute('minCoverage', '1');
        $rootElement->appendChild($projectElement);

        $classElement = $this->domDocument->createElement('class');
        $classElement->setAttribute('minCoverage', '2');
        $rootElement->appendChild($classElement);

        $domXpath = new \DOMXPath($this->domDocument);
        $xpathElement = new XpathElement($domXpath, $rootElement);

        $this->config = new Config($xpathElement);
    }

    public function testGetMinProjectCoverage(): void
    {
        $actual = $this->config->getMinProjectCoverage();
        self::assertSame(1.0, $actual);
    }

    public function testGetMinClassCoverage(): void
    {
        $actual = $this->config->getMinClassCoverage();
        self::assertSame(2.0, $actual);
    }
}
