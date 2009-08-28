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
 * @package    Zend_Entity
 * @subpackage Query
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Description of how a storage result is mapped to an entity result
 *
 * @category   Zend
 * @package    Zend_Entity
 * @subpackage Query
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Entity_Query_ResultSetMapping
{
    /**
     * @var array
     */
    public $entityResult = array();

    /**
     * @var array
     */
    public $joinedEntity = array();

    /**
     * @var array
     */
    public $rootEntity = array();

    /**
     * @var array
     */
    public $scalarResult = array();

    /**
     * @var array
     */
    public $storageFieldEntity = array();

    /**
     * @var array
     */
    private $_aliasToEntity = array();

    /**
     * Add Entity to ResultSetMapping
     *
     * Signal that an entity is part of the sql result set.
     *
     * @param  string $entityName
     * @param  string $alias
     * @return Zend_Entity_Query_ResultSetMapping
     */
    public function addEntity($entityName, $alias=null)
    {
        if($alias == null) {
            $alias = $entityName;
        }

        $this->rootEntity[] = $entityName;
        $this->_aliasToEntity[$alias] = $entityName;
        $this->entityResult[$entityName] = array(
            'properties' => array(),
        );
        return $this;
    }

    /**
     * Add a joined entity to ResultSetMapping
     *
     * Joined entities don't lead to a mixed result, but only get filled into the identity map before creation of
     * the "normal" result entities and will hence be related to them in their full state already.
     *
     * @param  string $entityName
     * @param  string $alias
     * @return Zend_Entity_Query_ResultSetMapping
     */
    public function addJoinedEntity($entityName, $alias=null, $parentEntity=null, $parentEntityProperty=null)
    {
        if($alias == null) {
            $alias = $entityName;
        }

        $this->joinedEntity[$entityName] = array('parentEntity' => $parentEntity, 'parentProperty' => $parentEntityProperty);
        $this->_aliasToEntity[$alias] = $entityName;
        $this->entityResult[$entityName] = array(
            'properties' => array(),
        );
        return $this;
    }

    /**
     * Add a new entity proprty to the result.
     * 
     * @param string $alias
     * @param string $storageFieldName
     * @param string $propertyName
     * @return Zend_Entity_Query_ResultSetMapping
     */
    public function addProperty($alias, $storageFieldName, $propertyName)
    {
        if(isset($this->_aliasToEntity[$alias])) {
            $entityName = $this->_aliasToEntity[$alias];
        } else {
            throw new Zend_Entity_Exception(
                "No valid alias or entity name '".$alias."' was given to add property '".$propertyName."' to."
            );
        }
        $this->entityResult[$entityName]['properties'][$storageFieldName] = $propertyName;
        $this->storageFieldEntity[$storageFieldName] = $entityName;
        return $this;
    }

    /**
     * A Scalar result is a single
     *
     * @param  string $scalarName
     * @return Zend_Entity_Query_ResultSetMapping
     */
    public function addScalar($scalarName)
    {
        $this->scalarResult[] = $scalarName;
        return $this;
    }
}