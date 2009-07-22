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
 * Resource for initializing the locale
 *
 * @uses       Zend_Application_Resource_Base
 * @category   Zend
 * @package    Zend_Application
 * @subpackage Resource
 * @author     Dolf Schimmel
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Application_Resource_Router extends Zend_Application_Resource_Base
{
    /**
     * @var Zend_Controller_Router_Rewrite
     */
    protected $_router;

    /**
     * Defined by Zend_Application_Resource_IResource
     *
     * @return void
     */
    public function init()
    {
        $options = $this->getOptions();
        if(!isset($options['route'])) {
            $options['route'] = array();
        }

        $this->getBootstrap()->bootstrap('Frontcontroller');
        $this->_router = $this->getBootstrap()->frontController;
        $this->_router->addConfig($options['routes']);
    }

    /**
     * Retrieve router object
     *
     * @return Zend_Controller_Router_Rewrite
     */
    public function getRouter()
    {
        return $this->_router;
    }
}
