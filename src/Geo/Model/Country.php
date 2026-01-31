<?php
/**
 * Created by PhpStorm.
 * User: karolis
 * Date: 7/19/18
 * Time: 7:41 PM
 */

namespace BestPub\Geo\Model;


use BestPub\Base\Model;

class Country implements Model
{
    private static $tableName = 'countries';

    public static function getTableName()
    {
        return self::$tableName;
    }
}