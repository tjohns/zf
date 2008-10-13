<?php

require_once 'ZendL/Reflection/Class.php';
require_once 'ZendL/Reflection/Function.php';

class ZendL_Reflection_Extension extends ReflectionExtension
{
    
    public function getFunctions()
    {
        $phpReflections = parent::getFunctions();
        $zendReflections = array();
        while ($phpReflections && ($phpReflection = array_shift($phpReflections))) {
            $zendReflections[] = new ZendL_Reflection_Function($phpReflection->getName());
            unset($phpReflection);
        }
        unset($phpReflections);
        return $zendReflections;
    }
    
    public function getClasses()
    {
        $phpReflections = parent::getClasses();
        $zendReflections = array();
        while ($phpReflections && ($phpReflection = array_shift($phpReflections))) {
            $zendReflections[] = new ZendL_Reflection_Class($phpReflection->getName());
            unset($phpReflection);
        }
        unset($phpReflections);
        return $zendReflections;
    }
}