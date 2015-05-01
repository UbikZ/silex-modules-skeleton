<?php

namespace Ubikz\SMS\Code\Db\Configuration;

/**
 * Interface IParameters
 * @package Ubikz\SMS\Code\Db\Configuration
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