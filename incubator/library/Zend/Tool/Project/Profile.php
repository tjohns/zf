<?php

class Zend_Tool_Project_Profile
{
    
    /**
     * @var bool
     */
    protected static $_traverseEnabled = false;
    
    /**
     * @var Zend_Tool_Project_Resource[]
     */
    protected $_topResources = array();

    
    
    public static function setTraverseEnabled($traverseEnabled)
    {
        self::$_traverseEnabled = (bool) $traverseEnabled;
    }
    
    public static function isTraverseEnabled()
    {
        return self::$_traverseEnabled;
    }
    
    public function __construct()
    {
    }
    
    /**
     * Enter description here...
     *
     * @param string|array $searchParameters
     * @return Zend_Tool_Project_Resource
     */
    public function findResourceByContext($searchParameters)
    {
        if (is_string($searchParameters)) {
            $searchParameters = array($searchParameters);
        }
        
        $orderedSearchParameters = array();
        $orderedSearchContextIndex = 0;
        
        foreach ($searchParameters as $searchParamName => $searchParamValue) {
            
            if (is_int($searchParamName)) {
                $orderedSearchParameters[$orderedSearchContextIndex]['contextName'] = $searchParamValue;
                $orderedSearchParameters[$orderedSearchContextIndex]['contextParams'] = array();
            } elseif (is_string($searchParamName) && is_array($searchParamValue)) {
                $orderedSearchParameters[$orderedSearchContextIndex]['contextName'] = $searchParamName;
                $orderedSearchParameters[$orderedSearchContextIndex]['contextParams'] = $searchParamValue;
            } else {
                throw new Exception('your search criteria doesnt make sense.');
            }
            
            $orderedSearchContextIndex++;
        }
        
        $foundResource = null;
        
        while ($currentSearchParam = array_shift($orderedSearchParameters)) {
            
            if (!$foundResource) {
                
                foreach ($this->_topResources as $resource) {
                    $foundResource = $this->_recursiveFindResource($resource, $currentSearchParam['contextName'], $currentSearchParam['contextParams']);
                    if ($foundResource) {
                        break;
                    }
                }
                
            } else {

                $foundResource = $this->_recursiveFindResource($foundResource, $currentSearchParam['contextName'], $currentSearchParam['contextParams']);

            }
            
        }
        
        return $foundResource;
    }
    
    protected function _recursiveFindResource(Zend_Tool_Project_Resource $searchResource, $searchContextName, $searchContextParams)
    {
        
        $searchResourceIterator = new RecursiveIteratorIterator($searchResource, RecursiveIteratorIterator::SELF_FIRST);
        
        foreach ($searchResourceIterator as $currentResource) {
            if (strtolower($currentResource->getContext()->getName()) == strtolower($searchContextName)) {
                
                /* @todo this needs to be reworked b/c parameters are not persisted. */
                
                if ($searchContextParams) {
                    
                    foreach ($searchContextParams as $searchContextParamName => $searchContextParamValue) {
                        $searchContextParamGetterName = 'get' . $searchContextParamName;
                        if (!method_exists($currentResource->getContext(), $searchContextParamGetterName) ||
                            $currentResource->getContext()->{$searchContextParamGetterName}() != $searchContextParamValue) 
                        {
                            continue 2;
                        }
                    }
                    
                    return $currentResource;
                } else {
                    return $currentResource;                    
                }

            }
        }
        
        return false;
    }
    
    public function append(Zend_Tool_Project_Resource $resource)
    {
        $this->_topResources[$resource->getName()] = $resource;
    }
    
    /*
    public function __set($name, $value)
    {
        $this->_topResources[$name] = $value;
    }
    
    public function __get($name)
    {
        // @todo implement this
    }
    
    public function __isset($name)
    {
        // @todo implement this
    }
    
    public function __unset($name)
    {
        // @todo implement this
    }
    */

    public function __call($methodName, $arguments)
    {
        foreach ($this->_topResources as $topResource) {
            call_user_func_array(array($topResource, $methodName), $arguments);
        }
    }

    public function getTopResources()
    {
        return $this->_topResources;
    }
    
}    