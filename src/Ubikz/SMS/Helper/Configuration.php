<?php

namespace Ubikz\SMS\Helper;

use Ubikz\SMS\Exception\InvalidConfFileException;

/**
 * Class Configuration
 * @package Ubikz\SMS\Helper
 */
class Configuration
{
    /**
     * @param $filename
     * @return string
     * @throws InvalidConfFileException
     */
    public static function getMainFile($filename)
    {
        $confFilePath = sprintf('%s/%s.%s', CONF_PATH, APPLICATION_ENV, $filename);

        if (!file_exists($confFilePath)) {
            throw new InvalidConfFileException(sprintf('Configuration main file `%s` not found.', $confFilePath));
        }

        return $confFilePath;
    }

    /**
     * @param $filepath
     * @return mixed
     * @throws InvalidConfFileException
     */
    public static function getModuleFile($filepath)
    {
        if (!file_exists($filepath)) {
            throw new InvalidConfFileException(sprintf('Configuration module file `%s` not found.', $filepath));
        }

        return $filepath;
    }
}
