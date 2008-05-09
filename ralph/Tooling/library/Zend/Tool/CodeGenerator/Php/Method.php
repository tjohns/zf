<?php

class Zend_Tool_CodeGenerator_Php_Method extends Zend_Tool_CodeGenerator_Php_MemberAbstract 
{

    protected $_methodDocblock = null;
    protected $_parameters = array();
    protected $_body = null;
    
    public function setMethodDocblock(Zend_Tool_CodeGenerator_Php_Docblock_Method $methodDocblock)
    {
        $this->_methodDocblock = $methodDocblock;
        return $this;
    }
    
    public function getMethodDocblock()
    {
        return $this->_methodDocblock;
    }
    
    public function setParameters(Array $parameters)
    {
        $this->_parameters = $parameters;
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
    
    public function toString()
    {
        $output = '    ';
        
        if ($this->_methodDocblock) {
            $output .= $this->_methodDocblock->toString() . PHP_EOL;
        }
        
        if ($this->_isAbstract) {
            $output .= 'abstract ';
        }
                
        $output .= $this->_visibility . ' function ' . $this->_name . '(';

        if ($this->_parameters) {
            foreach ($this->_parameters as $parameter) {
                $parameterOuput[] = $parameter->toString();
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