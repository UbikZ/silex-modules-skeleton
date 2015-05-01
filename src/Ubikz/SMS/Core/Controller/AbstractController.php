<?php

namespace Exaprint\Preflight\Controller;

use Silex\Controller;
use Ubikz\SMS\Core\Silex\Layout as SilexLayout;

/**
 * Class AbstractController
 * @package Exaprint\Preflight\Controller */
abstract class AbstractController extends Controller
{
    /**
     * @param $url
     */
    public function redirect($url)
    {
        SilexLayout::getInstance()->redirect($url)->send();
    }

    /**
     * @param array $data
     * @param int   $status
     * @param array $headers
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    final public function sendJson($data = array(), $status = 200, array $headers = array())
    {
        return SilexLayout::getInstance()->json($data, $status, $headers);
    }

    /**
     * @param $file
     * @param int   $status
     * @param array $headers
     * @param null  $contentDisposition
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    final public function sendFile($file, $status = 200, array $headers = array(), $contentDisposition = null)
    {
        return SilexLayout::getInstance()->sendFile($file, $status, $headers, $contentDisposition);
    }

    /**
     * Render template from twig engine (with default / specific template).
     *
     * @param $parameters
     * @param null $template
     *
     * @return mixed
     */
    final public function render($parameters = array(), $template = null)
    {
        if (SilexLayout::issetService('twig.currentTmpl')) {
            $template = is_null($template) ? SilexLayout::getService('twig.currentTmpl') : $template;
            SilexLayout::unsetService('twig.currentTmpl');
        }

        return SilexLayout::template()->render($template, $parameters);
    }
}