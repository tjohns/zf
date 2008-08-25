<?php

class ZendL_Tool_Rpc_Endpoint_Dispatcher
{
    /**
     * @var ZendL_Tool_Rpc_Endpoint_Request
     */
    protected $_request = null;
    
    /**
     * @var ZendL_Tool_Rpc_Endpoint_Response
     */
    protected $_response = null;
    
    public function setRequest(ZendL_Tool_Rpc_Endpoint_Request $request)
    {
        $this->_request = $request;
        return $this;
    }
    
    public function setResponse(ZendL_Tool_Rpc_Endpoint_Response $response)
    {
        $this->_response = $response;
        return $this;
    }
    
    public function dispatch()
    {
    
        $providerSignature = ZendL_Tool_Rpc_Provider_Registry::getInstance()->getProviderSignature($this->_request->getProviderName());
        $provider = $providerSignature->getProvider();        
        $actionName = $this->_request->getActionName();

        if (!$actionableMethod = $providerSignature->getActionableMethod($actionName)) {
            require_once 'ZendL/Tool/Rpc/Endpoint/Exception.php';
            throw new ZendL_Tool_Rpc_Endpoint_Exception('Dispatcher error - actionable method not found');         
        }
        
        $methodName       = $actionableMethod['methodName'];
        $methodParameters = $actionableMethod['parameterInfo'];

        $requestParameters = $this->_request->getProviderParameters();
        
        // @todo This seems hackish, determine if there is a better way
        $callParameters = array();
        foreach ($methodParameters as $methodParameterName => $methodParameterValue) {
            $callParameters[] = (array_key_exists($methodParameterName, $requestParameters)) ? $requestParameters[$methodParameterName] : $methodParameterValue['default'];
        }
        
        if (($specialtyName = $this->_request->getSpecialtyName()) != '_Global') {
            $methodName .= $specialtyName;
        }
        
        if (method_exists($provider, $methodName)) {
            call_user_func_array(array($provider, $methodName), $callParameters);
        } elseif (method_exists($provider, $methodName . 'Action')) {
            $method .= 'Action';
            call_user_func_array(array($provider, $methodName), $callParameters);
        } else {
            require_once 'ZendL/Tool/Rpc/Endpoint/Exception.php';
            throw new ZendL_Tool_Rpc_Endpoint_Exception('Not a supported method.');
        }
    }
    
}