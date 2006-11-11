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
     * Array of headers. Each header is an array with keys 'name' and 'value'
     * @var array
     */
    protected $_headers = array();

    /**
     * Whether or not to render exceptions; off by default
     * @var boolean 
     */
    protected $_renderExceptions = false;

    /**
     * Set a header
     *
     * If $replace is true, replaces any headers already defined with that
     * $name.
     *
     * @param string $name
     * @param string $value
     * @param boolean $replace
     * @return self
     */
    public function setHeader($name, $value, $replace = false)
    {
        $name  = (string) $name;
        $value = (string) $value;

        if ($replace) {
            foreach ($this->_headers as $key => $header) {
                if ($name == $header['name']) {
                    unset($this->_headers[$key]);
                }
            }
        }

        $this->_headers[] = array(
            'name'  => $name,
            'value' => $value
        );

        return $this;
    }

    /**
     * Return array of headers; see {@link $_headers} for format
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->_headers;
    }

    /**
     * Clear headers
     *
     * @return self
     */
    public function clearHeaders()
    {
        $this->_headers = array();

        return $this;
    }

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
        if (!headers_sent()) {
            foreach ($this->_headers as $header) {
                header($header['name'] . ': ' . $header['value']);
            }
        }

        if ($this->isException() && $this->renderExceptions()) {
            return $this->getException()->__toString();
        }

        return $this->_body;
    }
}
