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
     * @param object $entity
     * @param int $version
     */
    public function addObject($entityInterface, $key, $entity, $version = null)
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
     * @return object
     */
    public function getObject($entityInterface, $key)
    {
        return $this->_loadedObjects[$entityInterface][$key];
    }

    /**
     * @param  object $entity
     * @return boolean
     */
    public function contains($entity)
    {
        return isset($this->_primaryKeys[spl_object_hash($entity)]);
    }

    /**
     * @param string $entityInterface
     * @param object $entity
     */
    public function remove($entityInterface, $entity)
    {
        $hash = spl_object_hash($entity);
        unset($this->_loadedObjects[$entityInterface][$this->_primaryKeys[$hash]]);
        unset($this->_primaryKeys[$hash]);
    }

    /**
     * @param  object $entity
     * @return string
     */
    public function getPrimaryKey($entity)
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
     * @param object $entity
     * @param int $versionId
     */
    public function setVersion($entity, $versionId)
    {
        $this->_versions[spl_object_hash($entity)] = $versionId;
    }

    /**
     * Return version of an entity the current context works with.
     * 
     * @param object $entity
     * @return int|boolean
     */
    public function getVersion($entity)
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

    /**
     *
     * @param  string $entityName
     * @return array
     */
    public function getLoadedObjects($entityName)
    {
        return $this->_loadedObjects[$entityName];
    }
}