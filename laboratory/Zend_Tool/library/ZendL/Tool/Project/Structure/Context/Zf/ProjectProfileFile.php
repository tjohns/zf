<?php

class ZendL_Tool_Project_Structure_Context_Zf_ProjectProfileFile extends ZendL_Tool_Project_Structure_Context_Filesystem_File 
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
        $parser = new ZendL_Tool_Project_Structure_Parser_Xml();
        $isTraverseEnabled = ZendL_Tool_Project_Structure_Graph::isTraverseEnabled();
        ZendL_Tool_Project_Structure_Graph::setTraverseEnabled(true);
        $xml = $parser->serialize($this->_graph);
        ZendL_Tool_Project_Structure_Graph::setTraverseEnabled($isTraverseEnabled);
        return $xml;
    }
    
}