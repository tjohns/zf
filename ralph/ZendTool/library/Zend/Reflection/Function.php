<?php

class Zend_Reflection_Function extends ReflectionFunction
{
    
    public function getDocblock()
    {
        if (($comment = $this->getDocComment()) != '') {
            return new Zend_Reflection_Docblock($comment);
        }
        
        throw new Zend_Reflection_Exception($this->getName() . ' does not have a Docblock.');
    }
    
    public function getParameters()
    {
        $phpReflections = parent::getParameters();
        $zendReflections = array();
        while ($phpReflections && ($phpReflection = array_shift($phpReflections))) {
            $zendReflections[] = new Zend_Reflection_Parameter($this->getName(), $phpReflection->getName());
            unset($phpReflection);
        }
        unset($phpReflections);
        return $zendReflections;
    }

}