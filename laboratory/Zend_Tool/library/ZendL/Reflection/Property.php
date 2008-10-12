<?php

class ZendL_Reflection_Property extends ReflectionProperty
{
    
    // @todo implement line numbers in here
    
    public function getDeclaringClass()
    {
        $phpReflection = parent::getDeclaringClass();
        $zendReflection = new ZendL_Reflection_Class($phpReflection->getName());
        unset($phpReflection);
        return $zendReflection;
    }
}