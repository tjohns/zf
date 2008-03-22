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
 * @package    Zend_Build
 * @subpackage Zend_Build_Resource
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Interface.php 3412 2007-02-14 22:22:35Z darby $
 */

/**
 * @category   Zend
 * @package    Zend_Build_Resource
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface Zend_Build_Resource_Interface extends Zend_Build_Configurable
{
    /**
     * Returns true if an instance of this resource has been updated since it was created with CLI, false otherwise.
     *
     * @throws Zend_Build_Profile_Resource_Exception If authentication cannot be performed
     */
    public function updated ();

	/**
     * Returns true if an instance of this resource already exists in this project, false otherwise.
     *
     * @throws Zend_Build_Profile_Resource_Exception If authentication cannot be performed
     */
    public function exists ();

    /**
     * Gets the parent of this resource instance
     */
    public function getParent ();

    /**
     * Gets the children of this resource instance
     */
    public function getChildren ();

    /**
     * Adds a child to the end of the list of children for this resource
     */
    public function addChild (Zend_Build_Resource_Interface $child);

    /**
     * Removes a child from the list of children for this resource and returns the new list of children
     * 
     * @return array New list of children with $child removed
     */
    public function removeChild (Zend_Build_Resource_Interface $child);

    /**
     * Adds all children to the end of the list of children for this resource
     */
    public function addAllChildren (array $children);

    /**
     * Removes all children from the list of children for this resource and returns all removed children
     * 
     * @return array All children removed from this build resource
     */
    public function removeAllChildren ();
    
    /**
     * Gets the type of this resource instance
     */
    public function getType ();
}
