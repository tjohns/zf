<?php
/** Zend_Controller_Response_Abstract */
require_once 'Zend/Controller/Response/Abstract.php';

/**
 * Zend_Controller_Response_Cli
 *
 * CLI response for controllers
 * 
 * @uses Zend_Controller_Response_Abstract
 * @package Zend_Controller
 * @subpackage Response
 */
class Zend_Controller_Response_Cli extends Zend_Controller_Response_Abstract
{
    /**
     * Return string representation of response
     *
     * Overrides abstract method as it does not need to output headers
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->getBody();
    }
}
