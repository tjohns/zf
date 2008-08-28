<?php

class ZendL_Reflection_Parameter extends ReflectionParameter 
{

    protected $_isFromMethod = false;
    
    public function getDeclaringClass()
    {
        $phpReflection = parent::getDeclaringClass();
        $zendReflection = new ZendL_Reflection_Class($phpReflection->getName());
        unset($phpReflection);
        return $zendReflection;
    }
    
    public function getClass()
    {
        $phpReflection = parent::getClass();
        $zendReflection = new ZendL_Reflection_Class($phpReflection->getName());
        unset($phpReflection);
        return $zendReflection;
    }
    
    public function getDeclaringFunction()
    {
        $phpReflection = parent::getDeclaringFunction();
        if ($phpReflection instanceof ReflectionMethod) {
            $zendReflection = new ZendL_Reflection_Method($this->getDeclaringClass()->getName(), $phpReflection->getName());
        } else {
            $zendReflection = new ZendL_Reflection_Function($phpReflection->getName());
        }
        unset($phpReflection);
        return $zendReflection;
    }
    
    public function getType()
    {
        if ($docblock = $this->getDeclaringFunction()->getDocblock()) {
            $params = $docblock->getTags('param');
            
            if (isset($params[$this->getPosition() - 1])) {
                return $params[$this->getPosition() - 1]->getType();
            }
            
        }
        
        return null;
    }
    
}

