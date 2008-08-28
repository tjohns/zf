<?php

require_once 'ZendL/Tool/CodeGenerator/Php/Member/Abstract.php';
require_once 'ZendL/Tool/CodeGenerator/Php/Docblock.php';
require_once 'ZendL/Tool/CodeGenerator/Php/Parameter.php';

class ZendL_Tool_CodeGenerator_Php_Method extends ZendL_Tool_CodeGenerator_Php_Member_Abstract 
{

    protected $_methodDocblock = null;
    protected $_isFinal = false;
    protected $_parameters = array();
    protected $_body = null;
    
    public static function fromReflection(ZendL_Reflection_Method $reflectionMethod)
    {
        $method = new self();
        
        $method->setSourceContent($reflectionMethod->getContents(false));
        $method->setSourceDirty(false);
        
        if ($reflectionMethod->getDocComment() != '') {
            $method->setDocblock(ZendL_Tool_CodeGenerator_Php_Docblock::fromReflection($reflectionMethod->getDocblock()));
        }
        
        $method->setFinal($reflectionMethod->isFinal());
        
        if ($reflectionMethod->isPrivate()) {
            $method->setVisibility(self::VISIBILITY_PRIVATE);
        } elseif ($reflectionMethod->isProtected()) {
            $method->setVisibility(self::VISIBILITY_PROTECTED);
        } else {
            $method->setVisibility(self::VISIBILITY_PUBLIC);
        }
        
        $method->setStatic($reflectionMethod->isStatic());

        $method->setName($reflectionMethod->getName());
        
        foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
            $method->setParameter(ZendL_Tool_CodeGenerator_Php_Parameter::fromReflection($reflectionParameter));
        }
        
        $method->setBody($reflectionMethod->getBody());

        return $method;
    }
    
    public function setDocblock(ZendL_Tool_CodeGenerator_Php_Docblock $methodDocblock)
    {
        $this->_methodDocblock = $methodDocblock;
        return $this;
    }
    
    public function getMethodDocblock()
    {
        return $this->_methodDocblock;
    }
    
    public function setFinal($isFinal)
    {
        $this->_isFinal = ($isFinal) ? true : false;
    }
    
    public function setParameters(Array $parameters)
    {
        $this->_parameters = $parameters;
        return $this;
    }
    
    public function setParameter($parameter)
    {
        if (is_array($parameter)) {
            $parameter = new ZendL_Tool_CodeGenerator_Php_Parameter($parameter);
            $parameterName = $parameter->getName();
        } elseif ($parameter instanceof ZendL_Tool_CodeGenerator_Php_Parameter) {
            $parameterName = $parameter->getName();
        } else {
            require_once 'ZendL/Tool/CodeGenerator/Php/Exception.php';
            throw new ZendL_Tool_CodeGenerator_Php_Exception('setParameter() expects either an array of method options or an instance of ZendL_Tool_CodeGenerator_Php_Parameter');
        }
        
        /*
        if (isset($this->_parameters[$parameterName])) {
            require_once 'ZendL/Tool/CodeGenerator/Php/Exception.php';
            throw new ZendL_Tool_CodeGenerator_Php_Exception('A method by name ' . $parameterName . ' already exists in this class.');
        }
        */
        
        $this->_parameters[$parameterName] = $parameter;
        return $this;
    }
    
    public function getParameters()
    {
        return $this->_parameters;
    }
    
    public function setBody($body)
    {
        $this->_body = $body;
        return $this->_body;
    }
    
    public function getBody()
    {
        return $this->_body;
    }
    
    public function generate()
    {
        $output = '    ';
        
        if ($this->_methodDocblock) {
            $output .= $this->_methodDocblock->generate() . PHP_EOL;
        }
        
        $output = '    ';
        
        if ($this->_isAbstract) {
            $output .= 'abstract ';
        }
                
        $output .= $this->_visibility . ' function ' . $this->_name . '(';

        if ($this->_parameters) {
            foreach ($this->_parameters as $parameter) {
                $parameterOuput[] = $parameter->generate();
            }
            
            $output .= implode(', ', $parameterOuput);
        }
        
        $output .= ')' . PHP_EOL . '    {' . PHP_EOL;

        if ($this->_body) {
            $output .= $this->_body . PHP_EOL;
        }
        
        $output .= '    }' . PHP_EOL;
        
        return $output;
    }
    
}