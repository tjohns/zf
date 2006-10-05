<?php
require_once 'Zend/XmlRpc/Response.php';

/**
 * HTTP response
 * 
 * @uses Zend_XmlRpc_Response
 * @package Zend_XmlRpc
 * @version $Id$
 */
class Zend_XmlRpc_Response_Http extends Zend_XmlRpc_Response
{
    /**
     * Override __toString() to send HTTP Content-Type header
     * 
     * @return string
     */
    public function __toString()
    {
        if (!headers_sent()) {
            header('Content-Type: application/xml; charset=utf-8');
        }

        return parent::__toString();
    }
}
