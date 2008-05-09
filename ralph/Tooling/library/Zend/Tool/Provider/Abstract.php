<?php

abstract class Zend_Tool_Provider_Abstract
{
    /**
     * @var string
     */
    protected $_name     = null;
    
    /**
     * @var Zend_Tool_Manifest
     */
    protected $_manifest = null;
    
    /**
     * @var Zend_Tool_Endpoint_Request
     */
    protected $_request  = null;
    
    /**
     * @var Zend_Tool_Endpoint_Response
     */
    protected $_response = null;
    
    /**
     * @var Zend_Tool_Provider_Action
     */
    protected $_action   = null;
    
    /**
     * @var array
     */
    protected $_specialties = array();
    
    private $_actionableMethods = null;
    
    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }
    
    public function getName()
    {
        if ($this->_name == null) {
            $className = get_class($this);
            $this->_name = substr($className, strrpos($className, '_')+1);
        }
        
        return $this->_name;
    }
    
    public function setManifest(Zend_Tool_Manifest $manifest)
    {
        $this->_manifest = $manifest;
        return $this;
    }
    
    public function getManifest()
    {
        return $this->_manifest;
    }
    
    public function setRequest(Zend_Tool_Endpoint_Request $request)
    {
        $this->_request = $request;
        return $this;
    }
    
    public function setResponse(Zend_Tool_Endpoint_Response $response)
    {
        $this->_response = $response;
        return $this;
    }
    
    /**
     * Enter description here...
     *
     * @return array
     */
    public function getSpecialties()
    {
        return array_merge(array('_Global'), $this->_specialties);
    }
    
    public function execute($actionName, $providerSpecialty = null)
    {
        // get action object
        
        $actionMethod = $actionName;
        
        if ($providerSpecialty != '') {
            $actionMethod .= $providerSpecialty;
        }
        
        if (method_exists($this, $actionMethod)) {
            call_user_func_array(array($this, $actionMethod), $this->_request->getProviderParameters());
        } elseif (method_exists($this, $actionMethod . 'Action')) {
            $actionMethod .= 'Action';
            call_user_func_array(array($this, $actionMethod), $this->_request->getProviderParameters());
        } else {
            throw new Zend_Tool_Exception('Not a supported method.');
        }


    }
    
    public function getActions()
    {
        $actionSpecialtyMethods = $this->_getActionableMethods();

        $actions = array();
        foreach ($actionSpecialtyMethods as $actionSpecialtyMethod) {
            if (!in_array($actionSpecialtyMethod['action'], $actions)) {
                $actions[] = $actionSpecialtyMethod['action'];
            }
        }
        
        sort($actions);

        return $actions;
    }

    public function getParameterRequirements($actionName = null, $specialtyName = null)
    {
        if ($actionName instanceof Zend_Tool_Provider_Action) {
            $actionName = $actionName->getName();
        }
        
        $actionableMethods = $this->_getActionableMethods();
        
        if ($specialtyName == '') {
            $specialtyName = '_Global';
        }
        
        $dispatchableMethod = null;
        
        foreach ($actionableMethods as $actionableMethodName => $actionableMethod) {
            if (strtolower($actionName) == strtolower($actionableMethod['action']) && strtolower($specialtyName) == strtolower($actionableMethod['specialty'])) {
                $dispatchableMethodName = $actionableMethodName;
                break;
            }
        }
        
        if ($dispatchableMethodName) {
            return $this->_getParameterInformation($dispatchableMethodName);
        } else {
            throw new Zend_Tool_Exception('No method could satisfy the action name ' . $actionName);
        }
        
    }
    
    private function _getActionableMethods()
    {
        $this->_parseActionableMethods();
        return $this->_actionableMethods;

    }

    private function _parseActionableMethods()
    {
        $reflector = new ReflectionClass($this);
        $methods = $reflector->getMethods();
        
        // regex case insensitive
        $specialtyRegex = '#(.*)(' . implode('|', $this->getSpecialties()) . ')$#i';
        
        $actionableMethods = array();
        foreach ($methods as $method) {
            
            $methodName = $method->getName();
            
            if (!($method->getDeclaringClass()->isInstantiable() && $method->isPublic()) && $methodName[0] != '_') {
                continue;
            }
            
            $actionableMethods[$methodName]['reflectionMethod'] = $method;
            $actionableMethods[$methodName]['methodName'] = $method->getName(); 
            
            if (substr($actionableMethods[$methodName]['methodName'], -6) == 'Action') {
                $actionableMethods[$methodName]['methodName'] = substr($actionableMethods[$methodName]['methodName'], 0, -6);
            }
            
            if (preg_match($specialtyRegex, $actionableMethods[$methodName]['methodName'], $matches)) {
                $actionableMethods[$methodName]['action'] = $matches[1];
                $actionableMethods[$methodName]['specialty'] = $matches[2];
            } else {
                $actionableMethods[$methodName]['action'] = $actionableMethods[$methodName]['methodName'];
                $actionableMethods[$methodName]['specialty'] = '_Global';
            }
            
        }
        
        $this->_actionableMethods = $actionableMethods;
    }
    
    private function _getParameterInformation($methodName)
    {
        $this->_parseActionableMethods();
        
        $targetMethod = $this->_actionableMethods[$methodName];
        
        if (isset($targetMethod[$methodName]['parameterInformation'])) {
            return $targetMethod[$methodName]['parameterInformation'];
        }
        
        $parameterInfo = array();
        $position = 1;
        foreach ($targetMethod['reflectionMethod']->getParameters() as $parameter) {
            $currentParam = $parameter->getName();
            $parameterInfo[$currentParam]['position']    = $position++;
            $parameterInfo[$currentParam]['optional']    = $parameter->isOptional();
            $parameterInfo[$currentParam]['shortName']   = strtolower($currentParam[0]);
            $parameterInfo[$currentParam]['longName']    = $currentParam;
            $parameterInfo[$currentParam]['type']        = 'string';
            $parameterInfo[$currentParam]['description'] = null;
        }
        
        if (($docComment =  $targetMethod['reflectionMethod']->getDocComment()) != '' && 
            (preg_match_all('/@param\s+(\w+)+\s+(\$\S+)\s+(.*?)(?=(?:\*\s*@)|(?:\*\/))/s', $docComment, $matches)))
        {
            for ($i=0; $i <= count($matches[0])-1; $i++) {
                $currentParam = ltrim($matches[2][$i], '$');
                
                if ($currentParam != '' && isset($parameterInfo[$currentParam])) {
                    
                    $parameterInfo[$currentParam]['type'] = $matches[1][$i];
                    
                    $descriptionSource = $matches[3][$i];
                    
                    // find longName and shortName
                    if (preg_match('/shortName=(\w)/', $descriptionSource, $nameMatches)) {
                        $parameterInfo[$currentParam]['shortName'] = $nameMatches[1];
                        $descriptionSource = str_replace($nameMatches[0], '', $descriptionSource);
                    }

                    if (preg_match('/longName=(\w+)/', $descriptionSource, $nameMatches)) {
                        $parameterInfo[$currentParam]['longName'] = $nameMatches[1];
                        $descriptionSource = str_replace($nameMatches[0], '', $descriptionSource);
                    }
                    
                    if ($descriptionSource != '') {
                        $parameterInfo[$currentParam]['description'] = trim($descriptionSource);
                    }
                    
                }

            }

        }
        

        $this->_actionableMethods[$methodName]['parameterInfo'] = $parameterInfo;

        return $this->_actionableMethods[$methodName]['parameterInfo'];
    }
    
    private function _parseDocblock()
    {
        
    }

}
