<?php

require_once 'Zend/Filter/Interface.php';

class Zend_Filter_CamelCaseToDashed implements Zend_Filter_Interface
{
    
    public function filter($value)
    {
        $output = 
            preg_replace('/([a-zd])([A-Z])/','\1-\2',
                preg_replace('/([A-Z]+)([A-Z][a-z])/','\1-\2',$value)
                );
        return $output;
    }
    
}