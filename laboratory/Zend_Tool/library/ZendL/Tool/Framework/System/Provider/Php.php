<?php

class ZendL_Tool_Framework_System_Provider_PhpInfo implements ZendL_Tool_Framework_Provider_Interface
{
    
    public function showAction()
    {
        phpinfo();
    }
    
}