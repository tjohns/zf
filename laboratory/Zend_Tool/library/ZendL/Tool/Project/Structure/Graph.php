<?php

class ZendL_Tool_Project_Structure_Graph
{
    
    protected static $_traverseEnabled = false;
    
    protected $_topNodes = array();

    
    
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
     * @param unknown_type $searchParameters
     * @return ZendL_Tool_Project_Structure_Node
     */
    public function findNodeByContext($searchParameters)
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
        
        $foundNode = null;
        
        while ($currentSearchParam = array_shift($orderedSearchParameters)) {
            
            if (!$foundNode) {
                
                foreach ($this->_topNodes as $node) {
                    $foundNode = $this->_recursiveFindNode($node, $currentSearchParam['contextName'], $currentSearchParam['contextParams']);
                    if ($foundNode) {
                        break;
                    }
                }
                
            } else {
                $foundNode = $this->_recursiveFindNode($foundNode, $currentSearchParam['contextName'], $currentSearchParam['contextParams']);
            }
            
        }
        
        return $foundNode;
    }
    
    protected function _recursiveFindNode(ZendL_Tool_Project_Structure_Node $searchNode, $searchContextName, $searchContextParams)
    {
        
        $searchNodeIterator = new RecursiveIteratorIterator($searchNode, RecursiveIteratorIterator::SELF_FIRST);
        
        foreach ($searchNodeIterator as $currentNode) {
            if (strtolower($currentNode->getContext()->getName()) == strtolower($searchContextName)) {
                
                /* @todo this needs to be reworked b/c parameters are not persisted. */
                
                if ($searchContextParams) {
                    
                    foreach ($searchContextParams as $searchContextParamName => $searchContextParamValue) {
                        $searchContextParamGetterName = 'get' . $searchContextParamName;
                        if (!method_exists($currentNode->getContext(), $searchContextParamGetterName) ||
                            $currentNode->getContext()->{$searchContextParamGetterName}() != $searchContextParamValue) 
                        {
                            continue 2;
                        }
                    }
                    
                    return $currentNode;
                } else {
                    return $currentNode;                    
                }

            }
        }
        
        return false;
    }
    
    public function append(ZendL_Tool_Project_Structure_Node $node)
    {
        $this->_topNodes[$node->getName()] = $node;
    }
    
    public function __set($name, $value)
    {
        $this->_topNodes[$name] = $value;
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

    public function __call($methodName, $arguments)
    {
        foreach ($this->_topNodes as $topNode) {
            call_user_func_array(array($topNode, $methodName), $arguments);
        }
    }

    public function getTopNodes()
    {
        return $this->_topNodes;
    }
    
}    