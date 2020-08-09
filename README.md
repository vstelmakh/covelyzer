# Covelyzer

[![Build status](https://github.com/vstelmakh/covelyzer/workflows/build/badge.svg?branch=master)](https://github.com/vstelmakh/covelyzer/actions)
[![Packagist version](https://img.shields.io/packagist/v/vstelmakh/covelyzer?color=orange)](https://packagist.org/packages/vstelmakh/covelyzer)
[![PHP version](https://img.shields.io/packagist/php-v/vstelmakh/covelyzer)](https://www.php.net/)
[![License](https://img.shields.io/github/license/vstelmakh/covelyzer?color=yellowgreen)](LICENSE)

**Covelyzer** - PHP console tool to analyze PHPUnit test coverage report in clover (XML) format. 
Covelyzer integrates coverage metrics directly into the workflow forcing developers to write tests and 
forbidding delivery of uncovered code.

Highlights:
- Works in console
- Easy to integrate
- Flexible configuration
- Same setup locally and on CI
- Not depend on third-party service

## Installation
Install the latest version with [Composer](https://getcomposer.org/):  
```bash
composer require --dev vstelmakh/covelyzer
```

## Usage
Before you start, run tests with options to generate coverage report in XML format.
See corresponding PHPUnit documentation reference [Code coverage analysis](https://phpunit.readthedocs.io/en/9.2/code-coverage-analysis.html)
and Covelyzer [phpunit.xml](./phpunit.xml), [composer.json](./composer.json) scripts section as an example.

Run tests with XML coverage xdebug example:  
```bash
vendor/bin/phpunit --dump-xdebug-filter var/xdebug-filter.php && \
vendor/bin/phpunit --prepend var/xdebug-filter.php --coverage-clover var/coverage.xml --whitelist src
```

Run Covelyzer with [default configuration](./covelyzer.xml):  
```bash
vendor/bin/covelyzer var/coverage.xml
```

To specify additional configuration see [Configuration](#configuration).

## Configuration
Configuration defined in `covelyzer.xml` located in project root (where vendor dir located).  
If no configuration file provided - [default configuration](./covelyzer.xml) is used.  

Example configuration:
```xml
<?xml version="1.0" encoding="UTF-8" ?>
<covelyzer
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:noNamespaceSchemaLocation="vendor/vstelmakh/covelyzer/resources/covelyzer-config.xsd"
>
    <project minCoverage="100"/>
    <class minCoverage="100"/>
</covelyzer>
```

#### `<project>` element
Defines configuration for project coverage report. If defined project coverage report will be rendered.  
- use: `optional`

##### `minCoverage` attribute
Minimum coverage value. If project have less coverage - report will fail.  
- use: `required`
- type: `float` (min: 0, max: 100)

#### `<class>` element
Defines configuration for class coverage report. If defined class coverage report will be rendered.  
- use: `optional`

##### `minCoverage` attribute
Minimum coverage value. If any class have less coverage - report will fail.  
- use: `required`
- type: `float` (min: 0, max: 100)  

## Tips & tricks
### Ignore code blocks
Covelyzer will analyze full report produced by PHPUnit. In some cases you want to ignore some code parts to not be taken into account.
There are several options available, see [Ignoring code blocks](https://phpunit.readthedocs.io/en/9.2/code-coverage-analysis.html#ignoring-code-blocks)
in PHPUnit documentation.

### Speed up coverage with Xdebug
The performance of code coverage data collection with Xdebug can be improved by delegating whitelist filtering to Xdebug.
See corresponding PHPUnit docs section: [Speeding up code coverage with Xdebug](https://phpunit.readthedocs.io/en/9.2/code-coverage-analysis.html#speeding-up-code-coverage-with-xdebug).

## Credits
[Volodymyr Stelmakh](https://github.com/vstelmakh)  
Licensed under the MIT License. See [LICENSE](LICENSE) for more information.  
