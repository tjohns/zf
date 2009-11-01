<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Mapper
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Native Query implementation for the Database Mapper
 *
 * @uses       Zend_Entity_Query_QueryAbstract
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Mapper
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_Mapper_SqlQueryBuilder extends Zend_Db_Mapper_SqlQueryAbstract
{
    /**
     * @var Zend_Entity_Manager_Interface
     */
    protected $_entityManager = null;

    /**
     * @var Zend_Entity_Query_ResultSetMapping
     */
    protected $_rsm = null;

    /**
     * @var int
     */
    protected $_offset = null;

    /**
     * @var int
     */
    protected $_itemCountPerPage = null;

    /**
     * @var Zend_Db_Mapper_QueryObject
     */
    protected $_queryObject = null;

    /**
     * @var array
     */
    protected $_metadata = array();

    /**
     *
     * @param Zend_Entity_Manager_Interface $manager
     * @param Zend_Db_Mapper_QueryObject $queryObject
     */
    public function __construct(Zend_Entity_Manager_Interface $manager, Zend_Db_Mapper_QueryObject $queryObject=null)
    {
        $mapper = $manager->getMapper();
        if(!($mapper instanceof Zend_Db_Mapper_Mapper)) {
            throw new Zend_Entity_StorageMissmatchException("SqlQueryBuilder only works with Zend_Db_Mapper storage engine");
        }

        if($queryObject == null) {
            $queryObject = $mapper->createSqlQueryObject();
        }

        $this->_metadata = $manager->getMetadataFactory();
        $this->_entityManager = $manager;
        $this->_queryObject = $queryObject;
        $this->_rsm = new Zend_Entity_Query_ResultSetMapping();
    }

    /**
     * @param  string $table
     * @param  string $schema
     * @return Zend_Db_Mapper_SqlQueryBuilder
     */
    public function from($table, $schema = null)
    {
        $this->_queryObject->from($table, array(), $schema);
        return $this;
    }

    /**
     * Wrapper around from() and with() to add an Entity to the ResultSetMapping as Root Entity.
     * 
     * @param  string $entityName
     * @param  string $correlationName
     * @return Zend_Db_Mapper_SqlQueryBuilder
     */
    public function fromEntity($entityName, $correlationName=null)
    {
        $mapping = $this->_metadata[$entityName];
        $table = $mapping->table;

        if($correlationName !== null) {
            $table = array($correlationName => $table);
        }

        $schema = $mapping->schema;
        $this->from($table, $schema);
        $this->with($entityName, $correlationName);
        return $this;
    }

    /**
     * @param  string|array $table
     * @param  string $onCondition
     * @param  string $schema
     * @return Zend_Db_Mapper_SqlQueryBuilder
     */
    public function join($table, $onCondition, $schema=null)
    {
        $this->_queryObject->join($table, $onCondition, array(), $schema);
        return $this;
    }

    /**
     * @param  string|array $table
     * @param  string $onCondition
     * @param  string $schema
     * @return Zend_Db_Mapper_SqlQueryBuilder
     */
    public function joinInner($table, $onCondition, $schema=null)
    {
        $this->_queryObject->join($table, $onCondition, array(), $schema);
        return $this;
    }

    /**
     * @param  string|array $table
     * @param  string $onCondition
     * @param  string $schema
     * @return Zend_Db_Mapper_SqlQueryBuilder
     */
    public function joinLeft($table, $onCondition, $schema=null)
    {
        $this->_queryObject->joinLeft($table, $onCondition, array(), $schema);
        return $this;
    }

    /**
     * @param  string|array $table
     * @param  string $onCondition
     * @param  string $schema
     * @return Zend_Db_Mapper_SqlQueryBuilder
     */
    public function joinRight($table, $onCondition, $schema=null)
    {
        $this->_queryObject->joinRight($table, $onCondition, array(), $schema);
        return $this;
    }

    /**
     * @param  string|array $table
     * @param  string $onCondition
     * @param  string $schema
     * @return Zend_Db_Mapper_SqlQueryBuilder
     */
    public function joinFull($table, $onCondition, $schema=null)
    {
        $this->_queryObject->joinFull($table, $onCondition, array(), $schema);
        return $this;
    }

    /**
     * @param  string|array $table
     * @param  string $onCondition
     * @param  string $schema
     * @return Zend_Db_Mapper_SqlQueryBuilder
     */
    public function joinCross($table, $schema)
    {
        $this->_queryObject->joinCross($table, array(), $schema);
        return $this;
    }

    /**
     * @param  string|array $table
     * @param  string $onCondition
     * @param  string $schema
     * @return Zend_Db_Mapper_SqlQueryBuilder
     */
    public function joinNatural($table, $schema)
    {
        $this->_queryObject->joinNatural($table, array(), $schema);
        return $this;
    }

    /**
     * Add an inner join with creation of the given joined entity from its known table.
     *
     * @param string $joinedEntityName
     * @param string $onCondition
     * @param string $correlationName
     * @return Zend_Db_Mapper_SqlQueryBuilder
     */
    public function joinEntity($joinedEntityName, $onCondition, $correlationName=null)
    {
        $this->_joinEntity("inner", $joinedEntityName, $onCondition, $correlationName);
        return $this;
    }

    /**
     * Add a left join with creation of the given joined entity from its known table.
     *
     * @param string $joinedEntityName
     * @param string $onCondition
     * @param string $correlationName
     * @return Zend_Db_Mapper_SqlQueryBuilder
     */
    public function joinLeftEntity($joinedEntityName, $onCondition, $correlationName=null)
    {
        $this->_joinEntity("left", $joinedEntityName, $onCondition, $correlationName);
        return $this;
    }

    /**
     * Add a right join with creation of the given joined entity from its known table.
     *
     * @param string $joinedEntityName
     * @param string $onCondition
     * @param string $correlationName
     * @return Zend_Db_Mapper_SqlQueryBuilder
     */
    public function joinRightEntity($joinedEntityName, $onCondition, $correlationName=null)
    {
        $this->_joinEntity("right", $joinedEntityName, $onCondition, $correlationName);
        return $this;
    }

    /**
     * Add a full join with creation of the given joined entity from its known table.
     *
     * @param string $joinedEntityName
     * @param string $onCondition
     * @param string $correlationName
     * @return Zend_Db_Mapper_SqlQueryBuilder
     */
    public function joinFullEntity($joinedEntityName, $onCondition, $correlationName=null)
    {
        $this->_joinEntity("full", $joinedEntityName, $onCondition, $correlationName);
        return $this;
    }

    /**
     * Add a cross join with creation of the given joined entity from its known table.
     *
     * @param string $joinedEntityName
     * @param string $correlationName
     * @return Zend_Db_Mapper_SqlQueryBuilder
     */
    public function joinCrossEntity($joinedEntityName, $correlationName)
    {
        $this->_joinEntity("cross", $joinedEntityName, null, $correlationName);
        return $this;
    }

    /**
     * Add a natural join with creation of the given joined entity from its known table.
     *
     * @param string $joinedEntityName
     * @param string $correlationName
     * @return Zend_Db_Mapper_SqlQueryBuilder
     */
    public function joinNaturalEntity($joinedEntityName, $correlationName)
    {
        $this->_joinEntity("natural", $joinedEntityName, null, $correlationName);
        return $this;
    }

    /**
     * @param string $joinType
     * @param string $joinedEntityName
     * @param string $onCondition
     * @param string|null $correlationName
     */
    protected function _joinEntity($joinType, $joinedEntityName, $onCondition=null, $correlationName=null)
    {
        $mapping = $this->_metadata[$joinedEntityName];
        if($correlationName == null) {
            $table = $mapping->table;
            $entityCorrelationName = $mapping->table;
        } else {
            $table = array($correlationName => $mapping->table);
            $entityCorrelationName = $correlationName;
        }

        switch($joinType) {
            case 'inner':
                $this->_queryObject->joinInner($table, $onCondition, array(), $mapping->schema);
                break;
            case 'left':
                $this->_queryObject->joinLeft($table, $onCondition, array(), $mapping->schema);
                break;
            case 'right':
                $this->_queryObject->joinRight($table, $onCondition, array(), $mapping->schema);
                break;
            case 'full':
                $this->_queryObject->joinFull($table, $onCondition, array(), $mapping->schema);
                break;
            case 'cross':
                $this->_queryObject->joinCross($table, array(), $mapping->schema);
                break;
            case 'natural':
                $this->_queryObject->joinNatural($table, array(), $mapping->schema);
                break;
        }

        $this->with($joinedEntityName, $entityCorrelationName, true);
    }

    /**
     * Add one ore more scalars to the result columns and resultset.
     * 
     * @param  string $scalar
     * @param  string|Zend_Db_Expr $scalarExpression
     * @return Zend_Db_Mapper_SqlQueryBuilder
     */
    public function scalar($scalarName, $scalarExpression)
    {
        $this->_rsm->addScalar($scalarName);
        $this->_queryObject->columns($scalarExpression);
        return $this;
    }

    /**
     *
     * @param bool $flag
     * @return Zend_Db_Mapper_SqlQueryBuilder
     */
    public function distinct($flag)
    {
        $this->_queryObject->distinct($flag);
        return $this;
    }

    /**
     *
     * @param  bool $flag
     * @return Zend_Db_Mapper_SqlQueryBuilder
     */
    public function forUpdate($flag)
    {
        $this->_queryObject->forUpdate($flag);
        return $this;
    }

    /**
     *
     * @param  string $entityName
     * @param  string $correlationName
     * @return Zend_Db_Mapper_SqlQueryBuilder
     */
    public function with($entityName, $correlationName = null, $joined = false)
    {
        $mapping = $this->_metadata[$entityName];

        if($joined) {
            $this->_rsm->addJoinedEntity($entityName);
        } else {
            $this->_rsm->addEntity($entityName);
        }
        foreach($mapping->columnNameToProperty AS $columnName => $propertyName) {
            $this->_rsm->addProperty($entityName, $columnName, $propertyName);
        }

        $this->_queryObject->columns($mapping->sqlColumnAliasMap, $correlationName);
        return $this;
    }

    /**
     *
     * @param  string $entityName
     * @param  string $correlationName
     * @return Zend_Db_Mapper_SqlQueryBuilder
     */
    public function withJoined($entityName, $correlationName=null)
    {
        if($correlationName == null) {
            $mapping = $this->_metadata[$entityName];
            $correlationName = $mapping->table;
        }

        return $this->with($entityName, $correlationName, true);
    }

    /**
     * @param  string $cond
     * @param  string $value
     * @param  string $type
     * @return Zend_Db_Mapper_SqlQueryBuilder
     */
    public function where($cond, $value = null, $type = null)
    {
        $this->_queryObject->where($cond, $value, $type);
        return $this;
    }

    /**
     *
     * @param  string $cond
     * @param  string $value
     * @param  string $type
     * @return Zend_Db_Mapper_SqlQueryBuilder
     */
    public function orWhere($cond, $value = null, $type = null)
    {
        $this->_queryObject->orWhere($cond, $value, $type);
        return $this;
    }

    /**
     *
     * @param  string $spec
     * @return Zend_Db_Mapper_SqlQueryBuilder
     */
    public function group($spec)
    {
        $this->_queryObject->group($spec);
        return $this;
    }

    /**
     * @param  string $cond
     * @param  string|null $value
     * @return Zend_Db_Mapper_SqlQueryBuilder
     */
    public function having($cond, $value=null)
    {
        if($value == null) {
            $this->_queryObject->having($cond);
        } else {
            $this->_queryObject->having($cond, $value);
        }
        return $this;
    }

    /**
     *
     * @param  string $cond
     * @param  string|null $value
     * @return Zend_Db_Mapper_SqlQueryBuilder
     */
    public function orHaving($cond, $value=null)
    {
        if($value == null) {
            $this->_queryObject->orHaving($cond);
        } else {
            $this->_queryObject->orHaving($cond, $value);
        }
        return $this;
    }

    /**
     *
     * @param  string $order
     * @return Zend_Db_Mapper_SqlQueryBuilder
     */
    public function order($order)
    {
        $this->_queryObject->order($order);
        return $this;
    }

    /**
     *
     * @param  int|null $count
     * @param  int|null $offset
     * @return Zend_Db_Mapper_SqlQueryBuilder
     */
    public function limit($count = null, $offset = null)
    {
        $this->_queryObject->limit($count, $offset);
        return $this;
    }

    /**
     * @return Zend_Db_Mapper_QueryObject
     */
    protected function getQueryObject()
    {
        return $this->_queryObject;
    }

    protected function _doExecute()
    {
        $sql = $this->_queryObject->assemble();
        $db = $this->_queryObject->getAdapter();
        $stmt = $db->prepare($sql);
        foreach($this->getParams() AS $paramName => $paramValue) {
            // TODO: Add type!
            $stmt->bindParam($paramName, $paramValue);
        }
        $stmt->execute();
        return $stmt;
    }

    public function setFirstResult($offset)
    {
        $this->_offset = $offset;
        $this->getQueryObject()->limit($this->_itemCountPerPage, $this->_offset);
        return $this;
    }

    public function setMaxResults($itemCountPerPage)
    {
        $this->_itemCountPerPage = $itemCountPerPage;
        $this->getQueryObject()->limit($this->_itemCountPerPage, $this->_offset);
        return $this;
    }

    public function toSql()
    {
        return $this->getQueryObject()->assemble();
    }
}