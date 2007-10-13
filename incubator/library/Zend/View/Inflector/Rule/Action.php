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
 * Transform an action name to a view script name
 *
 * @package    Zend_View
 * @subpackage Inflector
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_View_Inflector_Rule_Action implements Zend_View_Inflector_Rule_Interface
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
     * View script path specification string
     * @var string
     */
    protected $_pathSpec = ':action.:suffix';

    /**
     * @var Zend_Controller_Request_Abstract
     */
    protected $_request;

    /**
     * View script suffix
     * @var string
     */
    protected $_suffix   = 'phtml';

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
        $action     = $path;
        $suffix     = $this->getSuffix();

        foreach ($params as $key => $value) {
            switch ($key) {
                case 'action':
                case 'suffix':
                    $$key = (string) $value;
                    break;
                default:
                    break;
            }
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

        $wordDelimiters = $this->_wordDelimiters;
        $replacements = array(
            ':action'     => str_replace($wordDelimiters, '-', strtolower($action)),
            ':suffix'     => $suffix
        );
        $value = str_replace(array_keys($replacements), array_values($replacements), $this->_pathSpec);
        $value = preg_replace('/-+/', '-', $value);

        return $value;
    }

    /**
     * Retrieve script suffix
     *
     * @return string
     */
    public function getSuffix()
    {
        return $this->_suffix;
    }

    /**
     * Set script suffix
     *
     * @param  string $value
     * @return Zend_View_Inflector_Rule_ControllerAction
     */
    public function setSuffix($suffix)
    {
        $this->_suffix = (string) $suffix;
        return $this;
    }

}
