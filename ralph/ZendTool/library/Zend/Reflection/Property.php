<?php

class Zend_Reflection_Property extends ReflectionProperty
{
    public function getDeclaringClass()
    {
        $phpReflection = parent::getDeclaringClass();
        $zendReflection = new Zend_Reflection_Class($phpReflection->getName());
        unset($phpReflection);
        return $zendReflection;
    }
}