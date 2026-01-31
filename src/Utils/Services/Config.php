<?php
/**
 * Created by PhpStorm.
 * User: karolis
 * Date: 12/5/17
 * Time: 1:26 PM
 */

namespace BestPub\Utils\Services;


if (extension_loaded('newrelic')) {
    newrelic_add_custom_tracer('BestPub\\Utils\\Services\\Config::getDomainConfig');
    newrelic_add_custom_tracer('BestPub\\Utils\\Services\\Config::setDomainConfig');
    newrelic_add_custom_tracer('BestPub\\Utils\\Services\\Config::getDomainConfigValue');
    newrelic_add_custom_tracer('BestPub\\Utils\\Services\\Config::hasDomainConfigKey');
}

class Config
{
    private static $config;

    private static $domainConfig;

    private static $controllerConfig;

    private static $controllerConfigValues = [];

    private static $modulesConfig = [];

    /**
     * @return mixed
     */
    public static function getConfig()
    {
        return self::$config;
    }

    /**
     * @param array $config
     */
    public static function setConfig($config)
    {
        self::$config = $config;
    }

    /**
     * Get config value
     *
     * @param $key
     * @param null $default
     * @return mixed
     */
    public static function getConfigValue($key, $default = null)
    {
        if (isset(self::$config[$key])) {
            return self::$config[$key];
        }

        return $default;
    }

    /**
     * @return mixed
     */
    public static function getDomainConfig()
    {
        return self::$domainConfig;
    }

    /**
     * @param array $config
     */
    public static function setDomainConfig($config)
    {
        self::$domainConfig = $config;
    }

    /**
     * @param $key
     * @param null $default
     * @return mixed
     */
    public static function getDomainConfigValue($key, $default = null)
    {
        if (isset(self::$domainConfig[$key])) {
            return self::$domainConfig[$key];
        }

        return $default;
    }

    /**
     * Get domain keyword
     *
     * @return string
     */
    public static function getKeyword()
    {
        return self::getDomainConfigValue('keyword');
    }

    /**
     * @param $key
     * @return mixed
     */
    public static function hasDomainConfigKey($key)
    {
        return isset(self::$domainConfig[$key]);
    }

    /**
     * Set controller config
     *
     * @param $config
     *
     */
    public static function setControllerConfig($config)
    {
        self::$controllerConfig = $config;
    }

    /**
     * Get config value from controller config
     *
     * @param $key
     * @param null $defaultValue
     * @return mixed
     */
    public static function getControllerConfigValue($key, $defaultValue = null)
    {
        if (isset(self::$controllerConfigValues[$key])) {
            return self::$controllerConfigValues[$key];
        }

        if (isset(self::$controllerConfig[$key])) {
            return self::$controllerConfig[$key];
        }

        return $defaultValue;
    }

    /**
     * Set value for controller config
     *
     * @param $key
     * @param $value
     */
    public static function setControllerConfigValue($key, $value) {
        self::$controllerConfigValues[$key] = $value;
    }

    /**
     * @param $key
     * @return mixed
     */
    public static function hasControllerConfigKey($key)
    {
        if (isset(self::$controllerConfigValues[$key])) {
            return true;
        }

        if (isset(self::$controllerConfig[$key])) {
            return true;
        }

        return false;
    }

    /**
     * Get module config
     *
     * @param string $module
     * @return array
     */
    public static function getModuleConfig($module)
    {
        if (!isset(self::$modulesConfig[$module])) {
            self::$modulesConfig[$module] = [];
            $path = self::getDomainConfigValue('site_config_path') . 'configs/' . $module . '.config.php';
            if (file_exists($path)) {
                include $path;
                /** @noinspection PhpUndefinedVariableInspection */
                self::$modulesConfig[$module] = $config;
            }
        }

        return self::$modulesConfig[$module];
    }


    /**
     * @param $module
     * @param $key
     * @param null $default
     * @return mixed
     */
    public static function getModuleConfigValue($module, $key, $default = null)
    {
        if (!isset(self::$modulesConfig[$module])) {
            self::getModuleConfig($module);
        }

        if (isset(self::$modulesConfig[$module][$key])) {
            return self::$modulesConfig[$module][$key];
        }

        return $default;
    }
}