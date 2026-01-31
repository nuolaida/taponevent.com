<?php
/**
 * Created by PhpStorm.
 * User: karolis
 * Date: 7/19/18
 * Time: 7:41 PM
 */

namespace BestPub\Geo\Model;


use BestPub\Base\Model;
use BestPub\Core\Model\Translation;
use BestPub\Utils\Services\Config;
use BestPub\Utils\Services\Db\QueryBuilder;

class City implements Model
{
    private static $tableName = 'cities';

    public static function getTableName()
    {
        return self::$tableName;
    }

    /**
     * Get base query for pubs fetch
     *
     * @return QueryBuilder
     */
    private static function getBaseSelectQuery()
    {
        $qb = (new QueryBuilder())->getSelectQuery();
        $qb->setSelectColumns([
            'city_t.text AS name',
            'c.*'
        ])
            ->setFrom(self::getTableName(), 'c')
            ->addLeftJoin(
                Translation::getTableName(),
                'city_t',
                'city_t.table_name = :city_table_name AND city_t.table_id = c.id 
                AND city_t.keyword = :city_name_keyword AND city_t.language = :language'
            )

            ->bindMany([
                'language' => Config::getControllerConfigValue('language', 'lt'),
                'city_table_name' => self::getTableName(),
                'city_name_keyword' => 'title'
            ]);

        return $qb;
    }

    /**
     * Fetch latest pubs data
     *
     * @return array
     */
    public static function getList()
    {
        return self::getBaseSelectQuery()
            ->orderBy(['name ASC'])
            ->fetchAll();
    }
}