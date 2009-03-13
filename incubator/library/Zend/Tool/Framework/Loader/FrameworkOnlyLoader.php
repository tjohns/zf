<?php

require_once 'Zend/Tool/Framework/Loader/Abstract.php';

class Zend_Tool_Framework_Loader_FrameworkOnlyLoader extends Zend_Tool_Framework_Loader_Abstract 
{
    
    protected function _getFiles()
    {
        $files = array();
        
        $files[] = realpath('Zend/Tool/Framework/System/Manifest.php');
        var_dump($files);
        die();
        
        return $files;
    }
    
}
