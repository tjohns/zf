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
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Zend_Controller_Response_Abstract
 *
 * Base class for Zend_Controller responses
 *
 * @package Zend_Controller
 * @subpackage Response
 */
abstract class Zend_Controller_Response_Abstract
{
    /**
     * Body content
     * @var string
     */
    protected $_body = '';

    /**
     * Exception
     * @var Exception
     */
    protected $_exception = null;

    /**
     * Whether or not to render exceptions; off by default
     * @var boolean 
     */
    protected $_renderExceptions = false;

    /**
     * Set body content
     *
     * If body content already defined, this will replace it.
     *
     * @param string $content
     * @return self
     */
    public function setBody($content)
    {
        $this->_body = (string) $content;
        return $this;
    }

    /**
     * Append content to the body content
     *
     * @param string $content
     * @return self
     */
    public function appendBody($content)
    {
        $this->_body .= (string) $content;
        return $this;
    }

    /**
     * Return the body content
     *
     * @return string
     */
    public function getBody()
    {
        return $this->_body;
    }

    /**
     * Register an exception with the response
     * 
     * @param Exception $e 
     * @return self
     */
    public function setException(Exception $e)
    {
        $this->_exception = $e;
        return $this;
    }

    /**
     * Retrieve the exception object, if set
     * 
     * @return null|Exception
     */
    public function getException()
    {
        return $this->_exception;
    }

    /**
     * Has an exception been registered with the response?
     * 
     * @return boolean
     */
    public function isException()
    {
        return $this->_exception instanceof Exception;
    }

    /**
     * Whether or not to render exceptions (off by default)
     *
     * If called with no arguments or a null argument, returns the value of the 
     * flag; otherwise, sets it and returns the current value.
     * 
     * @param boolean $flag Optional
     * @return boolean
     */
    public function renderExceptions($flag = null)
    {
        if (null !== $flag) {
            $this->_renderExceptions = $flag ? true : false;
        }

        return $this->_renderExceptions;
    }

    /**
     * Magic __toString functionality
     *
     * Sends all headers prior to returning the string
     *
     * @return string
     */
    public function __toString()
    {
        if ($this->isException() && $this->renderExceptions()) {
            return $this->getException()->getTraceAsString();
        }

        return $this->_body;
    }
}
