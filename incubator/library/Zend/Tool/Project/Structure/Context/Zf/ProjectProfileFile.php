<?php

class Zend_Tool_Project_Structure_Context_Zf_ProjectProfileFile extends Zend_Tool_Project_Structure_Context_Filesystem_File 
{

    protected $_filesystemName = '.zfproject.xml';
    
    protected $_graph = null;
    
    public function getName()
    {
        return 'ProjectProfileFile';
    }
    
    public function setGraph($graph)
    {
        $this->_graph = $graph;
    }
    
    public function getContents()
    {
        $parser = new Zend_Tool_Project_Structure_Parser_Xml();
        $isTraverseEnabled = Zend_Tool_Project_Structure_Graph::isTraverseEnabled();
        Zend_Tool_Project_Structure_Graph::setTraverseEnabled(true);
        $xml = $parser->serialize($this->_graph);
        Zend_Tool_Project_Structure_Graph::setTraverseEnabled($isTraverseEnabled);
        return $xml;
    }
    
}