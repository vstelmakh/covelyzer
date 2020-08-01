<?php

declare(strict_types=1);

namespace VStelmakh\Covelyzer\Util;

class ConfigLocator
{
    private const PATHS = [
        __DIR__ . '/../../../../../covelyzer.xml', // project root config
        __DIR__ . '/../../covelyzer.xml', // default config
    ];

    /**
     * @return string
     */
    public function getConfigPath(): string
    {
        foreach (self::PATHS as $path) {
            $realPath =  realpath($path);

            if ($realPath !== false) {
                return $realPath;
            }
        }

        throw new \RuntimeException('Unable to locate config');
    }
}
