<?php

namespace Ubikz\SMS\Core;

use Igorw\Silex\ConfigServiceProvider;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\SwiftmailerServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;
use Ubikz\SMS\Core\Db\Configuration\ArrayConfig;
use Ubikz\SMS\Core\Db\Connection;
use Ubikz\SMS\Core\Exception\ErrorSQLStatementException;
use Ubikz\SMS\Core\Exception\InvalidExtensionException;
use Ubikz\SMS\Core\Exception\InvalidFileException;
use Ubikz\SMS\Core\Silex\Layout as SilexLayout;

/**
 * Class Application
 * @package Ubikz\SMS
 */
class Application
{
    /** @var bool  */
    public $debug = false;

    /** @var array  */
    public $conf = [];

    /**
     * @throws InvalidConfigurationException
     * @throws InvalidExtensionException
     */
    public function __construct()
    {
        $this->checkIntegrity();
        $this->registerConfiguration();
    }

    /**
     *
     */
    public function run()
    {
        try {
            $this->registerErrorHandler();
            $this->registerLogger();
            $this->registerRoutes();
            $this->registerDatabase();
            $this->registerMailer();
            $this->registerTemplateEngine();

            SilexLayout::getInstance()->run();
        } catch (ErrorSQLStatementException $e) {
            $this->handleException($e, 'sql');
        } catch (\Exception $e) {
            $this->handleException($e);
        }
    }

    /**
     * We'll check application integrity (extensions loaded etc.)
     */
    private function checkIntegrity()
    {
        // We need to check if pdo_mysql is enabled (no adapter for now so we have to do this)
        if (!extension_loaded('pdo_mysql')) {
            throw new InvalidExtensionException('Install `pdo_mysql` extension before.');
        }
    }

    /***
     * Turn fatal errors into exception.
     */
    private function registerErrorHandler()
    {
        ErrorHandler::register();
        ExceptionHandler::register($this->debug);
    }

    /**
     * @throws InvalidFileException
     */
    private function registerConfiguration()
    {
        // We get configuration file
        if (!file_exists($confFile = CONF_PATH.'/config.yml')) {
            throw new InvalidFileException('Configuration file `'.$confFile.'` not found.');
        }

        // We get parameters file
        if (!file_exists($paramFile = CONF_PATH.'/parameters.yml')) {
            throw new InvalidFileException('Parameters file `'.$paramFile.'` not found.');
        }

        SilexLayout::getInstance()->register(new ConfigServiceProvider($paramFile));
        SilexLayout::getInstance()->register(new ConfigServiceProvider(
            $confFile,
            SilexLayout::getService('parameters'))
        );

        $this->conf = SilexLayout::getService('config.app');
        $confApp = $this->conf['application'];

        // Silex debugging
        SilexLayout::setService('debug', $this->debug = isset($confApp['debug']) ? $confApp['debug'] : false);

        // PHP Settings
        if (isset($conf['php_settings']) && is_array($conf['php_settings'])) {
            foreach ($conf['php_settings'] as $key => $value) {
                if (false === @ini_set($key, $value)) {
                    throw new InvalidConfigurationException('Conf `'.$key.'` => `'.$value.'` does not exist.');
                }
            }
        }
    }

    /**
     * @throws InvalidConfigurationException
     */
    private function registerDatabase()
    {
        if (!isset($this->conf['database'])) {
            throw new InvalidConfigurationException('Configuration for `database` does not exist');
        }

        SilexLayout::setService('database', new Connection(new ArrayConfig($this->conf['database'])));
    }

    /**
     * @throws InvalidConfigurationException
     */
    private function registerMailer()
    {
        if (!isset($this->conf['mailer'])) {
            throw new InvalidConfigurationException('Configuration for `mailer` does not exist');
        }

        $mailerConf = $this->conf['mailer'];
        SilexLayout::getInstance()->register(new SwiftmailerServiceProvider(), [
            'swiftmailer.options' => [
                'host'          =>  $mailerConf['host'],
                'port'          =>  $mailerConf['port'],
                'username'      =>  $mailerConf['username'],
                'password'      =>  $mailerConf['password'],
                'encryption'    =>  $mailerConf['encryption'],
                'auth_mode'     =>  $mailerConf['auth_mode'],
            ],
        ]);
    }

    /**
     * @throws InvalidConfigurationException
     */
    private function registerTemplateEngine()
    {
        $confApp = $this->conf['application'];
        if (!isset($confApp['template'])) {
            throw new InvalidConfigurationException('Configuration for `application.template` does not exist');
        }
        if (!isset($confApp['template']['path'])) {
            throw new InvalidConfigurationException('Configuration for `application.template.path` does not exist');
        }

        // We register the default engine
        $folderName = $confApp['template']['path'];
        $viewPaths = array_map(
            function($n) use ($folderName) { return MODULE_PATH.'/'.ucfirst($n).'/Resources/'.$folderName; },
            isset($confApp['modules']) && is_array($confApp['modules']) ? $confApp['modules'] : []
        );
        SilexLayout::getInstance()->register(new TwigServiceProvider(), ['twig.path' => $viewPaths]);

        // We define twig globals, filters, extensions etc. (if needed)
        $app = SilexLayout::getInstance();
        SilexLayout::setService('twig', $app->share($app->extend('twig', function ($twig, $app) {
            /* @var $twig \Twig_Environment */
            // Gestion de l'i18n
            $twig->addExtension(new \Twig_Extensions_Extension_I18n());
            // Gestion des assets
            $twig->addFunction(new \Twig_SimpleFunction('asset', function ($asset) {
                // We have to use only "dist" directory to find assets
                return sprintf('/static/dist/%s', ltrim($asset, '/'));
            }));

            return $twig;
        })));

        // We "hack" the dispatcher to "auto" redirect to defined views
        $app->before(function ($request) use ($app, $viewPaths) {
            /* @var \Symfony\Component\HttpFoundation\Request $request */
            if (!is_null($route = $request->attributes->get('_controller')) && is_string($route)) {
                $regexp = '/^.+\\\(\w+)Controller::(\w+)Action$/i';
                // Default template
                $twigTmpl = preg_replace($regexp, '${1}/${2}.twig', $route);
                foreach ($viewPaths as $viewPath) {
                    if (!empty($twigTmpl) && file_exists(sprintf('%s/%s', $viewPath, $twigTmpl))) {
                        SilexLayout::setService('twig.currentTmpl', $twigTmpl);
                        break;
                    }
                }
            }
        });
    }

    /**
     *
     */
    private function registerRoutes()
    {
        $confApp = $this->conf['application'];
        $modules = isset($confApp['modules']) && is_array($confApp['modules']) ? $confApp['modules'] : [];

        foreach ($modules as $module) {
            SilexLayout::getInstance()->register(new ConfigServiceProvider(
                MODULE_PATH.'/'.ucfirst($module).'/Resources/config/routes.yml'
            ));
            $conf = SilexLayout::getService('config.routes.'.strtolower($module));

            if (is_array($conf)) {
                foreach ($conf as $name => $route) {
                    SilexLayout::getInstance()->match(
                        $route['pattern'],
                        sprintf('Ubikz\\SMS\\Module\\%s\\Controller\\%s', ucfirst($module), $route['defaults']['_controller'])
                    )->bind($name)->method(isset($route['method']) ? $route['method'] : 'GET');
                }
            }
        }
    }

    /**
     * todo: manage own lvl of log with own channel.
     *
     * @throws InvalidConfigurationException
     */
    public function registerLogger()
    {
        $confApp = $this->conf['application'];
        $modules = isset($confApp['modules']) && is_array($confApp['modules']) ? $confApp['modules'] : [];

        if (!isset($confApp['logger'])) {
            throw new InvalidConfigurationException('Configuration for `application.logger` does not exist');
        }
        if (!isset($confApp['logger']['path'])) {
            throw new InvalidConfigurationException('Configuration for `application.logger.path` does not exist');
        }
        if (!isset($confApp['logger']['channels'])) {
            throw new InvalidConfigurationException('Configuration for `application.logger.channels` does not exist');
        }

        $channels = is_array($confApp['logger']['channels']) ? $confApp['logger']['channels'] : [];
        $logPath = ROOT_PATH.'/'.$confApp['logger']['path'];

        // Default configuration
        SilexLayout::getInstance()->register(new MonologServiceProvider(), [
            'monolog.logfile' => $logPath.'/'.APPLICATION_ENV.'.log',
        ]);

        // Register new channels (specific channels + modules one)
        foreach ($channels + $modules as $channel) {
            SilexLayout::setService('monolog.'.$channel, SilexLayout::getInstance()->share(
                function ($app) use ($logPath, $channel) {
                    /** @var Logger $log */
                    $log = new $app['monolog.logger.class']($channel);
                    $handler = new StreamHandler($logPath.'/'.$channel.'.log');
                    $log->pushHandler($handler);

                    return $log;
                }
            ));
        }
    }

    /**
     * @param $e \Exception
     * @param null $channel
     */
    private function handleException($e, $channel = null)
    {
        if ($this->debug) {
            dump($e);
        }
        SilexLayout::logger($channel)->addError($e->getMessage());
    }
}
