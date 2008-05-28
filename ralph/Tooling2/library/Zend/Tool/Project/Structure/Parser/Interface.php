<?php

interface Zend_Tool_Project_Structure_Parser_Interface
{
    
    public function serialize(Zend_Tool_Project_Structure_Graph $graph);
    public function unserialize($data);
    
}