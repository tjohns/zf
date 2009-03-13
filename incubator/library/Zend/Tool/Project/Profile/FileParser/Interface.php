<?php

interface Zend_Tool_Project_Profile_FileParser_Interface
{
    
    public function serialize(Zend_Tool_Project_Profile $profile);
    public function unserialize($data, Zend_Tool_Project_Profile $profile);
    
}