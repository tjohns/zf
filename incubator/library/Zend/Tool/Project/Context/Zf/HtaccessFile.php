<?php

class Zend_Tool_Project_Context_Zf_HtaccessFile extends Zend_Tool_Project_Context_Filesystem_File 
{
    
    protected $_filesystemName = '.htaccess';
    
    public function getName()
    {
        return 'HtaccessFile';
    }
    
    public function getContents()
    {
        $output = <<<EOS
RewriteEngine On

RewriteCond %{REQUEST_FILENAME} -s [OR]
RewriteCond %{REQUEST_FILENAME} -l [OR]
RewriteCond %{REQUEST_FILENAME} -d

RewriteRule ^.*$ - [NC,L]
RewriteRule ^.*$ /index.php [NC,L]
        
        
EOS;
        return $output;
    }
    
}