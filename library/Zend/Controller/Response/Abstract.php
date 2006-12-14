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
     * @var array
     */
    protected $_body = array();

    /**
     * Exception stack
     * @var Exception
     */
    protected $_exceptions = array();

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
     * @return Zend_Controller_Response_Abstract
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
     * @return Zend_Controller_Response_Abstract
     */
    public function clearHeaders()
    {
        $this->_headers = array();

        return $this;
    }

    /**
     * Send all headers
     * 
     * @return void
     */
    public function sendHeaders()
    {
        if (!headers_sent()) {
            foreach ($this->_headers as $header) {
                if ('status' == strtolower($header['name'])) {
                    // 'status' headers indicate an HTTP status, and need to be 
                    // handled slightly differently
                    header(ucfirst(strtolower($header['name'])) . ': ' . $header['value'], null, (int) $header['value']);
                } else {
                    header($header['name'] . ': ' . $header['value']);
                }
            }
        }
    }

    /**
     * Set body content
     *
     * If body content already defined, this will replace it.
     *
     * @param string $content
     * @return Zend_Controller_Response_Abstract
     */
    public function setBody($content)
    {
        $this->_body = array((string) $content);
        return $this;
    }

    /**
     * Append content to the body content
     *
     * @param string $content
     * @return Zend_Controller_Response_Abstract
     */
    public function appendBody($content)
    {
        $this->_body[] = (string) $content;
        return $this;
    }

    /**
     * Return the body content
     *
     * @param boolean $asArray Whether or not to return the body content as an 
     * array of strings or as a single string; defaults to false
     * @return string|array
     */
    public function getBody($asArray = false)
    {
        if ($asArray) {
            return $this->_body;
        }

        ob_start();
        $this->outputBody();
        return ob_get_clean();
    }

    /**
     * Echo the body segments
     * 
     * @return void
     */
    public function outputBody()
    {
        foreach ($this->_body as $content) {
            echo $content;
        }
    }

    /**
     * Register an exception with the response
     * 
     * @param Exception $e 
     * @return Zend_Controller_Response_Abstract
     */
    public function setException(Exception $e)
    {
        $this->_exceptions[] = $e;
        return $this;
    }

    /**
     * Retrieve the exception stack
     * 
     * @return array
     */
    public function getException()
    {
        return $this->_exceptions;
    }

    /**
     * Has an exception been registered with the response?
     * 
     * @return boolean
     */
    public function isException()
    {
        return !empty($this->_exceptions);
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
        $this->sendHeaders();

        if ($this->isException() && $this->renderExceptions()) {
            $exceptions = '';
            foreach ($this->getException() as $e) {
                $exceptions .= $e->__toString() . "\n";
            }
            return $exceptions;
        }

        ob_start();
        $this->outputBody();
        return ob_get_clean();
    }
}
