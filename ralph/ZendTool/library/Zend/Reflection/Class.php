<?php

require_once 'Zend/Reflection/Property.php';
require_once 'Zend/Reflection/Method.php';
require_once 'Zend/Reflection/Docblock.php';

class Zend_Reflection_Class extends ReflectionClass
{
    
    /**
     * Return the classes Docblock reflection object
     *
     * @return Zend_Reflection_Docblock
     */
    public function getDocblock()
    {
        if (($comment = $this->getDocComment()) != '') {
            return new Zend_Reflection_Docblock($comment);
        }
        
        throw new Zend_Reflection_Exception($this->getName() . ' does not have a Docblock.');
        
    }
    
    
    public function getInterfaces()
    {
        $phpReflections = parent::getInterfaces();
        $zendReflections = array();
        while ($phpReflections && ($phpReflection = array_shift($phpReflections))) {
            $zendReflections[] = new Zend_Reflection_Class($phpReflection->getName());
            unset($phpReflection);
        }
        unset($phpReflections);
        return $zendReflections;
    }
    
    public function getMethod($name)
    {
        $phpReflection = parent::getMethod($name);
        $zendReflection = new Zend_Reflection_Method($this->getName(), $phpReflection->getName());
        unset($phpReflection);
        return $zendReflection;
    }

    public function getMethods($filter = -1)
    {
        $phpReflections = parent::getMethods($filter);
        $zendReflections = array();
        while ($phpReflections && ($phpReflection = array_shift($phpReflections))) {
            $zendReflections[] = new Zend_Reflection_Method($this->getName(), $phpReflection->getName());
            unset($phpReflection);
        }
        unset($phpReflections);
        return $zendReflections;
    }

    public function getParentClass()
    {
        $phpReflection = parent::getParentClass();
        $zendReflection = new Zend_Reflection_Class($phpReflection->getName());
        unset($phpReflection);
        return $zendReflection;
    }

    public function getProperty($name)
    {
        $phpReflection = parent::getProperty($name);
        $zendReflection = new Zend_Reflection_Property($this->getName(), $phpReflection->getName());
        unset($phpReflection);
        return $zendReflection;
    }
    
    public function getProperties($filter = -1)
    {
        $phpReflections = parent::getProperties($filter);
        $zendReflections = array();
        while ($phpReflections && ($phpReflection = array_shift($phpReflections))) {
            $zendReflections[] = new Zend_Reflection_Property($this->getName(), $phpReflection->getName());
            unset($phpReflection);
        }
        unset($phpReflections);
        return $zendReflections;
    }
    
    
    
    
}
