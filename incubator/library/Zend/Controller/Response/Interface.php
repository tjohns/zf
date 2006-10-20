<?php
/**
 * Zend_Controller_Response_Interface 
 *
 * Response interface for controller classes
 * 
 * @package Zend_Controller
 * @subpackage Response
 */
interface Zend_Controller_Response_Interface
{
    /**
     * Set a header
     *
     * By default, creates a new header; if $replace is true, if another
     * header of the same $name exists, replaces it.
     *
     * @param string $name
     * @param string $value
     * @param boolean $replace
     */
    public function setHeader($name, $value, $replace = false);

    /**
     * Retrieve all headers
     *
     * @return array
     */
    public function getHeaders();

    /**
     * Set the response body; replaces any body previously set
     *
     * @param string $content (Could be anything; suggested usage is
     * string content)
     */
    public function setBody($content);

    /**
     * Append $content to current body content
     *
     * @param string $content (Could be anything; suggested usage is
     * string content)
     */
    public function appendBody($content);

    /**
     * Retrieve body content
     *
     * @return string (Could return anything; this is the suggested
     * usage)
     */
    public function getBody();

    /**
     * Require a __toString() method so that it can be echoed
     *
     * @return string
     */
    public function __toString();
}
