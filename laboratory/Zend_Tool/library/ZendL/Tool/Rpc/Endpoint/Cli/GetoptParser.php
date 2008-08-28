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
        
        // check to see if the first cli arg is the script name
        if ($this->_workingArguments[0] == $_SERVER["SCRIPT_NAME"]) {
            array_shift($this->_workingArguments);
        }
        
        // process global options
        if (!$this->_parseGlobalPart() || (count($this->_workingArguments) == 0)) {
            // @todo process global options?
            return;
        }
        
        // the next "word" should be the action name
        $cliWordActionName = array_shift($this->_workingArguments);

        // is the action name valid?
        $cliActionNameMetadatas = ZendL_Tool_Rpc_Manifest_Registry::getInstance()->getMetadatas(array('type' => 'Action', 'name' => 'cliActionName'));
        foreach ($cliActionNameMetadatas as $cliActionNameMetadata) {
            if ($cliWordActionName == $cliActionNameMetadata->getValue()) {
                $action = $cliActionNameMetadata->getReference();
                break;
            }
        }

        // if no action, handle error
        if (!isset($action)) {
            require_once 'ZendL/Tool/Rpc/Endpoint/Exception.php';
            throw new ZendL_Tool_Rpc_Endpoint_Exception('Action \'' . $cliWordActionName . '\' is not a valid action.');
        }
        
        // prepare action request name
        $endpointRequest->setActionName($action->getName());
        
        /* @TODO Action Parameter Requirements */
        
        // make sure there are more "words" on the command line
        if (count($this->_workingArguments) == 0) {
            return;
        }
        
        // get the cli "word" as the provider name from command line
        $cliWordProvider = array_shift($this->_workingArguments);
        $cliWordProviderSpecialty = '_global';
        
        // if there is notation for specialties? If so, break them up
        if (strstr($cliWordProvider, '.')) {
            list($cliWordProvider, $cliWordProviderSpecialty) = explode('.', $cliWordProvider);
        }
        
        // get the cli provider names from the manifest
        $cliProviderNameMetadatas = ZendL_Tool_Rpc_Manifest_Registry::getInstance()->getMetadatas(array('type'=>'Provider', 'name' => 'cliProviderName'));
        
        // find the provider signature for the given cli word provider name 
        foreach ($cliProviderNameMetadatas as $cliProviderNameMetadata) {
            if ($cliWordProvider == $cliProviderNameMetadata->getValue()) {
                $providerSignature = $cliProviderNameMetadata->getReference();
                break;
            }
        }
        
        // if no provider signature found, handle error
        if (!isset($providerSignature)) {
            require_once 'ZendL/Tool/Rpc/Endpoint/Exception.php';
            throw new ZendL_Tool_Rpc_Endpoint_Exception('Provider \'' . $cliWordProvider . '\' is not a valid provider.');
        }
        
        // prepare provider request name
        $endpointRequest->setProviderName($providerSignature->getName());
        
        $cliSpecialtyNameMetadatas = ZendL_Tool_Rpc_Manifest_Registry::getInstance()->getMetadatas(array('type'=>'Provider', 'providerName' => $providerSignature->getName(), 'name' => 'cliSpecialtyNames'));
        
        foreach ($cliSpecialtyNameMetadatas as $cliSpecialtyNameMetadata) {
            if ($cliWordProviderSpecialty == $cliSpecialtyNameMetadata->getValue()) {
                $specialtyName = $cliSpecialtyNameMetadata->getSpecialtyName();
                break;
            }
        }
        
        $endpointRequest->setSpecialtyName($specialtyName);
        
        $cliActionableMethodLongParameterMetadata = ZendL_Tool_Rpc_Manifest_Registry::getInstance()->getMetadata(array(
            'type'=>'Provider', 
            'providerName' => $providerSignature->getName(), 
            'actionName' => $action->getName(), 
            'specialtyName' => $specialtyName, 
            'name' => 'cliActionableMethodLongParameters'
            )); 

        $cliActionableMethodShortParameterMetadata = ZendL_Tool_Rpc_Manifest_Registry::getInstance()->getMetadata(array(
            'type'=>'Provider', 
            'providerName' => $providerSignature->getName(), 
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