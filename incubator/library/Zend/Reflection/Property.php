<?php

class Zend_Reflection_Property extends ReflectionProperty
{
    
    // @todo implement line numbers in here
    
    public function getDeclaringClass()
    {
        $phpReflection = parent::getDeclaringClass();
        $zendReflection = new Zend_Reflection_Class($phpReflection->getName());
        unset($phpReflection);
        return $zendReflection;
    }
}