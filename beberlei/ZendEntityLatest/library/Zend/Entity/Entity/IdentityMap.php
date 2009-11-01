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
     * @var array
     */
    protected $_objects = array();

    /**
     * @var array   snapshot of collections at loading, organized by entity hash
     *              and field name
     */
    protected $_relatedObjects = array();

    /**
     * @param string $entityClass
     * @param string $key
     * @param object $entity
     * @param int $version
     */
    public function addObject($entityClass, $key, $entity, $version = null)
    {
        $h = spl_object_hash($entity);
        
        $this->_objects[$h] = array(
            'id' => $key,
            'version' => $version,
            'entityClass' => $entityClass,
        );

        $this->_loadedObjects[$entityClass][$key] = $entity;
        $this->_relatedObjects[$h] = array();
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
        if(!is_object($entity)) {
            throw new Zend_Entity_InvalidEntityException();
        }

        $h = spl_object_hash($entity);

        return isset($this->_objects[$h]);
    }

    /**
     * @param object $entity
     */
    public function remove($entity)
    {
        if(!is_object($entity)) {
            throw new Zend_Entity_InvalidEntityException();
        }

        $hash = spl_object_hash($entity);
        if(!isset($this->_objects[$hash])) {
            return;
        }

        $id = $this->_objects[$hash]['id'];
        $entityClass = $this->_objects[$hash]['entityClass'];
        unset($this->_loadedObjects[$entityClass][$id]);
        unset($this->_objects[$hash]);
        unset($this->_relatedObjects[$hash]);
    }

    /**
     * @param  object $entity
     * @return string
     */
    public function getPrimaryKey($entity)
    {
        $hash = spl_object_hash($entity);
        if(isset($this->_objects[$hash])) {
            return $this->_objects[$hash]['id'];
        } else {
            throw new Zend_Entity_InvalidEntityException(
                "The primary key of an object of the type '".get_class($entity)."' ".
                "was not found in the identity map."
            );
        }
    }

    /**
     * @param object $entity
     * @param int $versionId
     */
    public function setVersion($entity, $versionId)
    {
        $hash = spl_object_hash($entity);
        if(!isset($this->_objects[$hash])) {
            throw new Zend_Entity_InvalidEntityException();
        }

        $this->_objects[$hash]['version'] = $versionId;
    }

    /**
     * Return version of an entity the current context works with.
     * 
     * @param object $entity
     * @return int
     */
    public function getVersion($entity)
    {
        $hash = spl_object_hash($entity);
        if(isset($this->_objects[$hash])) {
            return $this->_objects[$hash]['version'];
        }
        return 0;
    }

    /**
     * Clear this identity map
     *
     * @return void
     */
    public function clear()
    {
        $this->_objects = array();
        $this->_loadedObjects = array();
        $this->_relatedObjects = array();
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

    /**
     * @param object $entity
     * @param string $fieldName
     * @param object $relatedObject
     */
    public function storeRelatedObject($entity, $fieldName, $relatedObject)
    {
        $h = spl_object_hash($entity);

        $this->_relatedObjects[$h][$fieldName] = $relatedObject;
    }

    /**
     * @param  object $entity
     * @param  string $fieldName
     * @return object
     */
    public function getRelatedObject($entity, $fieldName)
    {
        $h = spl_object_hash($entity);

        if (!isset($this->_relatedObjects[$h])) {
            throw new Zend_Entity_InvalidEntityException(get_class($entity));
        }

        if (!isset($this->_relatedObjects[$h][$fieldName])) {
            throw new Zend_Entity_IdentityMapException('No such collection: ' . $fieldName);
        }

        return $this->_relatedObjects[$h][$fieldName];
    }
}
