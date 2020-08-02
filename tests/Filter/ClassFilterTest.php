<?php

declare(strict_types=1);

namespace VStelmakh\Covelyzer\Tests\Filter;

use VStelmakh\Covelyzer\Dom\XpathElement;
use VStelmakh\Covelyzer\Entity\ClassEntity;
use PHPUnit\Framework\TestCase;
use VStelmakh\Covelyzer\Filter\ClassFilter;

class ClassFilterTest extends TestCase
{
    public function testSetLimit(): void
    {
        $classes = [
            $this->createClass('first', '', 90),
            $this->createClass('second', '', 10),
            $this->createClass('third', '', 25),
        ];

        $classFilter = new ClassFilter($classes);
        $classFilter->setLimit(1);
        $actual = $classFilter->getResult();
        self::assertCount(1, $actual);
        self::assertSame('second', $actual[0]->getName());
    }

    /**
     * @dataProvider setCoverageBelowDataProvider
     *
     * @param float $coverage
     * @param int $expectedCount
     */
    public function testSetCoverageBelow(?float $coverage, int $expectedCount): void
    {
        $classes = [
            $this->createClass('first', '', 90),
            $this->createClass('second', '', 10),
            $this->createClass('third', '', 25),
        ];

        $classFilter = new ClassFilter($classes);
        $classFilter->setCoverageBelow($coverage);
        $actual = $classFilter->getResult();
        self::assertCount($expectedCount, $actual);
    }

    /**
     * @return array&array[]
     */
    public function setCoverageBelowDataProvider(): array
    {
        return [
            [90, 2],
            [91, 3],
            [25, 1],
            [26, 2],
            [0, 0],
            [null, 3],
        ];
    }

    public function testGetResult(): void
    {
        $classes = [
            $this->createClass('name1', '', 90),
            $this->createClass('name2', '', 10),
            $this->createClass('name4', '', 25),
            $this->createClass('name3', '', 25),
        ];

        $classFilter = new ClassFilter($classes);
        $actual = $classFilter->getResult();
        self::assertSame('name2', $actual[0]->getName());
        self::assertSame('name3', $actual[1]->getName());
        self::assertSame('name4', $actual[2]->getName());
        self::assertSame('name1', $actual[3]->getName());
    }

    /**
     * @param string $name
     * @param string $namespace
     * @param int $coverage
     * @return ClassEntity
     */
    private function createClass(string $name, string $namespace, int $coverage): ClassEntity
    {
        $domDocument = new \DOMDocument();

        $class = $domDocument->createElement('class');
        $class->setAttribute('name', $name);
        $class->setAttribute('namespace', $namespace);
        $domDocument->appendChild($class);

        $metrics = $domDocument->createElement('metrics');
        $metrics->setAttribute('elements', '100');
        $metrics->setAttribute('coveredelements', (string) $coverage);
        $class->appendChild($metrics);

        $domXpath = new \DOMXPath($domDocument);
        $xpathElement = new XpathElement($domXpath, $class);

        return new ClassEntity($xpathElement);
    }
}
