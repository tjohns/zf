<?php

require_once 'ZendL/Tool/Rpc/Manifest/Interface.php';

class ZendL_Tool_Project_Provider_Manifest implements ZendL_Tool_Rpc_Manifest_Interface 
{
    
    public function getProviders()
    {
        return array(
            new ZendL_Tool_Project_Provider_Project()
            );
    }
    
}