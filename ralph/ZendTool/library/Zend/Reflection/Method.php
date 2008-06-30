<?php

class Zend_Reflection_Method extends ReflectionMethod
{

    public function getDocblock()
    {
        if (($comment = $this->getDocComment()) != '') {
            return new Zend_Reflection_Docblock($comment);
        }
        
        throw new Zend_Reflection_Exception($this->getName() . ' does not have a Docblock.');
        
    }
    
    public function getDeclaringClass()
    {
        $phpReflection = parent::getDeclaringClass();
        $zendReflection = new Zend_Reflection_Class($phpReflection->getName());
        unset($phpReflection);
        return $zendReflection;
    }
    
    public function getParameters()
    {
        $phpReflections = parent::getParameters();
        $zendReflections = array();
        while ($phpReflections && ($phpReflection = array_shift($phpReflections))) {
            $zendReflections[] = new Zend_Reflection_Parameter(array($this->getDeclaringClass()->getName(), $this->getName()), $phpReflection->getName());
            unset($phpReflection);
        }
        unset($phpReflections);
        return $zendReflections;
    }
    
}

