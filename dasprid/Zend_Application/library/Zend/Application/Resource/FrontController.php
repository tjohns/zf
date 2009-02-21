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
class Zend_Application_Resource_FrontController
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
                    break;
                case 'modulecontrollerdirectoryname':
                    break;
                case 'moduledirectory':
                    break;
                case 'defaultcontrollername':
                    break;
                case 'defaultaction':
                    break;
                case 'defaultmodule':
                    break;
                case 'baseurl':
                    break;
                case 'params':
                    break;
                case 'plugins':
                    break;
            }
        }
        $this->getBootstrap()->frontController = $this->_front;
    }
}
