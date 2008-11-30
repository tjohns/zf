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
 * Plugin for settings view options
 *
 * @category  Zend
 * @package   Zend_Application
 * @uses      Zend_Application_Plugin
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Application_Plugin_View extends Zend_Application_Plugin
{
    /**
     * Options for the view
     *
     * @var array
     */
    protected $_options = array();

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
     * Defined by Zend_Application_Plugin
     *
     * @return void
     */
    public function init()
    {
        require_once 'Zend/View.php';
        $view = new Zend_View($this->_options);

        require_once 'Zend/Controller/Action/Helper/ViewRenderer.php';
        $viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer();
        $viewRenderer->setView($view);
        
        require_once 'Zend/Controller/Action/HelperBroker.php';
        Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);
    }
}
