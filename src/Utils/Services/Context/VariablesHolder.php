<?php
/**
 * Created by PhpStorm.
 * User: karolis
 * Date: 12/15/17
 * Time: 3:32 PM
 */

namespace BestPub\Utils\Services\Context;


class VariablesHolder
{
    /**
     * @var array
     */
    private static $serverEnv;

    /**
     * Set server environment variables
     *
     * @param array $variables
     */
    public static function setServerEnvironment($variables)
    {
        self::$serverEnv = $variables;
    }

    /**
     * Get server environment variables ($_SERVER data)
     *
     * @return array
     */
    public static function getServerEnvironment()
    {
        return self::$serverEnv;
    }
}