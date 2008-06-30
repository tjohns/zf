<?php

require_once 'Zend/Reflection/Class.php';

class Zend_Reflection_File implements Reflector
{

    protected $_filepath        = null;
    protected $_docComment      = null;
    protected $_includedFiles   = array();
    protected $_classes         = array();
    protected $_contents        = null;

    public static function export()
    {
        // @todo what does this do?
    }
    
    public function __construct($file)
    {
        $fileName = $file;
        
        while ( ($found = realpath($fileName)) === false)
        {
            if (!isset($includePaths)) {
                $includePaths =  explode(PATH_SEPARATOR, get_include_path());
            } elseif (count($includePaths) === 0) {
                break;
            }

            $fileName = array_shift($includePaths) . DIRECTORY_SEPARATOR . $file;
        }
        
        if (!$found) {
            require_once 'Zend/Reflection/Exception.php';
            throw new Zend_Reflection_Exception('File ' . $file . ' must be required before it can be reflected.');
        }
        
        $this->_fileName = $fileName;
        $this->_reflect(file_get_contents($this->_fileName));
    }
    
    public function getFileName()
    {
        return $this->_fileName;
    }
    
    public function getClasses()
    {
        return $this->_classes;
    }
    
    public function getClass($name)
    {
        foreach ($this->_classes as $class) {
            if ($class->getName() == $name) {
                return $class;
            }
        }
        
        require_once 'Zend/Reflection/Exception.php';
        throw new Zend_Reflection_Exception('Class by name ' . $name . ' not found.');
    }

    public function __toString()
    {
        
    }
    
    protected function _reflect($contents)
    {
        
        if (preg_match_all('#class\s([A-Za-z0-9_]*)\s(?:[extends|implements]?[\s\w,\n]+)?{#Us', $contents, $matches)) {
            foreach ($matches[1] as $classMatch) {
                $this->_classes[] = new Zend_Reflection_Class($classMatch);
            }
        }
        
        return;
        
    }
    
}
