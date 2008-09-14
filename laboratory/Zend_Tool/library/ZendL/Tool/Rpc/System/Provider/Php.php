<?php

class ZendL_Tool_Rpc_System_Provider_PhpInfo implements ZendL_Tool_Rpc_Provider_Interface
{
    
    public function showAction()
    {
        phpinfo();
    }
    
}