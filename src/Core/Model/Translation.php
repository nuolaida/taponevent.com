<?php
/**
 * Created by PhpStorm.
 * User: karolis
 * Date: 7/19/18
 * Time: 7:23 PM
 */

namespace BestPub\Core\Model;


use BestPub\Base\Model;

class Translation implements Model
{
    private static $tableName = 'languages_varchar';

    public static function getTableName()
    {
        return self::$tableName;
    }
}