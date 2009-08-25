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
 * @package    Entity
 * @subpackage Query
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Abstract Query
 *
 * @uses       Zend_Paginator_AdapterAggregate
 * @category   Zend
 * @package    Entity
 * @subpackage Query
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Entity_Query_QueryAbstract implements Zend_Paginator_AdapterAggregate
{
    /**
     * @var array
     */
    protected $_params = array();

    /**
     * @var array
     */
    protected $_hints = array();

    /**
     * @var Zend_Entity_Mapper_ResultSetMapping
     */
    protected $_rsm = null;

    /**
     * @return Zend_Entity_Mapper_ResultSetMapping
     */
    public function getResultSetMapping()
    {
        return $this->_rsm;
    }

    /**
     * @return object[]
     */
    abstract public function getResultList();

    /**
     * @return array
     */
    abstract public function getResultArray();

    /**
     *
     * @throws Zend_Entity_NonUniqueResultException
     * @throws Zend_Entity_NoResultException
     * @return Zend_Entity_Interface
     */
    public function getSingleResult()
    {
        $collection = $this->getResultList();
        $numResults = count($collection);
        if($numResults == 1) {
            return $collection[0];
        } elseif($numResults == 0) {
            if($this->getHint('singleResultNotFound') === Zend_Entity_Manager::NOTFOUND_NULL) {
                return null;
            } else {
                require_once "Zend/Entity/Exception.php";
                throw new Zend_Entity_NoResultException();
            }
        } elseif($numResults > 1) {
            throw new Zend_Entity_NonUniqueResultException();
        }
    }

    abstract public function setFirstResult($offset);

    abstract public function setMaxResults($itemCountPerPage);

    /**
     * @param  string|int $name
     * @param  mixed $value
     * @return Zend_Entity_Query_QueryAbstract
     */
    public function setParameter($name, $value)
    {
        $this->_params[$name] = $value;
        return $this;
    }

    /**
     *
     * @param  array $params
     * @return Zend_Entity_Query_QueryAbstract
     */
    public function setParameters($params)
    {
        foreach($params AS $k => $v) {
            $this->setParameter($k, $v);
        }
        return $this;
    }

    /**
     * @param  string $name
     * @return mixed
     */
    public function getParameter($name)
    {
        if(isset($this->_params[$name])) {
            return $this->_params[$name];
        }
        return null;
    }

    /**
     * @param string $name
     * @param string $value
     * @return Zend_Entity_Query_Query_Abstract
     */
    public function setHint($name, $value)
    {
        $this->_hints[$name] = $value;
        return $this;
    }

    /**
     *
     * @param  string $name
     * @return mixed|false
     */
    public function getHint($name)
    {
        if(isset($this->_hints[$name])) {
            return $this->_hints[$name];
        }
        return false;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->_params;
    }

    abstract public function __toString();
}