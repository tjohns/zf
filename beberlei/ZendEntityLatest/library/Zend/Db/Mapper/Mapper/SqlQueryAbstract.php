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
 * Abstract SQL Query Representation
 *
 * @uses       Zend_Entity_Query_QueryAbstract
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Mapper
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Db_Mapper_SqlQueryAbstract extends Zend_Entity_Query_QueryAbstract
{
    /**
     * @var Zend_Entity_Manager_Interface
     */
    protected $_entityManager = null;

    /**
     * Return current associated SQL string of the Query.
     *
     * @return string
     */
    abstract public function toSql();

    /**
     * Return the query result as a list of object entities.
     * 
     * @return array
     */
    public function getResultList()
    {
        return $this->_execute(Zend_Entity_Manager::FETCH_ENTITIES);
    }

    /**
     * Return the query result as a deep array structure simliar to the object structure.
     *
     * @return array
     */
    public function getResultArray()
    {
        return $this->_execute(Zend_Entity_Manager::FETCH_ARRAY);
    }

    /**
     * Return the single column of the single result row.
     *
     * If the result structure is not a single column in a single row an
     * exception is thrown.
     *
     * @throws Zend_Entity_NonUniqueResultException
     * @return string
     */
    public function getSingleScalar()
    {
        return $this->_execute(Zend_Entity_Manager::FETCH_SINGLESCALAR);
    }

    /**
     * Return a scalar resultset where entity fields are renamed and casted only.
     *
     * @return array
     */
    public function getScalarResult()
    {
        return $this->_execute(Zend_Entity_Manager::FETCH_SCALAR);
    }

    /**
     * Execute the Statement with the given fetch mode and hand it to the loader.
     *
     * @param  int $fetchMode
     * @return mixed
     */
    protected function _execute($fetchMode)
    {
        $stmt = $this->_doExecute();
        $resultSet = $stmt->fetchAll();
        $stmt->closeCursor();

        return $this->_entityManager->getMapper()
            ->getLoader($fetchMode, $this->_entityManager)
            ->processResultset($resultSet, $this->_rsm);
    }

    /**
     * @return Zend_Db_Statement_Interface
     */
    abstract protected function _doExecute();

    /**
     * @return Zend_Paginator_Adapter_DbSelect
     */
    public function getPaginatorAdapter()
    {
        $loader = $this->_entityManager->getMapper()
                       ->getLoader(Zend_Entity_Manager::FETCH_ENTITIES, $this->_entityManager);
        return new Zend_Db_Mapper_SqlQueryPaginator($this->getQueryObject(), $loader, $this->_rsm);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toSql();
    }
}