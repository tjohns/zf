<?php

abstract class Zend_Tool_CodeGenerator_CodeGeneratorAbstract
{
    
    public function __construct(Array $options = array())
    {
        if ($options) {
            $this->setOptions($options);
        }
    }
    
    public function setOptions(Array $options)
    {
        foreach ($options as $optionName => $optionValue) {
            $methodName = 'set' . $optionName;
            if (method_exists($this, $methodName)) {
                call_user_func(array($this, $methodName), $optionValue);
            }
        }
    }
    
    //abstract public function fromString();
    
    abstract public function toString();
    
    final public function __toString()
    {
        return $this->toString();
    }

}
