<?php
/** Zend_Controller_Response_Interface */
require_once 'Zend/Controller/Response/Interface.php';

/**
 * Zend_Controller_Response_Http 
 *
 * HTTP response for controllers
 * 
 * @uses Zend_Controller_Response_Interface
 * @package Zend_Controller
 * @subpackage Response
 */
class Zend_Controller_Response_Http implements Zend_Controller_Response_Interface
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
     * @return void
     */
    public function setHeader($name, $value, $replace = false)
    {
        $name  = (string) $name;
        $value = (string) $value;

        if ($replace) {
            $set = false;
            foreach ($this->_headers as $key => $header) {
                if ($name == $header['name']) {
                    $this->_headers[$key]['value'] = $value;
                    $set = true;
                }
            }
            if ($set) {
                return;
            }
        }

        $this->_headers[] = array(
            'name'  => $name,
            'value' => $value
        );
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
     * Set body content
     *
     * If body content already defined, this will replace it.
     * 
     * @param string $content 
     * @return void
     */
    public function setBody($content)
    {
        $this->_body = (string) $content;
    }

    /**
     * Append content to the body content
     * 
     * @param string $content 
     * @return void
     */
    public function appendBody($content)
    {
        $this->_body .= (string) $content;
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
