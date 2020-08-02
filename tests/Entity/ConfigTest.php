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
     * @var \DOMElement
     */
    private $rootElement;

    /**
     * @var Config
     */
    private $config;

    public function setUp(): void
    {
        $this->domDocument = new \DOMDocument();

        $this->rootElement = $this->domDocument->createElement('covelyzer');
        $this->domDocument->appendChild($this->rootElement);

        $domXpath = new \DOMXPath($this->domDocument);
        $xpathElement = new XpathElement($domXpath, $this->rootElement);

        $this->config = new Config($xpathElement);
    }

    /**
     * @dataProvider getMinCoverageDataProvider
     *
     * @param bool $isElementExist
     * @param float|null $minCoverage
     */
    public function testGetMinProjectCoverage(bool $isElementExist, ?float $minCoverage): void
    {
        if ($isElementExist) {
            $projectElement = $this->domDocument->createElement('project');
            $projectElement->setAttribute('minCoverage', (string) $minCoverage);
            $this->rootElement->appendChild($projectElement);
        }

        $actual = $this->config->getMinProjectCoverage();
        self::assertSame($minCoverage, $actual);
    }

    /**
     * @dataProvider getMinCoverageDataProvider
     *
     * @param bool $isElementExist
     * @param float|null $minCoverage
     */
    public function testGetMinClassCoverage(bool $isElementExist, ?float $minCoverage): void
    {
        if ($isElementExist) {
            $projectElement = $this->domDocument->createElement('class');
            $projectElement->setAttribute('minCoverage', (string) $minCoverage);
            $this->rootElement->appendChild($projectElement);
        }

        $actual = $this->config->getMinClassCoverage();
        self::assertSame($minCoverage, $actual);
    }

    /**
     * @return array[]
     */
    public function getMinCoverageDataProvider(): array
    {
        return [
            [true, 100],
            [false, null],
        ];
    }
}
