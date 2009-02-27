<?php

class Zend_Tool_Project_Context_Zf_TestPHPUnitConfigFile extends Zend_Tool_Project_Context_Filesystem_File
{
    
    protected $_filesystemName = 'phpunit.xml';
    
    public function getName()
    {
        return 'TestPHPUnitConfigFile';
    }
    
}