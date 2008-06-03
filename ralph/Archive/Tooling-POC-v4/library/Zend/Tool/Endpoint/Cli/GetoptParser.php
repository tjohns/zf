<?php

class Zend_Tool_Endpoint_Cli_GetoptParser
{
    
    /**
     * @var Zend_Tool_Endpoint_Cli
     */
    protected $_endpoint = null;
    
    protected $_arguments = null;
    protected $_workingArguments = null;
    
    /*
    protected $_globalOptions = null;
    protected $_actionOptions = null;
    protected $_providerOptions = null;
    */    

    public function __construct(Zend_Tool_Endpoint_Cli $endpoint, Array $arguments)
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
            return;
        }
        
        $actionName = array_shift($this->_workingArguments);
        
        $endpointRequest->setActionName($actionName);
        
        $action = $this->_endpoint->getManifest()->getAction($endpointRequest->getActionName());
        
        /* @TODO Action Parameter Requirements */
        
        if (count($this->_workingArguments) == 0) {
            return;
        }
        
        if (!$this->_parseActionPart() || (count($this->_workingArguments) == 0)) {
            return;
        }
        
        $providerName = array_shift($this->_workingArguments);
        
        $endpointRequest->setFullProviderName($providerName);
        
        $provider = $this->_endpoint->getManifest()->getProvider($endpointRequest->getProviderName());

        $paramRequirements = $provider->getParameterRequirements($endpointRequest->getActionName(), $endpointRequest->getProviderSpecialty());
        
        $getoptOptions = array();
        foreach ($paramRequirements as $parameterName => $parameterRequirement) {
            $optionConfig = $parameterRequirement['longName'] . '|';
            
            if ($parameterRequirement['type'] == 'string' || $parameterRequirement['type'] == 'bool') {
                $optionConfig .= $parameterRequirement['shortName'] . (($parameterRequirement['optional']) ? '-' : '=') . 's';
            } elseif (in_array($parameterRequirement['type'], array('int', 'integer', 'float'))) {
                $optionConfig .= $parameterRequirement['shortName'] . (($parameterRequirement['optional']) ? '-' : '=') . 'i';
            } else {
                $optionConfig .= $parameterRequirement['shortName'] . '-s';
            }

            $getoptOptions[$optionConfig] = ($parameterRequirement['description'] != '') ? $parameterRequirement['description'] : 'No description available.';
        }
        
        $getoptParser = new Zend_Console_Getopt($getoptOptions, $this->_workingArguments, array('parseAll' => false));
        $getoptParser->parse();
        foreach ($getoptParser->getOptions() as $option) {
            $value = $getoptParser->getOption($option);
            $endpointRequest->setProviderParameter($option, $value);
        }

        
        $this->_workingArguments = $getoptParser->getRemainingArgs();
        return;
        
        Zend_Debug::dump($getoptParser); 
        Zend_Debug::dump($endpointRequest);
        die();
        
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