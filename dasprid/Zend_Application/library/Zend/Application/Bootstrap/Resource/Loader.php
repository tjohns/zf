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
 * @category  Zend
 * @package   Zend_Application
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 * @version   $Id$
 */

/**
 * @see Zend_Application_Bootstrap_Resource_Base
 */
require_once 'Zend/Application/Bootstrap/Resource/Base.php';

/**
 * Resource for registering the auto loader
 *
 * @category  Zend
 * @package   Zend_Application
 * @uses      Zend_Application_Bootstrap_Resource_Base
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Application_Bootstrap_Resource_Loader extends Zend_Application_Bootstrap_Resource_Base
{
    /**
     * Class for the loader
     *
     * @var string
     */
    protected $_class = 'Zend_Loader';
    
    /**
     * Wether the loader is enabled by default
     *
     * @var boolean
     */
    protected $_enabled = true;
    
    /**
     * Set the name for the loader class
     *
     * @param  string $class
     * @return Zend_Application_Plugin_Loader
     */    
    public function setClass($class)
    {
        $this->_class = $class;
        return $this;    
    }
    
    /**
     * Set wether the loader is enabled by default or not
     *
     * @param  boolean $enabled
     * @return Zend_Application_Plugin_Loader
     */    
    public function setEnabled($enabled)
    {
        $this->_enabled = (bool) $enabled;
        return $this;    
    }
    
    /**
     * Defined by Zend_Application_Plugin
     *
     * @return void
     */
    public function init()
    {
        require_once 'Zend/Loader.php';
        Zend_Loader::registerAutoload($this->_class, $this->_enabled);
    }
}
