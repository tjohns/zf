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
 * @package    Zend_Controller
 * @subpackage Router
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */ 


/** Zend_Controller_Router */
require_once 'Zend/Controller/Router.php';

/**
 * Module-aware basic router
 *
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage Router
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Controller_ModuleRouter extends Zend_Controller_Router
{
    /**
     * Module name, if found
     * @var string
     */
    protected $_module;

    /**
     * Split path segments from request object
     * 
     * @param Zend_Controller_Request_Abstract $request 
     * @return array
     * @throws Zend_Controller_Router_Exception with invalid request
     */
    public function getPathSegs(Zend_Controller_Request_Abstract $request)
    {
        if (!$request instanceof Zend_Controller_Request_Http) {
            throw new Zend_Controller_Router_Exception('Zend_Controller_Router requires a Zend_Controller_Request_Http-based request object');
        }

        $pathInfo = $request->getPathInfo();
        $pathSegs = explode('/', trim($pathInfo, '/'));

        $controllerDir = $this->getFrontController()->getControllerDirectory();
        $modules       = array();
        foreach ($controllerDir as $key => $dir) {
            if (!is_numeric($key) && ('default' != $key)) {
                $modules[] = $key;
            }
        }

        /**
         * Retrieve module if valid
         */
        if (isset($pathSegs[0]) && !empty($pathSegs[0]) && in_array($pathSegs[0], $modules)) {
            $this->_module = array_shift($pathSegs);
        }

        return $pathSegs;
    }

    /**
     * Route a request
     *
     * Routes requests of the format /module/controller/action by default. 
     * Action may always be omitted.  If the module does not exist as a key in 
     * the controller directory array, it will not be populated in the request 
     * object. Additional parameters may be specified as key/value pairs
     * separated by the directory separator: 
     * /controller/action/key/value/key/value. 
     *
     * @param Zend_Controller_Request_Abstract $request 
     * @return void
     */
    public function route(Zend_Controller_Request_Abstract $request)
    {
        parent::route($request);

        /**
         * Set module, if found
         */
        if (null !== $this->_module) {
            $request->setModuleName(urldecode($this->_module));
        }

        return $request;
    }
}
