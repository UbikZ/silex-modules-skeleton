<?php

namespace Ubikz\SMS\Core\Db;

use Ubikz\SMS\Core\Exception\InvalidDatabaseAdapterException;

/**
 * Class StaticFactory
 * @package Ubikz\SMS\Db
 */
class StaticFactory
{
    /**
     * @param $adapter
     * @return object
     * @throws InvalidDatabaseAdapterException
     */
    public static function get($adapter)
    {
        $classAdapter = sprintf('%s\\Adapter\\%s', __NAMESPACE__, ucfirst($adapter));
        if (!class_exists($classAdapter)) {
            throw new InvalidDatabaseAdapterException('Database adapter not found.');
        }

        return (new \ReflectionClass($classAdapter))->newInstance();
    }
}
