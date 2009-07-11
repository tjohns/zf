<?php
/**
 * Mapper
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.
 * 
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to kontakt@beberlei.de so we can send you a copy immediately.
 *
 * @category   Zend
 * @category   Zend_Entity
 * @copyright  Copyright (c) 2009 Benjamin Eberlei
 * @license    New BSD License
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
     * @param string $entityInterface
     * @param string $key
     * @param Zend_Entity_Interface $entity
     */
    public function addObject($entityInterface, $key, Zend_Entity_Interface $entity)
    {
        $this->_primaryKeys[spl_object_hash($entity)] = $key;
        $this->_loadedObjects[$entityInterface][$key] = $entity;
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
        return ($this->_loadedObjects[$entityInterface][$key] instanceof Zend_Entity_Mapper_LazyLoad_Entity);
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
     * @param  Zend_Entity_Interface $entity
     * @return string
     */
    public function getPrimaryKey(Zend_Entity_Interface $entity)
    {
        $hash = spl_object_hash($entity);
        if(isset($this->_primaryKeys[$hash])) {
            return $this->_primaryKeys[$hash];
        } else {
            throw new Exception("Entity of class '".$entity."' is not contained in persistence context and has no primary key.");
        }
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