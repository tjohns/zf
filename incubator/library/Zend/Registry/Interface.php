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
 * @package    Zend_Registry
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */ 


/**
 * @category   Zend
 * @package    Zend_Registry
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface Zend_Registry_Interface
{
    /**
     * Registers a shared object.
     *
     * @todo use SplObjectStorage if ZF minimum PHP requirement moves up to at least PHP 5.1.0
     *
     * @param   string      $name The name for the object.
     * @param   object      $obj  The object to register.
     * @throws  Zend_Registry_Exception
     * @return  void
     */
	public function set($name,$obj);

    /**
     * Retrieves a registered shared object, where $name is the
     * registered name of the object to retrieve.
     *
     * If the $name argument is NULL, an array will be returned where 
	 * the keys to the array are the names of the objects in the registry 
	 * and the values are the class names of those objects.
     *
     * @see     register()
     * @param   string      $name The name for the object.
     * @throws  Zend_Registry_Exception
     * @return  object      The registered object.
     */
    public function get($name = NULL);
    
    /**
     * Returns TRUE if the $name is a named object in the
     * registry, or FALSE if $name was not found in the registry.
     *
     * @param  string $name
     * @return boolean
     */
    public function has($name);
    

}

