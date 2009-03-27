<?php

class Zend_Tool_Framework_Client_ConsoleClient_ResponseDecorator_Colorizer
{
    
    protected $_colorOptions = array(
        'green' => null,
        'red' => null,
        'blue' => null
        );
    
    public function getName()
    {
        return 'color';
    }
    
    public function decorate($content, $color)
    {
        
    }
    
    
}