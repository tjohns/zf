<?php

class Zend_Tool_Rpc_Endpoint_Dispatcher
{
    
    public function setRequest(Zend_Tool_Rpc_Endpoint_Request $request)
    {
        $this->_request = $request;
        return $this;
    }
    
    public function setResponse(Zend_Tool_Rpc_Endpoint_Response $response)
    {
        $this->_response = $response;
        return $this;
    }
    
    public function dispatch()
    {
    
        $providerSignature = Zend_Tool_Rpc_Provider_Registry::getInstance()->getProviderSignature($this->_request->getProviderName());
        $provider = $providerSignature->getProvider();
        
        $method = $this->_request->getActionName();

        if (($specialtyName = $this->_request->getSpecialtyName()) != '_Global') {
            $method .= $specialtyName;
        }
        
        if (method_exists($provider, $method)) {
            call_user_func_array(array($provider, $method), $this->_request->getProviderParameters());
        } elseif (method_exists($provider, $method . 'Action')) {
            $method .= 'Action';
            call_user_func_array(array($provider, $method), $this->_request->getProviderParameters());
        } else {
            throw new Zend_Tool_Exception('Not a supported method.');
        }
    }
    
}