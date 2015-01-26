<?php

namespace Ubikz\SMS\Module;

use Igorw\Silex\ConfigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Ubikz\SMS\Exception\InvalidModuleNameException;
use Ubikz\SMS\Helper\Configuration as ConfHelper;
use Ubikz\SMS\Server;

/**
 * Class Module
 * @package Ubikz\SMS\Module
 */
abstract class Module implements IModule
{
    /** @var   string */
    private $moduleName;

    /**
     * @return mixed|void
     * @throws InvalidModuleNameException
     */
    public function init()
    {
        if (is_null($this->getModuleName())) {
            throw new InvalidModuleNameException('A module can\'t be initialized without proper name.');
        }
        $this->initModuleTemplate();
        $this->initModuleConfiguration();
        $this->initModuleRouter();
    }

    /**
     *
     */
    protected function initLogger()
    {
        // implement own module logger
    }

    /**
     * @return mixed
     */
    public function getModuleConfiguration()
    {
        return Server::getService(sprintf('conf.app.%s', strtolower($this->getModuleName())));
    }

    /**
     * @throws InvalidConfigurationException
     */
    protected function initModuleConfiguration()
    {
        Server::getInstance()->register(new ConfigServiceProvider(
            ConfHelper::getModuleFile(sprintf('%s/%s/config/config.yml', __DIR__, $this->getModuleName()))
        ));
    }

    /**
     *
     */
    protected function initModuleTemplate()
    {
        $app = Server::getInstance();
        $viewPath = sprintf('%s/%s/view', __DIR__, $this->getModuleName());

        // We temporally set the view path
        Server::setService('twig.path', $viewPath);

        // We "hack" the dispatcher to "auto" redirect to defined views
        $app->before(function ($request) use ($app, $viewPath) {
            /** @var \Symfony\Component\HttpFoundation\Request $request */
            if (!is_null($route = $request->attributes->get('_controller')) && is_string($route)) {
                $regexp = '/^.+\\\(\w+)Controller::(\w+)Action$/i';
                // Default template
                $twigTmpl = strtolower(preg_replace($regexp, '${1}/${2}.twig', $route));
                if (!empty($twigTmpl) && file_exists(sprintf('%s/%s', $viewPath, $twigTmpl))) {
                    Server::setService('twig.currentTmpl', $twigTmpl);
                }
                // Default stylesheet
                $twigCss = strtolower(preg_replace($regexp, '/css/${1}.css', $route));
                if (!empty($twigCss) && file_exists(sprintf('%s/%s', WEB_PATH, $twigCss))) {
                    Server::setService('twig.currentCss', $twigCss);
                }
            }
        });
    }

    /**
     *  Routes registration from Yaml configuration file (with version management)
     */
    private function initModuleRouter()
    {
        Server::getInstance()->register(
            new ConfigServiceProvider(sprintf('%s/%s/config/routes.yml', __DIR__, $this->getModuleName()))
        );
        $conf = Server::getService(sprintf('conf.routes.%s', strtolower($this->getModuleName())));
        if (is_array($conf)) {
            foreach ($conf as $name => $route) {
                Server::getInstance()->match(
                    sprintf('/%s%s', strtolower($this->getModuleName()), $route['pattern']),
                    sprintf('Ubikz\\SMS\\Module\\%s\\Controller\\%s', $this->getModuleName(), $route['defaults']['_controller'])
                )->bind($name)->method(isset($route['method']) ? $route['method'] : 'GET');
            }
            Server::getInstance()->register(new UrlGeneratorServiceProvider());
        }
    }

    /**
     * @return mixed
     */
    public function getModuleName()
    {
        return ucfirst(strtolower($this->moduleName));
    }

    /**
     * @param mixed $moduleName
     */
    public function setModuleName($moduleName)
    {
        $this->moduleName = $moduleName;
    }
}
