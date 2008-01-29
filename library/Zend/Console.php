<?php

class Zend_Console
{

    static protected $_instance = null;
    
    static public function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
    }
    
    protected function __construct()
    {
        
    }
    

}