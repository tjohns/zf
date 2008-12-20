<?php

require_once 'Zend/Tool/Project/Provider/Abstract.php';

class Zend_Tool_Project_Provider_Profile extends Zend_Tool_Project_Provider_Abstract
{
    
    public function show()
    {
        $profile = $this->_loadExistingProfile();
        
        if ($profile === false) {
            
            
            
            echo 'There is no profile located here.';
            return;
        }
    }
    
}
