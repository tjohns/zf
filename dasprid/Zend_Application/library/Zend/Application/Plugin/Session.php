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
 * @see Zend_Application_Plugin
 */
require_once 'Zend/Application/Plugin.php';

/**
 * Plugin for setting session options
 *
 * @category  Zend
 * @package   Zend_Application
 * @uses      Zend_Application_Plugin
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Application_Plugin_Session extends Zend_Application_Plugin
{
    /**
     * Options for sessions
     *
     * @var array
     */
    protected $_options = array();
    
    /**
     * Save handler to use
     *
     * @var Zend_Session_SaveHandler_Interface
     */
    protected $_saveHandler = null;
    
    /**
     * Set options from array
     *
     * @param  array $options Configuration for Zend_Session
     * @return Zend_Application_Plugin_Session
     */
    public function setOptions(array $options)
    {
        $this->_options = $options;

        return parent::setOptions($options);
    }
    
    /**
     * Set session save handler
     *
     * @param Zend_Session_SaveHandler_Interface $saveHandler
     */
    public function setSaveHandler(Zend_Session_SaveHandler_Interface $saveHandler)
    {
        $this->_saveHandler = $saveHandler;
    }
    
    /**
     * Defined by Zend_Application_Plugin
     *
     * @return void
     */
    public function init()
    {
        require_once 'Zend/Session.php';
        
        if (count($this->_options) > 0) {
            Zend_Session::setOptions($this->_options);
        }
        
        if ($this->_saveHandler !== null) {
            Zend_Session::setSaveHandler($this->_saveHandler);
        }
    }
}
