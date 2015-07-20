<?php

namespace Ubikz\SMS\Core\Db\Configuration;

/**
 * Class AbstractConfig
 * @package Ubikz\SMS\Core\Db\Configuration
 */
abstract class AbstractConfig
{
    /** @var   */
    protected $storage;

    public function __construct($storage)
    {
        $this->storage = $storage;
    }

    /**
     * @return mixed
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * @param mixed $storage
     */
    public function setStorage($storage)
    {
        $this->storage = $storage;
    }

    /**
     * @param $value
     * @param null $key
     */
    public function addStorageParam($value, $key = null)
    {
        if (is_null($key)) {
            $this->storage[] = $value;
        } else {
            $this->storage[$key] = $value;
        }
    }
}
