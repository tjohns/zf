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
 * @package    Zend_View
 * @subpackage Inflector
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/** Zend_View_Inflector_Rule_Interface */
require_once 'Zend/View/Inflector/Rule/Interface.php';

/**
 * Abstract inflector rule class with base functionality
 *
 * @package    Zend_View
 * @subpackage Inflector
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_View_Inflector_Rule_Abstract implements Zend_View_Inflector_Rule_Interface
{
    /**
     * @var Zend_Controller_Dispatcher_Interface
     */
    protected $_dispatcher;

    /**
     * @var Zend_Controller_Front
     */
    protected $_front;

    /**
     * Characters representing path delimiters in the controller
     * @var string|array
     */
    protected $_pathDelimiters;

    /**
     * Word delimiters
     * @var array
     */
    protected $_wordDelimiters;

    /**
     * Path specification string for inflection
     * @var string
     */
    protected $_pathSpec;

    /**
     * @var Zend_Controller_Request_Abstract
     */
    protected $_request;

    /**
     * Get front controller
     * 
     * @return Zend_Controller_Front
     */
    public function getFrontController()
    {
        if (null === $this->_front) {
            $this->_front = Zend_Controller_Front::getInstance();
        }

        return $this->_front;
    }

    /**
     * Get request object
     * 
     * @return Zend_Controller_Request_Abstract
     */
    public function getRequest()
    {
        if (null === $this->_request) {
            $front = $this->getFrontController();
            $this->_request = $front->getRequest();
        }

        return $this->_request;
    }

    /**
     * Get dispatcher
     * 
     * @return Zend_Controller_Dispatcher_Interface
     */
    public function getDispatcher()
    {
        if (null === $this->_dispatcher) {
            $front = $this->getFrontController();
            $this->_dispatcher = $front->getDispatcher();
        }

        return $this->_dispatcher;
    }

    /**
     * Transform a path name according to rules
     * 
     * @param  string $path 
     * @return string Inflected path
     */
    public function inflect($path, array $params = array())
    {
        $params = $this->getParams($path, $params);
        $replacements = $this->getReplacements($params);
        return $this->doReplacement($replacements);
    }

    abstract public function getParams($path, array $params = array());

    public function getReplacements(array $params)
    {
        $replacements = array();
        foreach ($params as $key => $value) {
            $method = 'inflect' . ucfirst(strtolower($key));
            if (method_exists($this, $method)) {
                $replacements[':' . strtolower($key)] = $this->$method($value);
            }
        }

        return $replacements;
    }

    public function doReplacement(array $replacements)
    {
        $value = str_replace(array_keys($replacements), array_values($replacements), $this->_pathSpec);
        return preg_replace('/-+/', '-', $value);
    }

    /**
     * Inflect the controller name
     * 
     * @param  string $controller 
     * @return string
     */
    public function inflectController($controller) 
    {
        $this->initDelimiters();
        return str_replace(
            $this->_wordDelimiters, 
            '-', 
            strtolower( str_replace($this->_pathDelimiters, '/', $controller)));
    }

    /**
     * Inflect the action name
     * 
     * @param  string $action 
     * @return string
     */
    public function inflectAction($action)
    {
        $this->initDelimiters();
        return str_replace($this->_wordDelimiters, '-', strtolower($action));
    }

    /**
     * Initialize path and word delimiters
     * 
     * @return void
     */
    public function initDelimiters()
    {
        if ((null !== $this->_pathDelimiters) && (null !== $this->_wordDelimiters)) {
            return;
        }

        // Module, controller, and action names need normalized delimiters
        if (null === $this->_pathDelimiters) {
            $this->_pathDelimiters = $this->getDispatcher()->getPathDelimiter();
        }
        if (null === $this->_wordDelimiters) {
            $dispatcher        = $this->getDispatcher();
            $wordDelimiters    = $dispatcher->getWordDelimiter();
            $pathDelimiters    = $dispatcher->getPathDelimiter();
            $this->_wordDelimiters = array_unique(array_merge($wordDelimiters, (array) $this->_pathDelimiters));
        }
    }
}
