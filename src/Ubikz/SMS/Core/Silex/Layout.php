<?php

namespace Ubikz\SMS\Core\Silex;

use Doctrine\DBAL\Driver\PDOConnection;
use Monolog\Logger;
use Silex\Application;

/**
 * Class Layout.
 */
class Layout
{
    /** @var  Application */
    private static $instance = null;

    /**
     * @return Application
     */
    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new Application();
        }

        return self::$instance;
    }

    /**
     * @param $serviceName
     * @param bool $throw
     *
     * @return mixed
     */
    public static function getService($serviceName, $throw = true)
    {
        $return = null;
        if (!isset(self::getInstance()[$serviceName])) {
            if ($throw) {
                throw new \InvalidArgumentException(sprintf('Service `%s` does not exist.', $serviceName));
            }
        } else {
            $return = self::getInstance()[$serviceName];
        }

        return $return;
    }

    /**
     * @param $serviceName
     * @param $element
     */
    public static function setService($serviceName, $element)
    {
        self::getInstance()[$serviceName] = $element;
    }

    /**
     * @param $serviceName
     */
    public static function unsetService($serviceName)
    {
        if (!isset(self::getInstance()[$serviceName])) {
            throw new \InvalidArgumentException(sprintf('Service `%s` does not exist.', $serviceName));
        }

        unset(self::getInstance()[$serviceName]);
    }

    /**
     * @param $serviceName
     *
     * @return bool
     */
    public static function issetService($serviceName)
    {
        return isset(self::getInstance()[$serviceName]);
    }

    /*
     * Service aliases
     */

    /**
     * @return \Swift_Mailer
     */
    public static function mailer()
    {
        return self::getService('mailer');
    }

    /**
     * @return \Twig_Environment
     */
    public static function template()
    {
        return self::getService('twig');
    }

    /**
     * @return PDOConnection
     */
    public static function database()
    {
        return self::getService('database');
    }

    /**
     * @param null $channel
     *
     * @return Logger
     */
    public static function logger($channel = null)
    {
        return self::getService('monolog'.(is_null($channel) ? '' : '.'.$channel));
    }
}
