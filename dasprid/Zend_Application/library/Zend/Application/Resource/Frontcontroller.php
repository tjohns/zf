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
 * Front Controller resource
 *
 * @category   Zend
 * @package    Zend_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Application_Resource_Frontcontroller extends Zend_Application_Resource_Base
{
    /**
     * Initialize Front Controller
     * 
     * @return void
     */
    public function init()
    {
        $this->_front = Zend_Controller_Front::getInstance();
        foreach ($this->getOptions() as $key => $value) {
            switch (strtolower($key)) {
                case 'controllerdirectory':
                    if (is_string($value)) {
                        $this->_front->setControllerDirectory($value);
                    } elseif (is_array($value)) {
                        foreach ($value as $module => $directory) {
                            $this->_front->setControllerDirectory($directory, $module);
                        }
                    }
                    break;
                case 'modulecontrollerdirectoryname':
                    $this->_front->setModuleControllerDirectoryName($value);
                    break;
                case 'moduledirectory':
                    $this->_front->addModuleDirectory($value);
                    break;
                case 'defaultcontrollername':
                    $this->_front->setDefaultControllerName($value);
                    break;
                case 'defaultaction':
                    $this->_front->setDefaultAction($value);
                    break;
                case 'defaultmodule':
                    $this->_front->setDefaultModule($value);
                    break;
                case 'baseurl':
                    $this->_front->setBaseUrl($value);
                    break;
                case 'params':
                    $this->_front->setParams($value);
                    break;
                case 'plugins':
                    foreach ((array) $value as $pluginClass) {
                        $plugin = new $pluginClass();
                        $this->_front->registerPlugin($plugin);
                    }
                    break;
            }
        }
        $this->getBootstrap()->frontController = $this->_front;
    }
}
