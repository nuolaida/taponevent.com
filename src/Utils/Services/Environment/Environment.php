<?php
/**
 * Created by PhpStorm.
 * User: karolis
 * Date: 12/19/17
 * Time: 9:46 AM
 */

namespace BestPub\Utils\Services\Environment;


use BestPub\Utils\Services\View\Location\Location;

interface Environment
{
    /**
     * Get views hierarchy
     *
     * @return Location[]
     */
    public function getViewsHierarchy();

    /**
     * Get config key for particular value
     *
     * @param $key
     * @return string
     */
    public function getConfigKey($key);
}