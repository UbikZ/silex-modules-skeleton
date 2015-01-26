<?php

namespace Ubikz\SMS\Module;

/**
 * Interface IModule
 * @package Ubikz\SMS\Module
 */
interface IModule
{
    /**
     * Initialize the selected module
     * @return mixed
     */
    public function init();
}
