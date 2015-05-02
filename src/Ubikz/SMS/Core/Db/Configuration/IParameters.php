<?php

namespace Ubikz\SMS\Core\Db\Configuration;

/**
 * Interface IParameters
 * @package Ubikz\SMS\Core\Db\Configuration
 */
interface IParameters
{
    /**
     * @param $key
     * @return mixed
     */
    public function get($key);

    /**
     * @param $key
     * @param $value
     * @return mixed
     */
    public function set($key, $value);
}
