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
abstract class Zend_Entity_Mapper_SqlQueryAbstract extends Zend_Entity_Query_QueryAbstract
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

    public function getResultList()
    {
        return $this->_execute(Zend_Entity_Manager::FETCH_ENTITIES);
    }

    public function getResultArray()
    {
        return $this->_execute(Zend_Entity_Manager::FETCH_ARRAY);
    }

    protected function _execute($fetchMode)
    {
        $stmt = $this->_doExecute();
        $resultSet = $stmt->fetchAll();
        $stmt->closeCursor();

        return $this->_entityManager->getMapper()
            ->getLoader($fetchMode, $this->_entityManager)
            ->processResultset($resultSet, $this->_rsm);
    }

    abstract protected function _doExecute();

    /**
     * @return Zend_Paginator_Adapter_DbSelect
     */
    public function getPaginatorAdapter()
    {
        return new Zend_Paginator_Adapter_DbSelect($this->getQueryObject());
    }

    public function __toString()
    {
        return $this->toSql();
    }
}