<?php

interface Zend_Tool_Project_ProfileFileParser_Interface
{
    
    public function serialize(Zend_Tool_Project_Profile $profile);
    public function unserialize($data);
    
}