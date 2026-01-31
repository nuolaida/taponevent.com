<?php
/**
 * Created by PhpStorm.
 * User: karolis
 * Date: 12/15/17
 * Time: 3:31 PM
 */

namespace BestPub\Utils\Services;


use BestPub\Utils\Services\Context\VariablesHolder;
use BestPub\Utils\Services\Environment\Admin;
use BestPub\Utils\Services\Environment\Environment;
use BestPub\Utils\Services\Environment\Mobile;
use BestPub\Utils\Services\Environment\Web;

class Context
{
    /**
     * @var Environment
     */
    private static $environment;

    /**
     * Get version of app. Development version is enabled for domains ending with `dev` and `localhost`
     *
     * @return bool
     */
    public static function isDevelopmentVersion()
    {
        $server = VariablesHolder::getServerEnvironment();
        preg_match("/.*\.(.*)$/", $server['HTTP_HOST'], $matches);
        if (in_array($matches[1], ['dev', 'localhost'], true)) {
            return true;
        }

        return false;
    }

    /**
     * Check is admin environment used
     *
     * @return bool
     */
    public static function isAdminSide()
    {
        $server = VariablesHolder::getServerEnvironment();
        return $server['SCRIPT_NAME'] === '/admin.php';
    }

    /**
     * Is fist page displayed
     *
     * @return bool
     */
    public static function isFirstPage()
    {
        $requestUri = VariablesHolder::getServerEnvironment()['REQUEST_URI'];
        return $requestUri === '/' || $requestUri === '/index.php';
    }

    /**
     * Init BestPub environment
     *
     * @param string|null $context
     *
     * @return Environment
     */
    public static function initEnvironment($context = null)
    {
        if ($context === 'web' || $context === null) {
            self::$environment = new Web();
        } else if ($context === 'admin') {
            self::$environment = new Admin();
        }

        return self::$environment;
    }

    /**
     * Get current system environment
     *
     * @return Environment
     * @throws \RuntimeException
     */
    public static function getEnvironment()
    {
        if (!self::$environment) {
            return self::initEnvironment();
        }

        return self::$environment;
    }
}