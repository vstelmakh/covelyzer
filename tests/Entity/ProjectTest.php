<?php

declare(strict_types=1);

namespace VStelmakh\Covelyzer\Tests\Entity;

use VStelmakh\Covelyzer\Entity\ClassEntity;
use VStelmakh\Covelyzer\Entity\File;
use VStelmakh\Covelyzer\Entity\Project;
use PHPUnit\Framework\TestCase;
use VStelmakh\Covelyzer\Entity\ProjectMetrics;
use VStelmakh\Covelyzer\Dom\XpathElement;

class ProjectTest extends TestCase
{
    private const TIMESTAMP = '1590967053';

    /**
     * @var \DOMDocument
     */
    private $domDocument;

    /**
     * @var Project
     */
    private $project;

    public function setUp(): void
    {
        $this->domDocument = new \DOMDocument();

        $project = $this->domDocument->createElement('project');
        $project->setAttribute('timestamp', self::TIMESTAMP);
        $this->domDocument->appendChild($project);

        $file = $this->domDocument->createElement('file');
        $project->appendChild($file);

        $class = $this->domDocument->createElement('class');
        $file->appendChild($class);

        $metrics = $this->domDocument->createElement('metrics');
        $project->appendChild($metrics);

        $domXpath = new \DOMXPath($this->domDocument);
        $xpathElement = new XpathElement($domXpath, $project);

        $this->project = new Project($xpathElement);
    }

    /**
     * @dataProvider getTimestampDataProvider
     *
     * @param string|null $timezone
     */
    public function testGetTimestamp(?string $timezone): void
    {
        /** @var \DateTime $expected */
        $expected = \DateTime::createFromFormat('U', self::TIMESTAMP);
        if ($timezone !== null) {
            $expected->setTimezone(new \DateTimeZone($timezone));
        }

        /** @var \DateTime $actual */
        $actual = $this->project->getTimestamp($timezone);

        $format = 'Y-m-d H:i:s T';
        self::assertSame(
            $expected->format($format),
            $actual->format($format)
        );
    }

    /**
     * @return array&array[]
     */
    public function getTimestampDataProvider(): array
    {
        return [
            [null],
            ['GMT+3'],
            ['Europe/Berlin'],
        ];
    }

    public function testGetMetrics(): void
    {
        $actual = $this->project->getMetrics();

        /** @var \DOMElement $metricsNode */
        $metricsNode = $this->domDocument->getElementsByTagName('metrics')->item(0);
        $metricsXpathElement = $this->project->getXpathElement()->createElement($metricsNode);
        $expected = new ProjectMetrics($metricsXpathElement);

        self::assertEquals($expected, $actual);
    }

    public function testGetFiles(): void
    {
        $actual = $this->project->getFiles();

        /** @var \DOMElement $fileNode */
        $fileNode = $this->domDocument->getElementsByTagName('file')->item(0);
        $fileXpathElement = $this->project->getXpathElement()->createElement($fileNode);
        $file = new File($fileXpathElement);
        $expected = [$file];

        self::assertEquals($expected, iterator_to_array($actual));
    }

    public function testGetClasses(): void
    {
        $actual = $this->project->getClasses();

        /** @var \DOMElement $classNode */
        $classNode = $this->domDocument->getElementsByTagName('class')->item(0);
        $classXpathElement = $this->project->getXpathElement()->createElement($classNode);
        $class = new ClassEntity($classXpathElement);
        $expected = [$class];

        self::assertEquals($expected, iterator_to_array($actual));
    }
}
