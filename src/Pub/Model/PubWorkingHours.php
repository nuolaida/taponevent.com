<?php

namespace BestPub\Pub\Model;
use BestPub\Utils\Services\ArrayUtil;
use BestPub\Utils\Services\Db\QueryBuilder;

/**
 * Created by PhpStorm.
 * User: karolis
 * Date: 7/19/18
 * Time: 5:45 PM
 */

class PubWorkingHours implements \BestPub\Base\Model
{
    private static $tableName = 'pubs_working_hours';

    public static function getTableName()
    {
        return self::$tableName;
    }

    /**
     * Get base query for pubs working hours fetch
     *
     * @return \BestPub\Utils\Services\Db\QueryBuilder
     */
    private static function getBaseSelectQuery()
    {
        $qb = (new QueryBuilder())->getSelectQuery();
        $qb->setSelectColumns(['wh.*'])
            ->setFrom(self::$tableName, 'wh');

        return $qb;
    }

    /**
     * Fetch pub working hours
     *
     * @param $pubId
     * @return array
     */
    public static function fetchAll($pubId)
    {
        return ArrayUtil::changeKey(self::getBaseSelectQuery()
            ->addWhere('wh.pub_id = :id')
            ->bindMany(['id' => $pubId])
            ->orderBy(['wh.weekday ASC'])
            ->fetchAll(), 'weekday');
    }

    /**
     * Add or update bulk rows
     *
     * @param array $days
     */
    public static function addOrUpdateBulk(array $days)
    {
        foreach ($days as $day) {
            (new QueryBuilder())->getInsertQuery()
                ->setInsertColumns($day)
                ->setInto(self::$tableName)
                ->onDuplicateUpdate(['pub_id' => $days['pub_id'], 'weekday' => $days['weekday']])
                ->insert();
        }
    }
}