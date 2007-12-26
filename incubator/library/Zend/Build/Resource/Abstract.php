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
 * @package    Zend_Build_Resource
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Interface.php 3412 2007-02-14 22:22:35Z darby $
 */

/**
 * @category   Zend
 * @package    Zend_Build_Resource
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Build_Resource_Abstract implements Zend_Build_Resource_Interface
{
    public $name;
    protected $_parent;
    protected $_children;
    public function __construct ($name, array $children = array())
    {
        $this->name = $name;
        $this->addAllChildren($children);
    }

    /**
     * Returns true if an instance of this resource has been updated since it was created with CLI, false otherwise.
     *
     * @throws Zend_Build_Profile_Resource_Exception If authentication cannot be performed
     */
    public function updated ()
    {}

	/**
     * Returns true if an instance of this resource already exists in this project, false otherwise.
     *
     * @throws Zend_Build_Profile_Resource_Exception If authentication cannot be performed
     */
    public function exists ()
    {}

    /**
     * Creates this instance of the resource in a project
     *
     * @throws Zend_Build_Profile_Resource_Exception If authentication cannot be performed
     */
    public function create ()
    {}

    /**
     * Deletes this instance of this resource in a project
     *
     * @throws Zend_Build_Profile_Resource_Exception If authentication cannot be performed
     */
    public function delete ()
    {}

    /**
     * Gets the parent of this resource instance
     */
    public function getParent ()
    {
        return $this->_parent;
    }

    /**
     * Removes the parent from this instance of build resource
     */
    protected function removeParent (Zend_Build_Resource_Interface $child)
    {
        $child->_parent = NULL;
    }

    /**
     * Gets the children of this resource instance
     */
    public function getChildren ()
    {
        return $this->_children;
    }

    /**
     * Adds a child to the end of the list of children for this resource
     */
    public function addChild (Zend_Build_Resource_Interface $child)
    {
        $child->_parent = $this;
        $this->_children[] = $child;
    }

    /**
     * Removes a child from the list of children for this resource and returns the new list of children
     * 
     * @return array New list of children with $child removed
     */
    public function removeChild (Zend_Build_Resource_Interface $child)
    {
        $child->removeParent();
        $this->_children = array_diff($_children, array($child));
        return $_children;
    }

    /**
     * Adds all children to the end of the list of children for this resource
     */
    public function addAllChildren (array $children)
    {
        foreach ($children as $child) {
            $this->addChild($child);
        }
    }

    /**
     * Removes all children from the list of children for this resource and returns all removed children
     * 
     * @return array All children removed from this build resource
     */
    public function removeAllChildren ()
    {
        $removed_children = $this->_children;
        foreach ($removed_children as $child) {
            $child . removeParent();
        }
        $this->_children = array();
        return $removed_children;
    }

    /**
     * Gets the type of this resource instance
     */
    public function getType ()
    {
        get_class($this);
    }
    
    protected function readChecksum ()
    {}
    
    protected function writeChecksum ()
    {}

    /**
     * Default implementation of toString for all build resources
     */
    public function __toString ()
    {
        return $this->name;
    }

    /**
     * Default implementation of equals should work for all resources
     */
    public function __equals ($other)
    {
        if (! isset($other))
            return false;
        if ($this->getType() != $other->getType())
            return false;
        if ($this != $other)
            return false;
        foreach ($this->getChildren() as $key => $child) {
            $other_children = $other . getChildren();
            if (! isset($other_children) || ! array_key_exists($key, $other_children))
                return false;
            if ($child != $other_children[$key])
                return false;
        }
        return true;
    }
}
