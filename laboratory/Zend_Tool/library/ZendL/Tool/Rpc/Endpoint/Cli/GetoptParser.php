<?php

class ZendL_Tool_Rpc_Endpoint_Cli_GetoptParser
{
    
    /**
     * @var ZendL_Tool_Rpc_Endpoint_Cli
     */
    protected $_endpoint = null;
    
    protected $_arguments = null;
    protected $_workingArguments = null;
    
    protected $_validActions = array();
    protected $_validProviders = array();
    

    public function __construct(ZendL_Tool_Rpc_Endpoint_Cli $endpoint, Array $arguments)
    {
        $this->_endpoint = $endpoint;
        
        $this->_arguments = $arguments;
        $this->_workingArguments = $arguments;

    }
    
    public function parse()
    {
        $endpointRequest = $this->_endpoint->getRequest();
        
        if ($this->_workingArguments[0] == $_SERVER["SCRIPT_NAME"]) {
            array_shift($this->_workingArguments);
        }
        
        if (!$this->_parseGlobalPart() || (count($this->_workingArguments) == 0)) {
            // @todo process global options?
            return;
        }
        
        $actionName = array_shift($this->_workingArguments);

        
        // is the action name valid?
        $cliActionNameMetadatas = ZendL_Tool_Rpc_Manifest_Registry::getInstance()->getMetadatas(array('type' => 'Action', 'name' => 'cliActionName'));
        foreach ($cliActionNameMetadatas as $cliActionNameMetadata) {
            if ($actionName == $cliActionNameMetadata->getValue()) {
                $action = $cliActionNameMetadata->getReference();
                break;
            }
        }
        
        $endpointRequest->setActionName($action->getName());
        
        /* @TODO Action Parameter Requirements */
        
        if (count($this->_workingArguments) == 0) {
            return;
        }
        
        if (!$this->_parseActionPart() || (count($this->_workingArguments) == 0)) {
            return;
        }
        
        $cliProviderName = array_shift($this->_workingArguments);
        $cliSpecialtyName = '_global';
        
        if (strstr($cliProviderName, '.')) {
            list($cliProviderName, $cliSpecialtyName) = explode('.', $cliProviderName);
        }
        
        $cliProviderNameMetadatas = ZendL_Tool_Rpc_Manifest_Registry::getInstance()->getMetadatas(array('type'=>'Provider', 'name' => 'cliProviderName'));
        
        foreach ($cliProviderNameMetadatas as $cliProviderNameMetadata) {
            if ($cliProviderName == $cliProviderNameMetadata->getValue()) {
                $provider = $cliProviderNameMetadata->getReference();
                break;
            }
        }
        
        $endpointRequest->setProviderName($provider->getName());
        
        $cliSpecialtyNameMetadatas = ZendL_Tool_Rpc_Manifest_Registry::getInstance()->getMetadatas(array('type'=>'Provider', 'providerName' => $provider->getName(), 'name' => 'cliSpecialtyNames'));
        
        foreach ($cliSpecialtyNameMetadatas as $cliSpecialtyNameMetadata) {
            if ($cliSpecialtyName == $cliSpecialtyNameMetadata->getValue()) {
                $specialtyName = $cliSpecialtyNameMetadata->getSpecialtyName();
                break;
            }
        }
        
        $endpointRequest->setSpecialtyName($specialtyName);
        
        
        
        
        
        
        
        $cliActionableMethodLongParameterMetadata = ZendL_Tool_Rpc_Manifest_Registry::getInstance()->getMetadata(array(
            'type'=>'Provider', 
            'providerName' => $provider->getName(), 
            'actionName' => $action->getName(), 
            'specialtyName' => $specialtyName, 
            'name' => 'cliActionableMethodLongParameters'
            )); 

        $cliActionableMethodShortParameterMetadata = ZendL_Tool_Rpc_Manifest_Registry::getInstance()->getMetadata(array(
            'type'=>'Provider', 
            'providerName' => $provider->getName(), 
            'actionName' => $action->getName(), 
            'specialtyName' => $specialtyName, 
            'name' => 'cliActionableMethodShortParameters'
            )); 
            
        $cliParameterNameShortValues = $cliActionableMethodShortParameterMetadata->getValue();
        
        $getoptOptions = array();
        foreach ($cliActionableMethodLongParameterMetadata->getValue() as $parameterNameLong => $cliParameterNameLong) {
            $optionConfig = $cliParameterNameLong . '|';
            
            $cliActionableMethodReferenceData = $cliActionableMethodLongParameterMetadata->getReference();
            
            if ($cliActionableMethodReferenceData['type'] == 'string' || $cliActionableMethodReferenceData['type'] == 'bool') {
                $optionConfig .= $cliParameterNameShortValues[$parameterNameLong] . (($cliActionableMethodReferenceData['optional']) ? '-' : '=') . 's';
            } elseif (in_array($cliActionableMethodReferenceData['type'], array('int', 'integer', 'float'))) {
                $optionConfig .= $cliParameterNameShortValues[$parameterNameLong] . (($cliActionableMethodReferenceData['optional']) ? '-' : '=') . 'i';
            } else {
                $optionConfig .= $cliParameterNameShortValues[$parameterNameLong] . '-s';
            }

            $getoptOptions[$optionConfig] = ($cliActionableMethodReferenceData['description'] != '') ? $cliActionableMethodReferenceData['description'] : 'No description available.';
        }
        
        $getoptParser = new ZendL_Console_Getopt($getoptOptions, $this->_workingArguments, array('parseAll' => false));
        $getoptParser->parse();
        foreach ($getoptParser->getOptions() as $option) {
            $value = $getoptParser->getOption($option);
            $endpointRequest->setProviderParameter($option, $value);
        }
        
        /*
        Zend_Debug::dump($getoptParser); 
        Zend_Debug::dump($endpointRequest);
        die();
        */        

        $this->_workingArguments = $getoptParser->getRemainingArgs();
        return;
    }
    
    public function getActionName()
    {
        return $this->_actionName;
    }
    
    public function getActionOptions()
    {
        
    }
    
    public function getProviderName()
    {
        return $this->_providerName;
    }
    
    public function getProviderOptions()
    {
        
    }
    
    protected function _parseGlobalPart()
    {
        
        return true;
    }
    
    protected function _parseActionPart()
    {
        
        return true;
    }
    
    protected function _parseProviderPart()
    {
        
        return true;
    }
    
}