<?php

require_once 'ZendL/Tool/CodeGenerator/Php/Abstract.php';
require_once 'ZendL/Tool/CodeGenerator/Php/Class.php';

class ZendL_Tool_CodeGenerator_Php_File extends ZendL_Tool_CodeGenerator_Php_Abstract
{
    
    protected static $_markerDocblock = '/* ZendL_Tool_CodeGenerator_Php_File-DocblockMarker */';
    protected static $_markerRequire = '/* ZendL_Tool_CodeGenerator_Php_File-RequireMarker: {?} */';
    protected static $_markerClass = '/* ZendL_Tool_CodeGenerator_Php_File-ClassMarker: {?} */';
    
    /**
     * @var ZendL_Tool_CodeGenerator_Php_Docblock
     */
    protected $_docblock = null;
    
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
    
    /**
     * Enter description here...
     *
     * @param ZendL_Reflection_File $reflectionFile
     * @return ZendL_Tool_CodeGenerator_Php_File
     */
    public static function fromReflection(ZendL_Reflection_File $reflectionFile)
    {
        $file = new self();
        
        $file->setSourceContent($reflectionFile->getContents());
        $file->setSourceDirty(false);
        
        $body = $reflectionFile->getContents();
        
        // @todo this whole area needs to be reworked with respect to how body lines are processed
        foreach ($reflectionFile->getClasses() as $class) {
            $file->setClass(ZendL_Tool_CodeGenerator_Php_Class::fromReflection($class));
            $classStartLine = $class->getStartLine(true);
            $classEndLine = $class->getEndLine();
            
            $bodyLines = explode("\n", $body);
            $bodyReturn = array();
            for ($lineNum = 1; $lineNum <= count($bodyLines); $lineNum++) {
                if ($lineNum == $classStartLine) { 
                    $bodyReturn[] = str_replace('?', $class->getName(), self::$_markerClass);  //'/* ZendL_Tool_CodeGenerator_Php_File-ClassMarker: {' . $class->getName() . '} */';
                    $lineNum = $classEndLine;
                } else {
                    $bodyReturn[] = $bodyLines[$lineNum - 1]; // adjust for index -> line conversion
                }
            }
            $body = implode("\n", $bodyReturn);
            unset($bodyLines, $bodyReturn, $classStartLine, $classEndLine);
        }
        
        if ($docblock = $reflectionFile->getDocblock()) {
            $file->setDocblock(ZendL_Tool_CodeGenerator_Php_Docblock::fromReflection($docblock));
            
            $bodyLines = explode("\n", $body);
            $bodyReturn = array();
            for ($lineNum = 1; $lineNum <= count($bodyLines); $lineNum++) {
                if ($lineNum == $docblock->getStartLine()) { 
                    $bodyReturn[] = str_replace('?', $class->getName(), self::$_markerDocblock);  //'/* ZendL_Tool_CodeGenerator_Php_File-ClassMarker: {' . $class->getName() . '} */';
                    $lineNum = $docblock->getEndLine();
                } else {
                    $bodyReturn[] = $bodyLines[$lineNum - 1]; // adjust for index -> line conversion
                }
            }
            $body = implode("\n", $bodyReturn);
            unset($bodyLines, $bodyReturn, $classStartLine, $classEndLine);
        }
        
        $file->setBody($body);
        
        return $file;
    }
    
    /**
     * Set the docblock
     *
     * @param ZendL_Tool_CodeGenerator_Php_Docblock $docblock
     * @return ZendL_Tool_CodeGenerator_Php_File
     */
    public function setDocblock(ZendL_Tool_CodeGenerator_Php_Docblock $docblock) 
    {
        $this->_docblock = $docblock;
        return $this;
    }
    
    /**
     * Get docblock
     *
     * @return ZendL_Tool_CodeGenerator_Php_Docblock
     */
    public function getDocblock() 
    {
        return $this->_docblock;
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
        foreach ($classes as $class) {
            $this->setClass($class);
        }
        return $this;
    }
    
    public function setClass($class)
    {
        if (is_array($class)) {
            $class = new ZendL_Tool_CodeGenerator_Php_Class($class);
            $className = $class->getName();
        } elseif ($class instanceof ZendL_Tool_CodeGenerator_Php_Class) {
            $className = $class->getName();
        } else {
            require_once 'ZendL/Tool/CodeGenerator/Php/Exception.php';
            throw new ZendL_Tool_CodeGenerator_Php_Exception('Expecting either an array or an instance of ZendL_Tool_CodeGenerator_Php_Class');
        }
        
        // @todo check for dup here 
        
        $this->_classes[$className] = $class;
        return $this;
    }
    
    /**
     * Enter description here...
     *
     * @return ZendL_Tool_CodeGenerator_Php_Class[]
     */
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
    
    public function isSourceDirty()
    {
        if (($docblock = $this->getDocblock()) && $docblock->isSourceDirty()) {
            return true;
        }
        
        foreach ($this->_classes as $class) {
            if ($class->isSourceDirty()) {
                return true;
            }
        }
        
        return parent::isSourceDirty();
    }
    
    public function generate()
    {
        if ($this->isSourceDirty() === false) {
            return $this->_sourceContent;
        }
        
        // start with the body (if there), or open tag
        $output = ($this->_body) ? $this->_body : '<?php' . PHP_EOL; // need to start with the body and then produce a php file
        
        // put file docblock in
        if ($this->_docblock) {
            $regex = preg_quote(self::$_markerDocblock, '#');
            if (preg_match('#'.$regex.'#', $output, $matches)) {
                $output = preg_replace('#'.$regex.'#', $this->_docblock->generate(), $output, 1);
            } else {
                $output .= $this->_docblock->generate() . PHP_EOL;
            }
        }
        
        // newline
        $output .= PHP_EOL;
        
        // process required files
        // @todo marker replacement for required files
        if ($this->_requiredFiles) {
            foreach ($this->_requiredFiles as $requiredFile) {
                $output .= 'require_once \'' . $requiredFile . '\';' . PHP_EOL;
            }
            
            $output .= PHP_EOL;
        }
        
        // process classes
        if ($this->_classes) {
            foreach ($this->_classes as $class) {
                $regex = str_replace('?', $class->getName(), self::$_markerClass);
                $regex = preg_quote($regex, '#');
                if (preg_match('#'.$regex.'#', $output, $matches)) {
                    $output = preg_replace('#'.$regex.'#', $class->generate(), $output, 1);
                } else {
                    $output .= $class->generate() . PHP_EOL;
                }
            }
            
            $output .= PHP_EOL;
        }

        return $output;
    }
    
}