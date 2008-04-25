<?php

require_once 'Zend/Version.php';

class Zend_Tool_Provider_Version extends Zend_Tool_Provider_Abstract
{

    public function show()
    {
        echo 'Zend Framework Version: ' . Zend_Version::VERSION;
    }
    
}
