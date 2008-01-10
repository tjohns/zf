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
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage Action_Helper
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id$
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Controller_Action_Helper_Abstract */
require_once 'Zend/Controller/Action/Helper/Abstract.php';

/**
 * Simplify context switching based on requested format
 *
 * @uses       Zend_Controller_Action_Helper_Abstract
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage Action_Helper
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Controller_Action_Helper_ContextSwitch extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * Initially supported contexts
     * @var array
     */
    protected $_contexts = array( 
        'json' => array( 
            'suffix' => 'json.phtml', 
            'header' => array( 
                'type'    => 'Content-Type', 
                'content' => 'application/json' 
            ) 
        ), 
        'xml'  => array( 
            'suffix' => 'xml.phtml', 
            'header' => array( 
                'type'    => 'Content-Type', 
                'content' => 'text/xml' 
            ) 
        ), 
    ); 

    /**
     * Controller property key to utilize for context switching
     * @var string
     */
    protected $_contextKey = 'contexts';

    /**
     * Request parameter containing requested context
     * @var string
     */
    protected $_contextParam = 'format';

    /**
     * Current context
     * @var string
     */
    protected $_currentContext;
 
    /**
     * Default context (xml)
     * @var string
     */
    protected $_defaultContext = 'xml'; 
 
    /**
     * Whether or not to disable layouts when switching contexts
     * @var bool
     */
    protected $_disableLayout = true; 

    /**
     * @var Zend_Controller_Action_Helper_ViewRenderer
     */
    protected $_viewRenderer;

    /**
     * Strategy pattern: return object
     * 
     * @return Zend_Controller_Action_Helper_ContextSwitch
     */
    public function direct()
    {
        return $this;
    }
 
    /**
     * Initialize context detection and switching
     */ 
    public function initContext($format = null)
    {
        $this->_currentContext = null;

        $controller = $this->getActionController();
        $request    = $this->getRequest();
        $action     = $request->getActionName();
        $contextKey = $this->_contextKey;

        // Return if no context switching enabled, or no context switching 
        // enabled for this action
        if (!isset($controller->$contextKey)) {
            return;
        }
        $contexts = $controller->$contextKey;

        if (!is_array($contexts) 
            || !isset($contexts[$action]))
        {
            return;
        }

        // Return if no context parameter provided
        if (!$context = $request->getParam($this->getContextParam())) {
            return;
        }

        // Check if context allowed by action controller
        if (!in_array($context, (array) $contexts[$action])) {
            return;
        }

        // Return if invalid context parameter provided and no format or invalid 
        // format provided
        if (!isset($this->_contexts[$context]) 
            && (empty($format) || (!isset($this->_contexts[$format]))))
        {
            return;
        }

        // Use provided format if passed
        if (!empty($format) && isset($this->_contexts[$format])) {
            $context = $format;
        }

        $suffix = $this->getSuffix($context);
        if (!empty($suffix)) {
            $this->_getViewRenderer()->setViewSuffix($suffix);
        }

        $header = $this->getHeader($context);
        if (!empty($header)) {
            $this->getResponse()->setHeader($header['type'], $header['content']);
        }

        if ($this->getAutoDisableLayout()) {
            $layout = Zend_Layout::getMvcInstance();
            if (null !== $layout) {
                $layout->disableLayout();
            }
        }

        $this->_currentContext = $context;
    }
 
    /**
     * Customize view script suffix to use when switching context.
     * 
     * Passing an empty suffix value to the setters disables the view script
     * suffix change.
     *
     * @param  string $type   Context type for which to set suffix
     * @param  string $suffix Suffix to use
     * @return Zend_Controller_Action_Helper_ContextSwitch
     */ 
    public function setSuffix($type, $suffix)
    {
        if (!isset($this->_contexts[$type])) {
            require_once 'Zend/Controller/Action/Exception.php';
            throw new Zend_Controller_Action_Exception(sprintf('Cannot set suffix; invalid context type "%s"', $type));
        }

        if (empty($suffix)) {
            $suffix = null;
        }

        $this->_contexts[$type]['suffix'] = (string) $suffix;
        return $this;
    }

    /**
     * Retrieve suffix for given context type
     * 
     * @param  string $type Context type
     * @return string
     */
    public function getSuffix($type)
    {
        if (!isset($this->_contexts[$type])) {
            require_once 'Zend/Controller/Action/Exception.php';
            throw new Zend_Controller_Action_Exception(sprintf('Cannot retrieve suffix; invalid context type "%s"', $type));
        }

        return $this->_contexts[$type]['suffix'];
    }
 
    /**
     * Customize response header to use when switching context
     * 
     * Passing an empty header value to the setters disables the response
     * header.
     *
     * @param  string $type   Context type for which to set suffix
     * @param  string $header Header to set
     * @param  string $content Header content
     * @return Zend_Controller_Action_Helper_ContextSwitch
     */ 
    public function setHeader($type, $header = null, $content = '')
    {
        if (!isset($this->_contexts[$type])) {
            require_once 'Zend/Controller/Action/Exception.php';
            throw new Zend_Controller_Action_Exception(sprintf('Cannot set header; invalid context type "%s"', $type));
        }

        if (empty($header) || empty($content)) {
            $this->_contexts[$type]['header'] = null;
        } else {
            $this->_contexts[$type]['header'] = array(
                'type'    => $header,
                'content' => $content,
            );
        }

        return $this;
    }

    /**
     * Retrieve context header
     *
     * Returns a context header for the given type. Header is an array, with 
     * the following keys:
     * - 'type' is the header type/key
     * - 'content' is the header content
     * 
     * @param  string $type 
     * @return array
     */
    public function getHeader($type)
    {
        if (!isset($this->_contexts[$type])) {
            require_once 'Zend/Controller/Action/Exception.php';
            throw new Zend_Controller_Action_Exception(sprintf('Cannot retrieve header; invalid context type "%s"', $type));
        }

        return $this->_contexts[$type]['header'];
    }
 
    /**
     * Set name of parameter to use when determining context format
     *
     * @param  string $name
     * @return Zend_Controller_Action_Helper_ContextSwitch
     */ 
    public function setContextParam($name)
    {
        $this->_contextParam = (string) $name;
        return $this;
    }

    /**
     * Return context format request parameter name
     * 
     * @return string
     */
    public function getContextParam()
    {
        return $this->_contextParam;
    }
 
    /**
     * Indicate default context to use when no context format provided
     *
     * @param  string $type
     * @return Zend_Controller_Action_Helper_ContextSwitch
     */ 
    public function setDefaultContext($type)
    {
        if (!isset($this->_contexts[$type])) {
            require_once 'Zend/Controller/Action/Exception.php';
            throw new Zend_Controller_Action_Exception(sprintf('Cannot set default context; invalid context type "%s"', $type));
        }

        $this->_defaultContext = $type;
        return $this;
    }

    /**
     * Return default context
     * 
     * @return string
     */
    public function getDefaultContext()
    {
        return $this->_defaultContext;
    }
 
    /**
     * Set flag indicating if layout should be disabled
     *
     * @param  bool $flag
     * @return Zend_Controller_Action_Helper_ContextSwitch
     */ 
    public function setAutoDisableLayout($flag)
    {
        $this->_disableLayout = ($flag) ? true : false;
        return $this;
    }

    /**
     * Retrieve auto layout disable flag
     * 
     * @return bool
     */
    public function getAutoDisableLayout()
    {
        return $this->_disableLayout;
    }
 
    /**
     * Add new context
     *
     * @param  string $type Context type
     * @param  string $suffix View suffix to use with context
     * @param  string $headerName HTTP header to set for context
     * @param  string $headerContent Content for context HTTP header
     * @return Zend_Controller_Action_Helper_ContextSwitch
     */ 
    public function addContext($type, $suffix = null, $headerName = null, $headerContent = null)
    {
        $type = (string) $type;
        if (isset($this->_contexts[$type])) {
            require_once 'Zend/Controller/Action/Exception.php';
            throw new Zend_Controller_Action_Exception(sprintf('Cannot add context "%s"; already exists', $type));
        }

        $this->_contexts[$type] = array();
        $this->setSuffix($type, $suffix);
        $this->setHeader($type, $headerName, $headerContent);
        return $this;
    }

    /**
     * Overwrite existing context
     *
     * @param  string $type Context type
     * @param  string $suffix View suffix to use with context
     * @param  string $headerName HTTP header to set for context
     * @param  string $headerContent Content for context HTTP header
     * @return Zend_Controller_Action_Helper_ContextSwitch
     */ 
    public function setContext($type, $suffix = null, $headerName = null, $headerContent = null)
    {
        $type = (string) $type;
        $this->_contexts[$type] = array();
        $this->setSuffix($type, $suffix);
        $this->setHeader($type, $headerName, $headerContent);
        return $this;
    }

    /**
     * Retrieve context definitions
     * 
     * @return array
     */
    public function getContexts()
    {
        return $this->_contexts;
    }

    /**
     * Remove a context
     * 
     * @param  string $type 
     * @return Zend_Controller_Action_Helper_ContextSwitch
     */
    public function removeContext($type)
    {
        $type = (string) $type;
        if (isset($this->_contexts[$type])) {
            unset($this->_contexts[$type]);
        }

        return $this;
    }

    /**
     * Return current context, if any
     * 
     * @return null|string
     */
    public function getCurrentContext()
    {
        return $this->_currentContext;
    }

    /**
     * Retrieve ViewRenderer
     * 
     * @return Zend_Controller_Action_Helper_ViewRenderer
     */
    protected function _getViewRenderer()
    {
        if (null === $this->_viewRenderer) {
            $this->_viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        }

        return $this->_viewRenderer;
    }
}

