<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to version 1.0 of the Zend Framework
 * license, that is bundled with this package in the file LICENSE.txt, and
 * is available through the world-wide-web at the following URL:
 * http://framework.zend.com/license/new-bsd. If you did not receive
 * a copy of the Zend Framework license and are unable to obtain it
 * through the world-wide-web, please send a note to license@zend.com
 * so we can mail you a copy immediately.
 *
 * @package    Zend_View
 * @subpackage Helpers
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id$
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_View_Helper_Placeholder_Container */
require_once 'Zend/View/Helper/Placeholder/Container.php';

/**
 * Registry for placeholder containers
 *
 * @package    Zend_View
 * @subpackage Helpers
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */ 
class Zend_View_Helper_Placeholder_Registry
{
    /**
     * Default container class
     * @var string
     */
    protected $_containerClass = 'Zend_View_Helper_Placholder_Container';

    /**
     * Placeholder containers
     * @var array
     */
    protected $_items = array();

    /**
     * createContainer 
     * 
     * @param mixed $key 
     * @param array $value 
     * @return void
     */
    public function createContainer($key, array $value)
    {
        $key = (string) $key;
        $this->_items[$key] = new $this->_containerClass(array());
        return $this->_items[$key];
    }

    /**
     * Retrieve a placeholder container
     * 
     * @param  string $key 
     * @return Zend_View_Helper_Placeholder_Container
     */
    public function getContainer($key)
    {
        $key = (string) $key;
        if (isset($this->_items[$key])) {
            return $this->_items[$key];
        }

        $container = $this->createContainer($key);

        return $container;
    }

    /**
     * Set the container for an item in the registry
     * 
     * @param  stirng $key 
     * @param  Zend_View_Placeholder_Container $container 
     * @return Zend_View_Placeholder_Registry
     */
    public function setContainer($key, Zend_View_Placeholder_Container $container)
    {
        $key = (string) $key;
        $this->_items[$key] = $container;
        return $this;
    }

    /**
     * Set the container class to use
     * 
     * @param  string $name 
     * @return Zend_View_Helper_Placeholder_Registry
     */
    public function setContainerClass($name)
    {
        $this->_containerClass = $name;
        return $this;
    }

    /**
     * Retrieve the container class
     * 
     * @return string
     */
    public function getContainerClass()
    {
        return $this->_containerClass;
    }
}
