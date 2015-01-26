<?php

namespace Ubikz\SMS\Db;

use Ubikz\SMS\Exception\InvalidDatabaseAdapterException;

/**
 * Class DbFactory
 * @package Ubikz\SMS\Db
 */
class DbFactory
{
    /**
     * @param $adapter
     * @param $connection
     * @return object
     * @throws InvalidDatabaseAdapterException
     */
    public static function get($adapter, $connection)
    {
        $classAdapter = sprintf('\\Ubikz\\SMS\\Db\\Adapter\\%s', ucfirst($adapter));
        if (!class_exists($classAdapter)) {
            throw new InvalidDatabaseAdapterException('Database adapter not found.');
        }

        return (new \ReflectionClass($classAdapter))->newInstance($connection);
    }
}
