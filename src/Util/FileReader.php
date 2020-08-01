<?php

declare(strict_types=1);

namespace VStelmakh\Covelyzer\Util;

class FileReader
{
    /**
     * @param string $filePath
     * @return string
     */
    public function getContents(string $filePath): string
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
}
