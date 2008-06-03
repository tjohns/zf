<?php

class Zend_Tool_Project_Provider_Manifest implements Zend_Tool_Rpc_Manifest_Interface 
{
    
    public function getProviders()
    {
        return array(
            new Zend_Tool_Project_Provider_Project()
            );
    }
    
}