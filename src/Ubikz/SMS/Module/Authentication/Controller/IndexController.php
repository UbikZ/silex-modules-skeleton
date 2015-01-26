<?php

namespace Ubikz\SMS\Module\Authentication\Controller;

use Ubikz\SMS\Controller\AbstractController;

/**
 * Class IndexController
 * @package Ubikz\SMS\Module\Authentication\Controller
 */
class IndexController extends AbstractController
{
    public function indexAction()
    {
        return $this->render(['test' => 'prout']);
    }
}
