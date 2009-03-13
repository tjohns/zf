<?php

require_once 'Zend/Reflection/Class.php';
require_once 'Zend/Reflection/Function.php';

class Zend_Reflection_Extension extends ReflectionExtension
{
    
    public function getFunctions()
    {
        $phpReflections = parent::getFunctions();
        $zendReflections = array();
        while ($phpReflections && ($phpReflection = array_shift($phpReflections))) {
            $zendReflections[] = new Zend_Reflection_Function($phpReflection->getName());
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
            $zendReflections[] = new Zend_Reflection_Class($phpReflection->getName());
            unset($phpReflection);
        }
        unset($phpReflections);
        return $zendReflections;
    }
}