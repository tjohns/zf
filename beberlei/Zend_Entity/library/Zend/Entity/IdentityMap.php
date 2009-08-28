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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * Identity Map
 *
 * @category   Zend
 * @package    Zend_Entity
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Entity_IdentityMap
{
    /**
     * HashMap to keep loaded objects only once.
     *
     * @var array
     */
    protected $_loadedObjects = array();

    /**
     * Primary Keys
     *
     * @var array
     */
    protected $_primaryKeys = array();

    /**
     * @var array
     */
    protected $_versions = array();

    /**
     * @param string $entityInterface
     * @param string $key
     * @param Zend_Entity_Interface $entity
     * @param int $version
     */
    public function addObject($entityInterface, $key, Zend_Entity_Interface $entity, $version = null)
    {
        $h = spl_object_hash($entity);
        $this->_primaryKeys[$h] = $key;
        $this->_loadedObjects[$entityInterface][$key] = $entity;

        if($version !== null) {
            $this->_versions[$h] = $version;
        }
    }

    /**
     * @param string $entityInterface
     * @param string $key
     * @return boolean
     */
    public function hasObject($entityInterface, $key)
    {
        return (
            isset($this->_loadedObjects[$entityInterface][$key]) &&
            !$this->isLazyLoadObject($entityInterface, $key)
        );
    }

    /**
     * @param string $entityInterface
     * @param string $key
     * @return boolean
     */
    public function hasLazyObject($entityInterface, $key)
    {
        return (
            isset($this->_loadedObjects[$entityInterface][$key]) &&
            $this->isLazyLoadObject($entityInterface, $key)
        );
    }

    /**
     * @param  string $entityInterface
     * @param  string $key
     * @return boolean
     */
    public function isLazyLoadObject($entityInterface, $key)
    {
        return ($this->_loadedObjects[$entityInterface][$key] instanceof Zend_Entity_LazyLoad_Entity);
    }

    /**
     *
     * @param  string $entityInterface
     * @param  string $key
     * @return Zend_Entity_Interface
     */
    public function getObject($entityInterface, $key)
    {
        return $this->_loadedObjects[$entityInterface][$key];
    }

    /**
     * @param  Zend_Entity_Interface $entity
     * @return boolean
     */
    public function contains(Zend_Entity_Interface $entity)
    {
        return isset($this->_primaryKeys[spl_object_hash($entity)]);
    }

    /**
     * @param string $entityInterface
     * @param Zend_Entity_Interface $entity
     */
    public function remove($entityInterface, Zend_Entity_Interface $entity)
    {
        $hash = spl_object_hash($entity);
        unset($this->_loadedObjects[$entityInterface][$this->_primaryKeys[$hash]]);
        unset($this->_primaryKeys[$hash]);
    }

    /**
     * @param  Zend_Entity_Interface $entity
     * @return string
     */
    public function getPrimaryKey(Zend_Entity_Interface $entity)
    {
        $hash = spl_object_hash($entity);
        if(isset($this->_primaryKeys[$hash])) {
            return $this->_primaryKeys[$hash];
        } else {
            require_once "Zend/Entity/Exception.php";
            throw new Zend_Entity_Exception(
                "Entity of class '".get_class($entity)."' is not contained ".
                "in persistence context and has no primary key."
            );
        }
    }

    /**
     * @param Zend_Entity_Interface $entity
     * @param int $versionId
     */
    public function setVersion(Zend_Entity_Interface $entity, $versionId)
    {
        $this->_versions[spl_object_hash($entity)] = $versionId;
    }

    /**
     * Return version of an entity the current context works with.
     * 
     * @param Zend_Entity_Interface $entity
     * @return int|boolean
     */
    public function getVersion(Zend_Entity_Interface $entity)
    {
        $hash = spl_object_hash($entity);
        if(isset($this->_versions[$hash])) {
            return $this->_versions[$hash];
        }
        return false;
    }

    /**
     * Clear this identity map
     *
     * @return void
     */
    public function clear()
    {
        $this->_primaryKeys = array();
        $this->_loadedObjects = array();
    }
}