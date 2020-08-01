<?php

declare(strict_types=1);

namespace VStelmakh\Covelyzer\Util;

use VStelmakh\Covelyzer\App;

class ConfigLocator
{
    private const PATHS = [
        App::ROOT_DIR . '/../../../covelyzer.xml', // project root config
        App::ROOT_DIR . '/covelyzer.xml', // default config
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
