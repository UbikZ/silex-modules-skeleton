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
    public static function getFile($filename)
    {
        $confFilePath = sprintf('%s/%s.%s', CONF_PATH, APPLICATION_ENV, $filename);
        if (!file_exists($confFilePath)) {
            throw new InvalidConfFileException(sprintf('Configuration file `%s` not found.', $confFilePath));
        }

        return $confFilePath;
    }
}
