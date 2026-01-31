<?php

namespace BestPub\Pub\Model;
use BestPub\Core\Model\Translation;
use BestPub\Core\Model\TranslationText;
use BestPub\Geo\Model\City;
use BestPub\Geo\Model\Country;
use BestPub\Utils\Services\Config;
use BestPub\Utils\Services\Db\QueryBuilder;

/**
 * Created by PhpStorm.
 * User: karolis
 * Date: 7/19/18
 * Time: 5:45 PM
 */

class Pub implements \BestPub\Base\Model
{
    private static $tableName = 'pubs';

    public static function getTableName()
    {
        return self::$tableName;
    }

    /**
     * Get base query for pubs fetch
     *
     * @param null $columns
     * @return \BestPub\Utils\Services\Db\QueryBuilder
     */
    private static function getBaseSelectQuery($columns = null)
    {
        if ($columns === null) {
            $columns = [
                'city_t.text AS city',
                'country_t.text AS country',
                'address_t.text AS address',
                'name_t.text AS name',
                'description_t.text AS description',
                'short_description_t.text AS short_description',
                'p.*'
            ];
        }

        $qb = (new \BestPub\Utils\Services\Db\QueryBuilder())->getSelectQuery();
        $qb->setSelectColumns($columns)
            ->setFrom(self::$tableName, 'p')
            ->addLeftJoin(City::getTableName(),'city','city.id = p.city_id')
            ->addLeftJoin(
                Translation::getTableName(),
                'city_t',
                'city_t.table_name = :city_table_name AND city_t.table_id = p.city_id 
                AND city_t.keyword = :city_name_keyword AND city_t.language = :language'
            )
            ->addLeftJoin(
                Translation::getTableName(),
                'country_t',
                'country_t.table_name = :country_table_name AND country_t.table_id = city.country_id 
                AND city_t.keyword = :country_name_keyword AND country_t.language = :language'
            )
            ->addLeftJoin(
                Translation::getTableName(),
                'address_t',
                'address_t.table_name = :address_table_name AND address_t.table_id = p.id 
                AND address_t.keyword = :address_name_keyword AND address_t.language = :language'
            )
            ->addLeftJoin(
                Translation::getTableName(),
                'name_t',
                'name_t.table_name = :name_table_name AND name_t.table_id = p.id 
                AND name_t.keyword = :name_name_keyword AND name_t.language = :language'
            )
            ->addLeftJoin(
                TranslationText::getTableName(),
                'description_t',
                'description_t.table_name = :description_table_name AND description_t.table_id = p.id 
                AND description_t.keyword = :description_keyword AND description_t.language = :language'
            )
            ->addLeftJoin(
                Translation::getTableName(),
                'short_description_t',
                'short_description_t.table_name = :short_description_table_name AND short_description_t.table_id = p.id 
                AND short_description_t.keyword = :short_description_keyword AND short_description_t.language = :language'
            )
            ->bindMany([
                'language' => Config::getControllerConfigValue('language', 'lt'),
                'city_table_name' => City::getTableName(),
                'city_name_keyword' => 'title',
                'country_table_name' => Country::getTableName(),
                'country_name_keyword' => 'title',
                'address_table_name' => self::getTableName(),
                'address_name_keyword' => 'address',
                'name_table_name' => self::getTableName(),
                'name_name_keyword' => 'title',
                'description_table_name' => self::getTableName(),
                'description_keyword' => 'description',
                'short_description_table_name' => self::getTableName(),
                'short_description_keyword' => 'short_description'
            ]);

        return $qb;
    }

    private static function attachFilters(QueryBuilder $qb, array $filters)
    {
        if (isset($filters['city_id']) && $filters['city_id'] > 0) {
            $qb->addWhere('p.city_id = :city_id')
                ->bindMany(['city_id' => $filters['city_id']]);
        }

        if (isset($filters['name'])) {
            $qb->addWhere('name_t.text LIKE :name')
                ->bindMany(['name' => '%' . $filters['name'] . '%']);
        }

        if (isset($filters['is_active']) && $filters['is_active'] === true) {
            $qb->addWhere('p.is_active = 1');
        }
    }

    /**
     * Fetch pubs data
     *
     * @param int $limit
     * @param int $offset
     * @param array $filters
     * @param array $orderBy
     * @return array
     */
    public static function fetchAll($limit = 10, $offset = 0, array $filters = [], array $orderBy = ['p.id DESC'])
    {
        $qb =self::getBaseSelectQuery()
            ->orderBy($orderBy)
            ->setLimit($limit);

        if ($offset) {
            $qb->setOffset($offset);
        }

        self::attachFilters($qb, $filters);

        return $qb->fetchAll();
    }

    /**
     * Fetch pub element
     *
     * @param $id
     * @param array $filters
     * @return array
     */
    public static function fetch($id, $filters = [])
    {
        $qb = self::getBaseSelectQuery()
            ->addWhere('p.id = :id')
            ->bindMany(['id' => $id]);

        self::attachFilters($qb, $filters);

        return $qb->fetchOne();
    }

    /**
     * Update Pub info
     *
     * @param $id
     * @param $data
     * @return bool
     */
    public static function update($id, $data)
    {
        return (new QueryBuilder())
            ->getUpdateQuery()
            ->setUpdateColumns($data)
            ->setTable(self::$tableName)
            ->addWhere('id = :id')
            ->bindMany(['id' => $id])
            ->update();
    }

    /**
     * Update Pub info
     *
     * @param $data
     * @return bool
     */
    public static function add($data)
    {
        return (new QueryBuilder())
            ->getInsertQuery()
            ->setInsertColumns($data)
            ->setInto(self::$tableName)
            ->insert();
    }

    /**
     * Get pubs count
     *
     * @param array $filters
     * @return int
     */
    public static function getPubsCount($filters = [])
    {
        $qb = self::getBaseSelectQuery(['COUNT(p.id)']);
        self::attachFilters($qb, $filters);
        return $qb->count();
    }
}