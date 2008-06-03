<?php

class Zend_Tool_CodeGenerator_Php_File extends Zend_Tool_CodeGenerator_CodeGeneratorAbstract
{
    
    /**
     * @var Zend_Tool_CodeGenerator_Php_Docblock_File
     */
    protected $_fileDocblock = null;
    
    /**
     * @var array
     */
    protected $_requiredFiles = array();
    
    /**
     * @var array
     */
    protected $_classes = array();
    
    /**
     * @var string
     */
    protected $_body = null;

    public function setFileDocblock(Zend_Tool_CodeGenerator_Php_Docblock_File $fileDocblock) 
    {
        $this->_fileDocblock = $_fileDocblock;
        return $this;
    }
    
    public function getFileDocblock() 
    {
        return $this->_fileDocblock;
    }

    public function setRequiredFiles($requiredFiles)
    {
        $this->_requiredFiles = $requiredFiles;
        return $this;
    }
    
    public function getRequiredFiles() 
    {
        return $this->_requiredFiles;
    }

    public function setClasses(Array $classes) 
    {
        $this->_classes = $classes;
        return $this;
    }
    
    public function getClasses() 
    {
        return $this->_classes;
    }

    public function setBody($body)
    {
        $this->_body = $body;
    }
    
    public function getBody()
    {
        return $this->_body;
    }
    
    public function toString()
    {
        $output = '<?php' . PHP_EOL;
        
        if ($this->_fileDocblock) {
            $output .= $this->_fileDocblock->toString() . PHP_EOL;
        }
        
        $output .= PHP_EOL;
        
        if ($this->_requiredFiles) {
            foreach ($this->_requiredFiles as $requiredFile) {
                $output .= 'require_once \'' . $requiredFile . '\';' . PHP_EOL;
            }
        }
        
        if ($this->_classes) {
            foreach ($this->_classes as $class) {
                $output .= $class->toString() . PHP_EOL . PHP_EOL;
            }
        }
        
        if ($this->_body) {
            $output .= $this->_body . PHP_EOL . PHP_EOL;
        }

        return $output;
    }

    
}