<?php
/**
 * Created by PhpStorm.
 * User: karolis
 * Date: 7/19/18
 * Time: 7:45 PM
 */

namespace BestPub\Core\Model;


use BestPub\Base\Model;

class TranslationText implements Model
{
    private static $tableName = 'languages_text';

    public static function getTableName()
    {
        return self::$tableName;
    }
}