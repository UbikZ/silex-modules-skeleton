<?php

namespace Ubikz\SMS\Module\App\Controller;

use Ubikz\SMS\Core\Controller\AbstractController;

/**
 * Class IndexController
 * @package Ubikz\SMS\Module\App\Controller
 */
class IndexController extends AbstractController
{
    /**
     *
     */
    public function indexAction()
    {
        return $this->render();
    }
}