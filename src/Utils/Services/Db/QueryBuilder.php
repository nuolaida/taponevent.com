<?php
/**
 * Created by PhpStorm.
 * User: karolis
 * Date: 12/14/17
 * Time: 3:18 PM
 */

namespace BestPub\Utils\Services\Db;


use Aura\SqlQuery\Common\InsertInterface;
use Aura\SqlQuery\Common\SelectInterface;
use Aura\SqlQuery\Common\UpdateInterface;
use Aura\SqlQuery\Mysql\Insert;
use BestPub\Utils\Services\Db;

class QueryBuilder
{
    private $queryBuilder;

    /**
     * @var SelectInterface|UpdateInterface|InsertInterface|Insert
     */
    private $query;

    /**
     * QueryBuilder constructor.
     */
    public function __construct()
    {
        $this->queryBuilder = Db::getQueryBuilder();
    }

    /**
     * Create new select query
     *
     * @return QueryBuilder
     */
    public function getSelectQuery()
    {
        $this->query = $this->queryBuilder->newSelect();
        return $this;
    }

    /**
     * Create new delete query
     *
     * @return QueryBuilder
     */
    public function getDeleteQuery()
    {
        $this->query = $this->queryBuilder->newDelete();
        return $this;
    }

    /**
     * Create new Update Query
     *
     * @return $this
     */
    public function getUpdateQuery()
    {
        $this->query = $this->queryBuilder->newUpdate();
        return $this;
    }

    /**
     * Create new Update Query
     *
     * @return $this
     */
    public function getInsertQuery()
    {
        $this->query = $this->queryBuilder->newInsert();
        return $this;
    }

    /**
     * @param array $columns
     * @return $this
     */
    public function setSelectColumns(array $columns)
    {
        $this->query->cols($columns);
        return $this;
    }

    /**
     * Set columns for update
     * array keys - columns name, values - row values
     *
     * @param array $columns
     * @return QueryBuilder
     */
    public function setUpdateColumns(array $columns)
    {
        return $this->setSelectColumns($columns);
    }

    /**
     * Set columns for insert
     * array keys - columns name, values - row values
     *
     * @param array $columns
     * @return QueryBuilder
     */
    public function setInsertColumns(array $columns)
    {
        return $this->setSelectColumns($columns);
    }

    /**
     * @param $table
     * @param null $alias
     * @return $this
     */
    public function setFrom($table, $alias = null)
    {
        if($alias !== null) {
            $this->query->from($table . ' AS ' . $alias);
            return $this;
        }

        $this->query->from($table);

        return $this;
    }

    /**
     * Set table for update
     *
     * @param $table
     * @return $this
     */
    public function setTable($table)
    {
        $this->query->table($table);

        return $this;
    }

    /**
     * Set table for update
     *
     * @param $table
     * @return $this
     */
    public function setInto($table)
    {
        $this->query->into($table);

        return $this;
    }

    /**
     * @param $table
     * @param $alias
     * @param $condition
     * @return $this
     */
    public function addLeftJoin($table, $alias, $condition)
    {
        $this->query->join('LEFT', $table . ' AS ' . $alias, $condition);

        return $this;
    }

    /**
     * Add inner join to query
     *
     * @param $table
     * @param $alias
     * @param $condition
     * @return $this
     */
    public function addInnerJoin($table, $alias, $condition)
    {
        $this->query->join('INNER', $table . ' AS ' . $alias, $condition);

        return $this;
    }

    /**
     * @param $condition
     * @return $this
     */
    public function addWhere($condition)
    {
        $this->query->where($condition);

        return $this;
    }

    /**
     * Add condition with variables for array of values
     *
     * @param $column
     * @param $values
     * @return $this
     */
    public function addWhereIn($column, $values)
    {
        $preparedValues = [];
        foreach ((array)$values as $value) {
            $preparedValues[] = '\'' . $value . '\'';
        }

        $this->query->where($column . ' IN (' . implode(',', $values) . ')');

        return $this;
    }

    /**
     * Group by columns
     *
     * @param array $columns
     * @return $this
     */
    public function groupBy(array $columns)
    {
        $this->query->groupBy($columns);

        return $this;
    }

    /**
     * Set having condition
     *
     * @param string $condition
     * @return $this
     */
    public function having($condition)
    {
        $this->query->having($condition);

        return $this;
    }

    /**
     * @param $orderBy
     * @return $this
     */
    public function orderBy(array $orderBy)
    {
        $this->query->orderBy($orderBy);

        return $this;
    }

    /**
     * @param array $params
     * @return $this
     */
    public function bindMany(array $params)
    {
        $this->query->bindValues($params);

        return $this;
    }

    /**
     * Set limit
     *
     * @param $limit
     * @return $this
     */
    public function setLimit($limit)
    {
        $this->query->limit($limit);

        return $this;
    }

    /**
     * Set offset
     *
     * @param $offset
     * @return $this
     */
    public function setOffset($offset)
    {
        $this->query->offset($offset);

        return $this;
    }

    /**
     * @return array
     */
    public function fetchAll()
    {
        $sth = Db::getDbInstance()->prepare($this->query->getStatement());
        $sth->execute($this->query->getBindValues());
        return $sth->fetchAll();
    }

    /**
     * Fetch one row from db
     *
     * @return array
     */
    public function fetchOne()
    {
        $sth = Db::getDbInstance()->prepare($this->query->getStatement());
        $sth->execute($this->query->getBindValues());
        return $sth->fetch();
    }

    public function showColumns($tableName)
    {
        $sth = Db::getDbInstance()->prepare('SHOW COLUMNS FROM ' . $tableName);
        $sth->execute(null);
        return $sth->fetchAll();
    }

    /**
     * Return count value of given sql
     *
     * @return integer
     */
    public function count()
    {
        $sth = Db::getDbInstance()->prepare($this->query->getStatement());
        $sth->execute($this->query->getBindValues());
        return $sth->fetchColumn();
    }

    /**
     * Delete rows from database
     *
     * @return void
     */
    public function delete()
    {
        $sth = Db::getDbInstance()->prepare($this->query->getStatement());
        $sth->execute($this->query->getBindValues());
    }

    /**
     * Execute update query
     *
     * @return bool
     */
    public function update()
    {
        $sth = Db::getDbInstance()->prepare($this->query->getStatement());
        return $sth->execute($this->query->getBindValues());
    }

    /**
     * Execute insert query
     *
     * @return string|int
     */
    public function insert()
    {
        $sth = Db::getDbInstance()->prepare($this->query->getStatement());
        $sth->execute($this->query->getBindValues());
        return Db::getDbInstance()->lastInsertId();
    }

    /**
     * @return string
     */
    public function getStatement()
    {
        return $this->query->getStatement();
    }

    /**
     * @param $query
     * @return bool
     */
    public function executeQuery($query)
    {
        $sth = Db::getDbInstance()->prepare($query);
        return $sth->execute($this->query->getBindValues());
    }

    /**
     * Add on duplicate check
     *
     * @param array $columns
     * @return QueryBuilder
     */
    public function onDuplicateUpdate(array $columns)
    {
        $this->query->onDuplicateKeyUpdateCols($columns);
        return $this;
    }

    /**
     * Add row to insert statement
     *
     * @param $rows
     * @return QueryBuilder
     */
    public function addRows($rows)
    {
        $this->query->addRows($rows);
        return $this;
    }
}