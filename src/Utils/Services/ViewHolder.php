<?php
/**
 * Created by PhpStorm.
 * User: karolis
 * Date: 1/15/18
 * Time: 9:24 AM
 */

namespace BestPub\Utils\Services;


class ViewHolder
{
    /**
     * @var View
     */
    private static $view;

    /**
     * @return View
     */
    public static function getView()
    {
        if (self::$view === null) {
            self::$view = new View();
        }
        return self::$view;
    }

    /**
     * @param View $view
     */
    public static function setView($view)
    {
        self::$view = $view;
    }
}