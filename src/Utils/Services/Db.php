<?php
/**
 * Created by PhpStorm.
 * User: karolis
 * Date: 12/5/17
 * Time: 1:26 PM
 */

namespace BestPub\Utils\Services;

if (extension_loaded('newrelic')) {
    newrelic_add_custom_tracer('BestPub\\Utils\\Services\\Db::setInstance');
    newrelic_add_custom_tracer('BestPub\\Utils\\Services\\Db::setDbConfig');
}

use ADOConnection;
use Aura\SqlQuery\QueryFactory;
use PDO;

class Db
{
    private static $db;
    private static $dbConfig;
    /**
     * @var PDO
     */
    private static $dbInstance;

    /**
     * @return mixed
     */
    public static function getInstance()
    {
        return self::$db;
    }

    /**
     * @param ADOConnection $db
     */
    public static function setInstance($db)
    {
        self::$db = $db;
    }

    /**
     * @param array $config
     */
    public static function setDbConfig($config)
    {
        self::$dbConfig = $config;
    }

    /**
     * @return QueryFactory
     */
    public static function getQueryBuilder()
    {
        return new QueryFactory('mysql');
    }

    /**
     * @return PDO
     */
    public static function getDbInstance()
    {
        if(self::$dbInstance === null) {
            self::$dbInstance = new \PDO(
                'mysql:host=' . self::$dbConfig['host'] . ';dbname=' . self::$dbConfig['database']. ';charset=utf8',
                self::$dbConfig['user'],
                self::$dbConfig['password']
            );
            self::$dbInstance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            self::$dbInstance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return self::$dbInstance;
    }
}