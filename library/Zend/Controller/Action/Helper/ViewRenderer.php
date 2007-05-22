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
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */ 

/** Zend_Controller_Action_Helper_Abstract */
require_once 'Zend/Controller/Action/Helper/Abstract.php';

/** Zend_View_Interface */
require_once 'Zend/View/Interface.php';

/**
 * View script integration
 *
 * Zend_Controller_Action_Helper_ViewRenderer provides transparent view 
 * integration for action controllers. It allows you to create a view object 
 * once, and populate it throughout all actions. Several global options may be 
 * set:
 *
 * - noController: if set true, render() will not look for view scripts in 
 *   subdirectories named after the controller
 * - viewSuffix: what view script filename suffix to use
 *
 * The helper autoinitializes the action controller view preDispatch(). It 
 * determines the path to the class file, and then determines the view base 
 * directory from there. It also uses the module name as a class prefix for 
 * helpers and views such that if your module name is 'Search', it will set the 
 * helper class prefix to 'Search_View_Helper' and the filter class prefix to 
 * 'Search_View_Filter'.
 *
 * Usage:
 * <code>
 * // In your bootstrap:
 * Zend_Controller_Action_HelperBroker::addHelper(new Wopnet_Controller_Action_Helper_Abstract());
 *
 * // In your action controller methods:
 * $viewHelper = $this->_helper->getHelper('view');
 *
 * // Don't use controller subdirectories
 * $viewHelper->setNoController(true);
 *
 * // Specify a different script to render:
 * $this->_helper->view('form');
 *
 * </code>
 * 
 * @uses       Zend_Controller_Action_Helper_Abstract
 * @package    Zend_Controller
 * @subpackage Zend_Controller_Action
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Controller_Action_Helper_ViewRenderer extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * @var Zend_View_Interface
     */
    public $view;

    /**
     * Front controller instance
     * @var Zend_Controller_Front
     */
    protected $_frontController;

    /**
     * Whether or not to autorender postDispatch; global setting (not reset at 
     * next invocation)
     * @var boolean
     */
    protected $_neverRender     = false;

    /**
     * Whether or not to use a controller name as a subdirectory when rendering
     * @var boolean
     */
    protected $_noController    = false;

    /**
     * Whether or not to autorender postDispatch; per controller/action setting (reset 
     * at next invocation)
     * @var boolean
     */
    protected $_noRender        = false;

    /**
     * Which named segment of the response to utilize
     * @var string
     */
    protected $_responseSegment = null;

    /**
     * Which action view script to render
     * @var string
     */
    protected $_scriptAction    = null;

    /**
     * Flag: has the view been initialized?
     * @var boolean
     */
    protected $_viewInitialized = false;

    /**
     * View script suffix
     * @var string
     */
    protected $_viewSuffix      = 'phtml';

    /**
     * Constructor
     *
     * Optionally set view object and options.
     * 
     * @param  Zend_View_Interface $view 
     * @param  array $options 
     * @return void
     */
    public function __construct(Zend_View_Interface $view = null, array $options = array())
    {
        if (null !== $view) {
            $this->setView($view);
        }

        if (!empty($options)) {
            $this->_setOptions($options);
        }
    }

    /**
     * Set the view object
     * 
     * @param Zend_View_Interface $view 
     * @return Zend_Controller_Action_Helper_ViewRenderer
     */
    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
        return $this;
    }

    /**
     * Retrieve front controller instance
     * 
     * @return Zend_Controller_Front
     */
    public function getFrontController()
    {
        if (null === $this->_frontController) {
            $this->_frontController = Zend_Controller_Front::getInstance();
        }

        return $this->_frontController;
    }

    /**
     * Generate a class prefix for helper and filter classes
     * 
     * @return string
     */
    protected function _generateDefaultPrefix()
    {
        if ((null === $this->_actionController) || !strstr(get_class($this->_actionController), '_')) {
            $prefix = 'Zend_View_Helper';
        } else {
            $class = get_class($this->_actionController);
            $prefix = substr($class, 0, strpos($class, '_')) . '_View';
        }

        return $prefix;
    }

    /**
     * Retrieve base path based on location of current action controller
     * 
     * @return string
     */
    protected function _getBasePath()
    {
        if (null === $this->_actionController) {
            return '.' . DIRECTORY_SEPARATOR . 'views';
        }

        $front      = $this->getFrontController();
        $modulePath = $front->getControllerDirectory($this->getRequest()->getModuleName());
        if (null === $modulePath) {
            throw new Zend_Controller_Action_Exception('Cannot determine view base path: invalid module "' . $module . '"in request');

        }
        $path = realpath($modulePath . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'views');
        return $path;
    }

    /**
     * Set options
     * 
     * @param  array $options 
     * @return Zend_Controller_Action_Helper_ViewRenderer
     */
    protected function _setOptions(array $options)
    {
        foreach ($options as $key => $value)
        {
            switch ($key) {
                case 'neverRender':
                case 'noController':
                case 'noRender':
                    $property = '_' . $key;
                    $this->{$property} = ($value) ? true : false;
                    break;
                case 'responseSegment':
                case 'scriptAction':
                case 'viewSuffix':
                    $property = '_' . $key;
                    $this->{$property} = (string) $value;
                    break;
                default:
                    break;
            }
        }

        return $this;
    }

    /**
     * Initialize the view object
     *
     * $options may contain the following keys:
     * - neverRender - flag dis/enabling postDispatch() autorender (affects all subsequent calls)
     * - noController - flag indicating whether or not to look for view scripts in subdirectories named after the controller
     * - noRender - flag indicating whether or not to autorender postDispatch()
     * - responseSegment - which named response segment to render a view script to
     * - scriptAction - what action script to render
     * - viewSuffix - what view script filename suffix to use
     * 
     * @param  string $path 
     * @param  string $prefix 
     * @param  array $options 
     * @return void
     */
    public function initView($path = null, $prefix = null, array $options = array())
    {
        if ($this->_viewInitialized) {
            return;
        }

        $this->_viewInitialized = true;

        if (null === $this->view) {
            $this->setView(new Zend_View());
        }

        if (null === $path) {
            $path = $this->_getBasePath();
        }

        if (null === $prefix) {
            $prefix = $this->_generateDefaultPrefix();
        }

        $this->view->addBasePath($path, $prefix);

        $this->_noRender        = false;
        $this->_responseSegment = null;
        $this->_scriptAction    = null;

        $this->_setOptions($options);

        if ((null !== $this->_actionController) && (null === $this->_actionController->view)) {
            $this->_actionController->view       = $this->view;
            $this->_actionController->viewSuffix = $this->_viewSuffix;
        }
    }

    /**
     * preDispatch - initialize view
     * 
     * @return void
     */
    public function preDispatch()
    {
        $this->initView();
    }

    /**
     * Set the neverRender flag (i.e., globally dis/enable autorendering)
     * 
     * @param  boolean $flag 
     * @return Zend_Controller_Action_Helper_ViewRenderer
     */
    public function setNeverRender($flag = true)
    {
        $this->_neverRender = ($flag) ? true : false;
        return $this;
    }

    /**
     * Retrieve neverRender flag value
     * 
     * @return boolean
     */
    public function getNeverRender()
    {
        return $this->_neverRender;
    }

    /**
     * Set the noRender flag (i.e., whether or not to autorender)
     * 
     * @param  boolean $flag 
     * @return Zend_Controller_Action_Helper_ViewRenderer
     */
    public function setNoRender($flag = true)
    {
        $this->_noRender = ($flag) ? true : false;
        return $this;
    }

    /**
     * Retrieve noRender flag value
     * 
     * @return boolean
     */
    public function getNoRender()
    {
        return $this->_noRender;
    }

    /**
     * Set the view script to use
     * 
     * @param  string $name 
     * @return Zend_Controller_Action_Helper_ViewRenderer
     */
    public function setScriptAction($name)
    {
        $this->_scriptAction = (string) $name;
        return $this;
    }

    /**
     * Retrieve view script name
     * 
     * @return string
     */
    public function getScriptAction()
    {
        return $this->_scriptAction;
    }

    /**
     * Set the response segment name
     * 
     * @param  string $name 
     * @return Zend_Controller_Action_Helper_ViewRenderer
     */
    public function setResponseSegment($name)
    {
        if (null === $name) {
            $this->_responseSegment = null;
        } else {
            $this->_responseSegment = (string) $name;
        }

        return $this;
    }

    /**
     * Retrieve named response segment name
     * 
     * @return string
     */
    public function getResponseSegment()
    {
        return $this->_responseSegment;
    }

    /**
     * Set the noController flag (i.e., whether or not to render into controller subdirectories)
     * 
     * @param  boolean $flag 
     * @return Zend_Controller_Action_Helper_ViewRenderer
     */
    public function setNoController($flag = true)
    {
        $this->_noController = ($flag) ? true : false;
        return $this;
    }

    /**
     * Retrieve noController flag value
     * 
     * @return boolean
     */
    public function getNoController()
    {
        return $this->_noController;
    }

    /**
     * Set view script suffix 
     * 
     * @param  string $suffix 
     * @return Zend_Controller_Action_Helper_ViewRenderer
     */
    public function setViewSuffix($suffix)
    {
        $this->_viewSuffix = (string) $suffix;
        return $this;
    }

    /**
     * Get view script suffix 
     * 
     * @return string
     */
    public function getViewSuffix()
    {
        return $this->_viewSuffix;
    }

    /**
     * Set options for rendering a view script
     * 
     * @param  string $action View script to render
     * @param  string $name Response named segment to render to
     * @param  boolean $noController Whether or not to render within a subdirectory named after the controller
     * @return Zend_Controller_Action_Helper_ViewRenderer
     */
    public function setRender($action = null, $name = null, $noController = false)
    {
        $this->setScriptAction($action)
             ->setResponseSegment($name)
             ->setNoController($noController);

        return $this;
    }

    /**
     * postDispatch - auto render a view
     *
     * Only autorenders if: 
     * - _noRender is false
     * - action controller is present
     * - request has not been re-dispatched (i.e., _forward() has not been called)
     * - response is not a redirect
     * 
     * @return void
     */
    public function postDispatch()
    {
        if (!$this->_noRender 
            && (null !== $this->_actionController)
            && $this->getRequest()->isDispatched()
            && !$this->getResponse()->isRedirect())
        {
            $this->_actionController->render($this->_scriptAction, $this->_responseSegment, $this->_noController);
        }

        $this->_viewInitialized = false;
    }

    /**
     * Use this helper as a method; proxies to setRender()
     * 
     * @param  string $action 
     * @param  string $name 
     * @param  boolean $noController 
     * @return void
     */
    public function direct($action = null, $name = null, $noController = false)
    {
        $this->setRender($action, $name, $noController);
    }
}
