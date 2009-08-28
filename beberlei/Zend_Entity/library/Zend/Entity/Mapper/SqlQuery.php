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
 * Native SQL Query whose result is mapped using a {@link Zend_Entity_Mapper_ResultSetMapping}.
 *
 * @uses       Zend_Entity_Mapper_SqlQueryAbstract
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Mapper
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Entity_Mapper_SqlQuery extends Zend_Entity_Mapper_SqlQueryAbstract
{
    /**
     * @var string
     */
    protected $_sql = null;

    /**
     *
     * @param Zend_Entity_Manager_Interface $em
     * @param string $sqlQuery
     * @param Zend_Entity_Mapper_ResultSetMapping $rsm
     */
    public function __construct(Zend_Entity_Manager_Interface $em, $sqlQuery, Zend_Entity_Mapper_ResultSetMapping $rsm)
    {
        $mapper = $em->getMapper();
        if(!($mapper instanceof Zend_Entity_Mapper_Mapper)) {
            throw new Zend_Entity_StorageMissmatchException("SqlQuery only works with Zend_Db_Mapper storage engine");
        }

        $this->_entityManager = $em;
        $this->_sql = $sqlQuery;
        $this->_rsm = $rsm;
    }

    public function setFirstResult($offset)
    {
        throw new Zend_Entity_Exception("Not implemented.");
    }

    public function setMaxResults($itemCountPerPage)
    {
        throw new Zend_Entity_Exception("Not implemented.");
    }


    protected function _doExecute()
    {
        return $this->_entityManager->getMapper()
                                    ->getAdapter()
                                    ->query($this->_sql, $this->getParams());
    }

    public function toSql()
    {
        return $this->_sql;
    }
}