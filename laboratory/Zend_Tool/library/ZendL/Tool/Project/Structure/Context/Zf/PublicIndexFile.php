<?php

class ZendL_Tool_Project_Structure_Context_Zf_PublicIndexFile extends ZendL_Tool_Project_Structure_Context_Filesystem_File 
{
    
    protected $_filesystemName = 'index.php';
    
    public function getName()
    {
        return 'PublicIndexFile';
    }
    
    public function getContents()
    {
        $codeGenerator = new ZendL_Tool_CodeGenerator_Php_File(array(
            'body' => <<<EOS
<?php 
// @see application/bootstrap.php
\$bootstrap = true; 
require '../application/bootstrap.php';  

// \$frontController is created in your boostrap file. Now we'll dispatch it, which dispatches your application. 
\$frontController->dispatch();
EOS
            ));
        return $codeGenerator->generate();
    }
    
}