<?php

class ZendL_Tool_Project_Structure_Context_Zf_HtaccessFile extends ZendL_Tool_Project_Structure_Context_Filesystem_File 
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