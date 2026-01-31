<?php
/**
 * Created by PhpStorm.
 * User: karolis
 * Date: 1/2/18
 * Time: 5:38 PM
 */

namespace BestPub\Utils\Services;

use Desarrolla2\Cache\Cache;
use Desarrolla2\Cache\Adapter\File;

class FileCache
{
    /**
     * @var CacheLib|null
     */
    private static $instance;

    /**
     * Set cache value
     *
     * @param $key
     * @param $value
     * @param int $ttl TTL in seconds
     */
    public static function setValue($key, $value, $ttl = 3600)
    {
        $instance = self::getCacheLibInstance();
        if ($instance !== null) {
            $instance->set($key, $value, $ttl);
        }
    }

    private static function getCacheLibInstance()
    {
        if (self::$instance === null) {
            $cacheDir = CACHE_PATH ?: '/tmp';
            $adapter = new File($cacheDir);
            $adapter->setOption('ttl', 3600);
            self::$instance = new Cache($adapter);
        }

        return self::$instance;
    }

    /**
     * Get cache value
     *
     * @param $key
     *
     * @return mixed|null
     */
    public static function getValue($key)
    {
        $instance = self::getCacheLibInstance();
        if ($instance !== null) {
            return $instance->get($key);
        }

        return null;
    }

    /**
     * @param $key
     * @return bool
     */
    public static function has($key)
    {
        $instance = self::getCacheLibInstance();
        if ($instance !== null) {
            return $instance->has($key);
        }

        return false;
    }
}