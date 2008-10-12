<?php

class ZendL_Tool_Rpc_Provider_Signature
{
    
    protected $_provider = null;
    protected $_name = null;
    protected $_specialties = array();
    protected $_actionableMethods = array();
    protected $_actions = array();

    
    /**
     * @var ReflectionClass
     */
    protected $_providerReflection = null;
    
    public function __construct(ZendL_Tool_Rpc_Provider_Interface $provider)
    {
        $this->_provider = $provider;
        $this->_providerReflection = new ReflectionClass($provider);
        $this->_process();
    }
    
    public function getName()
    {
        return $this->_name;
    }
    
    public function getProvider()
    {
        return $this->_provider;
    }
    
    public function getSpecialties()
    {
        return $this->_specialties;
    }
    
    public function getActionableMethods()
    {
        return $this->_actionableMethods;
    }
    
    public function getActionableMethod($actionName)
    {
        foreach ($this->_actionableMethods as $actionableMethodName => $actionableMethod) {
            if ($actionName == $actionableMethod['actionName']) {
                return $actionableMethod;
            }
        }
        
        return false;
    }
    
    protected function _process()
    {
        $this->_processName();
        $this->_processSpecialties();
        $this->_processActionableMethods();
        //$this->_processMethodSignatures();
    }
    
    protected function _processName()
    {
        if (method_exists($this->_provider, 'getName')) {
            $this->_name = $this->_provider->getName();
        }
        
        if ($this->_name == null) {
            $className = get_class($this->_provider);
            $name = substr($className, strrpos($className, '_')+1);
            $name = preg_replace('#(Tool|Provider|Manifest)$#', '', $name);
            $this->_name = $name;
        }
    }
    
    protected function _processSpecialties()
    {
        $specialties = array();
        
        if ($this->_providerReflection->hasMethod('getSpecialties')) {
            $specialties = $this->_provider->getSpecialties();
            if (!is_array($specialties)) {
                throw new ZendL_Tool_Rpc_Exception('Provider ' . get_class($this->_provider) . ' must return an array for method getSpecialties().');
            }
        } else {
            $defaultProperties = $this->_providerReflection->getDefaultProperties();
            $specialties = (isset($defaultProperties['_specialties'])) ? $defaultProperties['_specialties'] : array();
            if (!is_array($specialties)) {
                throw new ZendL_Tool_Rpc_Exception('Provider ' . get_class($this->_provider) . '\'s property $_specialties must be an array.');
            }
        }
        
        $this->_specialties = array_merge(array('_Global'), $specialties);
        
    }
    
    protected function _processActionableMethods()
    {
        
        $specialtyRegex = '#(.*)(' . implode('|', $this->_specialties) . ')$#i';
                
        
        $methods = $this->_providerReflection->getMethods();

        $actionableMethods = array();
        foreach ($methods as $method) {
            
            $methodName = $method->getName();
            
            if (!$method->getDeclaringClass()->isInstantiable() || !$method->isPublic() || $methodName[0] == '_') {
                continue;
            }
            
            $actionableName = ucfirst($methodName);
            
            if (substr($actionableName, -6) == 'Action') {
                $actionableName = substr($actionableName, 0, -6);
            }
            
            $actionableMethods[$methodName]['methodName'] = $methodName;
            
            if (preg_match($specialtyRegex, $actionableName, $matches)) {
                $actionableMethods[$methodName]['actionName'] = $matches[1];
                $actionableMethods[$methodName]['specialty'] = $matches[2];
            } else {
                $actionableMethods[$methodName]['actionName'] = $actionableName;
                $actionableMethods[$methodName]['specialty'] = '_Global';
            }
            
            $actionableMethods[$methodName]['action'] = ZendL_Tool_Rpc_Provider_Registry::getInstance()->getAction($actionableMethods[$methodName]['actionName']);

            if (!in_array($actionableMethods[$methodName]['action'], $this->_actions)) {
                $this->_actions[] = $actionableMethods[$methodName]['action'];
            }
            
            $parameterInfo = array();
            $position = 1;
            foreach ($method->getParameters() as $parameter) {
                $currentParam = $parameter->getName();
                $parameterInfo[$currentParam]['position']    = $position++;
                $parameterInfo[$currentParam]['optional']    = $parameter->isOptional();
                $parameterInfo[$currentParam]['default']     = ($parameter->isOptional()) ? $parameter->getDefaultValue() : null;
                $parameterInfo[$currentParam]['name']        = $currentParam;
                $parameterInfo[$currentParam]['type']        = 'string';
                $parameterInfo[$currentParam]['description'] = null;
            }

            if (($docComment = $method->getDocComment()) != '' && 
                (preg_match_all('/@param\s+(\w+)+\s+(\$\S+)\s+(.*?)(?=(?:\*\s*@)|(?:\*\/))/s', $docComment, $matches)))
            {
                for ($i=0; $i <= count($matches[0])-1; $i++) {
                    $currentParam = ltrim($matches[2][$i], '$');
                    
                    if ($currentParam != '' && isset($parameterInfo[$currentParam])) {
                        
                        $parameterInfo[$currentParam]['type'] = $matches[1][$i];
                        
                        $descriptionSource = $matches[3][$i];
                        
                        if ($descriptionSource != '') {
                            $parameterInfo[$currentParam]['description'] = trim($descriptionSource);
                        }
                        
                    }
    
                }
    
            }
            
            $actionableMethods[$methodName]['parameterInfo'] = $parameterInfo;
            
        }
        
        $this->_actionableMethods = $actionableMethods;
    }    

}