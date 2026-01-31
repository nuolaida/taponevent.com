<?php
/**
 * Created by PhpStorm.
 * User: karolis
 * Date: 7/21/18
 * Time: 11:48 AM
 */

namespace BestPub\Text\Model;


use BestPub\Base\Model;
use BestPub\Core\Model\Translation;
use BestPub\Core\Model\TranslationText;
use BestPub\Utils\Services\Config;

class Text implements Model
{
    private static $tableName = 'texts';

    public static function getTableName()
    {
        return self::$tableName;
    }

    /**
     * Get base query for pubs fetch
     *
     * @return \BestPub\Utils\Services\Db\QueryBuilder
     */
    private static function getBaseSelectQuery()
    {
        $qb = (new \BestPub\Utils\Services\Db\QueryBuilder())->getSelectQuery();
        $qb->setSelectColumns([
            'description_t.text AS text',
            'name_t.text AS title',
            't.*'
        ])
            ->setFrom(self::$tableName, 't')
            ->addLeftJoin(
                TranslationText::getTableName(),
                'description_t',
                'description_t.table_name = :description_table_name AND description_t.table_id = t.id 
                AND description_t.keyword = :description_keyword AND description_t.language = :language'
            )
            ->addLeftJoin(
                Translation::getTableName(),
                'name_t',
                'name_t.table_name = :name_table_name AND name_t.table_id = t.id 
                AND name_t.keyword = :name_name_keyword AND name_t.language = :language'
            )
            ->bindMany([
                'language' => Config::getControllerConfigValue('language', 'lt'),
                'description_table_name' => self::getTableName(),
                'description_keyword' => 'description',
                'name_table_name' => self::getTableName(),
                'name_name_keyword' => 'title',
            ]);

        return $qb;
    }

    /**
     * Fetch text
     *
     * @param $id
     * @return array
     */
    public static function fetch($id)
    {
        return self::getBaseSelectQuery()
            ->addWhere('t.id = :id')
            ->bindMany(['id' => $id])
            ->fetchOne();
    }
}