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

class Zend_Entity_Mapper_IdentityMap
{
    /**
     * HashMap to keep loaded objects only once.
     *
     * @var array
     */
    protected $_loadedObjects = array();

    public function addObject($entityInterface, $key, Zend_Entity_Interface $entity)
    {
        $this->_loadedObjects[$entityInterface][$key] = $entity;
    }

    public function hasObject($entityInterface, $key)
    {
        if(isset($this->_loadedObjects[$entityInterface][$key])) {
            return true;
        }
        return false;
    }

    public function getObject($entityInterface, $key)
    {
        return $this->_loadedObjects[$entityInterface][$key];
    }

    /**
     * Clear this identity map
     *
     * @return void
     */
    public function clear()
    {
        $this->_loadedObjects = array();
    }
}