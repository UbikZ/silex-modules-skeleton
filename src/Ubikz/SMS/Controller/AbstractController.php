<?php

namespace Ubikz\SMS\Controller;

use Ubikz\SMS\Server;

/**
 * Class AbstractController
 * @package Ubikz\SMS\Controller
 */
abstract class AbstractController
{
    /**
     * @param $url
     */
    public function redirect($url)
    {
        Server::getInstance()->redirect($url)->send();
    }

    /**
     * Render template from twig engine (with default / specific template)
     * @param $parameters
     * @param null $template
     * @return mixed
     */
    final public function render($parameters = array(), $template = null)
    {
        if (Server::issetService('twig.currentTmpl')) {
            $template = is_null($template) ? Server::getService('twig.currentTmpl') : $template;
            Server::unsetService('twig.currentTmpl');
        }
        if (Server::issetService('twig.currentCss')) {
            $parameters = array_merge($parameters, ['currentCss' => Server::getService('twig.currentCss')]);
            Server::unsetService('twig.currentCss');
        }

        return Server::getService('twig')->render($template, $parameters);
    }
}
