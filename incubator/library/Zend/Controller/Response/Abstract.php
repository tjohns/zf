<?php
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
     * Array of headers. Each header is an array with keys 'name' and 'value'
     * @var array
     */
    protected $_headers = array();

    /**
     * Body content
     * @var string
     */
    protected $_body = '';

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

        return $this->_body;
    }
}
