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
 * @package    Zend_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @see Zend_Application_Resource_Base
 */
require_once 'Zend/Application/Resource/Base.php';

/**
 * Resource for setting session options
 *
 * @uses       Zend_Application_Resource_Base
 * @category   Zend
 * @package    Zend_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Application_Resource_Session extends Zend_Application_Resource_Base
{
    /**
     * Save handler to use
     *
     * @var Zend_Session_SaveHandler_Interface
     */
    protected $_saveHandler = null;
        
    /**
     * Set session save handler
     *
     * @param  mixed $saveHandler
     * @return Zend_Application_Resource_Session
     * @throws Zend_Application_Resource_Exception When $saveHandler is no valid save handler
     */
    public function setSaveHandler($saveHandler)
    {
        if (is_string($saveHandler)) {
            $this->_saveHandler = new $saveHandler();
        }
        
        if ($saveHandler instanceof Zend_Session_SaveHandler_Interface) {
            $this->_saveHandler = $saveHandler;
        } else {
            throw new Zend_application_Resource_Exception('Invalid session save handler');
        }
    }
    
    /**
     * Defined by Zend_Application_Plugin
     *
     * @return void
     */
    public function init()
    {
        if (count($this->_options) > 0) {
            Zend_Session::setOptions($this->_options);
        }
        
        if ($this->_saveHandler !== null) {
            Zend_Session::setSaveHandler($this->_saveHandler);
        }
    }
}
