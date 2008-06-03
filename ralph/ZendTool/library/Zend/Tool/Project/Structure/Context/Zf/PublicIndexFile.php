<?php

class Zend_Tool_Project_Structure_Context_Zf_PublicIndexFile extends Zend_Tool_Project_Structure_Context_Filesystem_File 
{
    
    protected $_filesystemName = 'index.php';
    
    public function getName()
    {
        return 'PublicIndexFile';
    }
    
    public function getContents()
    {
        $codeGenerator = new Zend_Tool_CodeGenerator_Php_File(array(
            'body' => 'include \'../bootstrap.php\';'
            ));
        return $codeGenerator->toString();
    }
    
}