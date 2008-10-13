<?php

class ZendL_Tool_Rpc_System_Manifest implements ZendL_Tool_Rpc_Manifest_Interface
{
    
    public function getProviders()
    {
        $providers = array(
            new ZendL_Tool_Rpc_System_Provider_Version(),
            new ZendL_Tool_Rpc_System_Provider_Providers()
            );
            
        return $providers;
    }
    
    public function getActions()
    {
        $actions = array(
            new ZendL_Tool_Rpc_System_Action_Create(),
            new ZendL_Tool_Rpc_System_Action_Delete()
            );
            
        return $actions;
    }
    
    
}