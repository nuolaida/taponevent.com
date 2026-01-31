<?php

namespace BestPub\News\Model;
use BestPub\Core\Model\Translation;
use BestPub\Core\Model\TranslationText;
use BestPub\Utils\Services\Db\QueryBuilder;

/**
 * Created by PhpStorm.
 * User: karolis
 * Date: 17/10/18
 * Time: 5:45 PM
 */

class News implements \BestPub\Base\Model
{
    private static $tableName = 'news';

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
                'title_t.text AS title',
                'description_t.text AS description',
                'short_description_t.text AS short_description',
                'n.*'
            ];
        }

        $qb = (new QueryBuilder())->getSelectQuery();
        $qb->setSelectColumns($columns)
            ->setFrom(self::$tableName, 'n')
            ->addLeftJoin(
                Translation::getTableName(),
                'title_t',
                'title_t.table_name = :title_table_name AND title_t.table_id = n.id 
                AND title_t.keyword = :title_name_keyword AND title_t.language = :language'
            )
            ->addLeftJoin(
                TranslationText::getTableName(),
                'description_t',
                'description_t.table_name = :description_table_name AND description_t.table_id = n.id 
                AND description_t.keyword = :description_name_keyword AND description_t.language = :language'
            )
            ->addLeftJoin(
                TranslationText::getTableName(),
                'short_description_t',
                'short_description_t.table_name = :description_table_name AND short_description_t.table_id = n.id 
                AND short_description_t.keyword = :short_description_name_keyword AND short_description_t.language = :language'
            )
            ->bindMany([
                // We are not supporting EN at the moment, so LT is forced
                'language' => 'lt',
                'title_table_name' => self::getTableName(),
                'title_name_keyword' => 'title',
                'description_table_name' => self::getTableName(),
                'description_name_keyword' => 'description',
                'short_description_table_name' => self::getTableName(),
                'short_description_name_keyword' => 'short_description'
            ]);

        return $qb;
    }

    /**
     * Fetch news data
     *
     * @param int $limit
     * @param int $offset
     * @param array $orderBy
     * @return array
     */
    public static function fetchAll($limit = 10, $offset = 0, array $orderBy = ['n.id DESC'])
    {
        $qb =self::getBaseSelectQuery()
            ->orderBy($orderBy)
            ->setLimit($limit);

        if ($offset) {
            $qb->setOffset($offset);
        }

        return $qb->fetchAll();
    }

    /**
     * Fetch news element
     *
     * @param $id
     * @return array
     */
    public static function fetch($id)
    {
        return self::getBaseSelectQuery()
            ->addWhere('n.id = :id')
            ->bindMany(['id' => $id])
            ->fetchOne();
    }

    /**
     * Get news count
     *
     * @return int
     */
    public static function getNewsCount()
    {
        return self::getBaseSelectQuery(['COUNT(n.id)'])->count();
    }
}