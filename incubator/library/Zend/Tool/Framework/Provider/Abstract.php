<?php

require_once 'Zend/Tool/Framework/Provider/Interface.php';

/**
 * This is a convenience class.
 * 
 * At current it will return the request and response from the client registry
 * as they are the more common things that will be needed by providers
 *
 */
abstract class Zend_Tool_Framework_Provider_Abstract implements Zend_Tool_Framework_Provider_Interface
{
    
    /**
     * Return the request object
     *
     * @return Zend_Tool_Framework_Client_Request
     */
    protected function _getRequest()
    {
        return Zend_Tool_Framework_Registry::getInstance()->getRequest();
    }
    
    /**
     * Return the response object
     *
     * @return Zend_Tool_Framework_Client_Response
     */
    protected function _getResponse()
    {
        return Zend_Tool_Framework_Registry::getInstance()->getResponse();
    }
    
}
