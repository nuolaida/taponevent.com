<?php
/**
 * Created by PhpStorm.
 * User: karolis
 * Date: 12/27/17
 * Time: 9:33 AM
 */

namespace BestPub\Utils\Services;

use Desarrolla2\Cache\Adapter\Memcache;
use Desarrolla2\Cache\Adapter\Memcached;
use Desarrolla2\Cache\Cache as CacheLib;

class Cache
{
    /**
     * @var CacheLib|null
     */
    private static $instance;

    /**
     * @return CacheLib|null
     */
    private static function getCacheLibInstance()
    {
        if (self::$instance === null) {
            if (class_exists('Memcached')) {
                self::$instance = new CacheLib(new Memcached());
            } else if (class_exists('Memcache')) {
                self::$instance = new CacheLib(new Memcache());
            }
        }

        return self::$instance;
    }

    /**
     * Set cache value
     *
     * @param $key
     * @param $value
     * @param int $ttl TTL in seconds
     *
     * @return mixed
     */
    public static function setValue($key, $value, $ttl = 3600)
    {
        $instance = self::getCacheLibInstance();
        if ($instance !== null) {
            $instance->set($key, $value, $ttl);
        }

        return $value;
    }

    /**
     * Get cache value
     *
     * @param $key
     * @param null|mixed $default
     *
     * @return mixed|null
     */
    public static function getValue($key, $default = null)
    {
        $instance = self::getCacheLibInstance();
        if ($instance !== null) {
            return $instance->get($key);
        }

        return $default;
    }

    /**
     * Delete cache key
     *
     * @param $key
     */
    public static function deleteKey($key)
    {
        $instance = self::getCacheLibInstance();
        if ($instance !== null) {
            $instance->delete($key);
        }
    }
}