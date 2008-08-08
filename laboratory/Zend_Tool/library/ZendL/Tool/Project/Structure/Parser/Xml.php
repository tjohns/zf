<?php

class ZendL_Tool_Project_Structure_Parser_Xml implements ZendL_Tool_Project_Structure_Parser_Interface
{
    
    protected $_graph = null;
    
    public function serialize(ZendL_Tool_Project_Structure_Graph $graph)
    {

        $graph = clone $graph;
        
        //$this->_graph = $graph;
        $xmlElement = new SimpleXMLElement('<projectProfile />');

        self::_serializeRecurser($graph->getTopNodes(), $xmlElement);            
        
        $doc = new DOMDocument('1.0');
        $doc->formatOutput = true;
        $domnode = dom_import_simplexml($xmlElement);
        $domnode = $doc->importNode($domnode, true);
        $domnode = $doc->appendChild($domnode);
        
        return $doc->saveXML();
    }
    
    /**
     * 
     * @return ZendL_Tool_Project_Structure_Graph
     */
    public function unserialize($data)
    {
        if ($data == null) {
            throw new Exception('contents not available to unserialize.');
        }
        
        $xmlDataIterator = new SimpleXMLIterator($data);

        if ($xmlDataIterator->getName() != 'projectProfile') {
            throw new Exception('Profiles must start with a projectProfile node');
        }

        $this->_graph = new ZendL_Tool_Project_Structure_Graph();
        
        $this->_unserializeRecurser($xmlDataIterator);
        
        return $this->_graph;
        
    }
        
    protected function _serializeRecurser($graphNodes, SimpleXmlElement $xmlNode)
    {
        // @todo find a better way to handle concurrency.. if no clone, _position in node gets messed up
        if ($graphNodes instanceof ZendL_Tool_Project_Structure_Node) {
            $graphNodes = clone $graphNodes;
        }
        
        foreach ($graphNodes as $graphNode) {
            
            if ($graphNode->isDeleted()) {
                continue;
            }
            
            $nodeName = $graphNode->getContext()->getName();
            $nodeName[0] = strtolower($nodeName[0]);
            
            $newNode = $xmlNode->addChild($nodeName);

            $reflectionClass = new ReflectionClass($graphNode->getContext());

            if ($graphNode->isEnabled() == false) {
                $newNode->addAttribute('enabled', 'false');
            }
            
            foreach ($graphNode->getPersistentParameters() as $paramName => $paramValue) {
                $newNode->addAttribute($paramName, $paramValue);
            }

            if ($graphNode->hasChildren()) {
                self::_serializeRecurser($graphNode, $newNode);
            }
            
        }

    }
    
    
    
    protected function _unserializeRecurser(SimpleXMLIterator $xmlIterator, ZendL_Tool_Project_Structure_Node $node = null)
    {
        
        foreach ($xmlIterator as $nodeName => $nodeData) {
            
            $context = ZendL_Tool_Project_Structure_Context_Registry::getInstance()->getContext($nodeName);
            $subNode = new ZendL_Tool_Project_Structure_Node($context);
            
            if ($attributes = $nodeData->attributes()) {
                foreach ($attributes as $attrName => $attrValue) {
                    $method = 'set' . $attrName;
                    if (method_exists($subNode, $method)) {
                        $subNode->{$method}((string) $attrValue);
                    } elseif (method_exists($context, $method)) {
                        $context->{$method}((string) $attrValue);
                    }
                }
            }
            
            if (method_exists($context, 'setGraph')) {
                $context->setGraph($this->_graph);
            }
            
            if ($node) {
                $node->append($subNode);
            } else {
                $this->_graph->append($subNode);
            }

            if ($xmlIterator->hasChildren()) {
                self::_unserializeRecurser($xmlIterator->getChildren(), $subNode);
            }
        }
    }
    
}