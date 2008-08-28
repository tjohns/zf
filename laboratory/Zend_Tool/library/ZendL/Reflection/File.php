<?php

require_once 'ZendL/Reflection/Class.php';

class ZendL_Reflection_File implements Reflector
{
    
    /**
     * @var string
     */
    protected $_filepath        = null;
    
    /**
     * @var string
     */
    protected $_docComment      = null;
    
    /**
     * @var ZendL_Reflection_Docblock
     */
    protected $_docblock        = null;
    
    protected $_startLine       = 1;
    protected $_endLine         = null;
    
    /**
     * @var string[]
     */
    protected $_includedFiles   = array();
    
    /**
     * @var ZendL_Reflection_Class[]
     */
    protected $_classes         = array();
    
    /**
     * @var string
     */
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
        
        if (!$found || !in_array($found, get_included_files())) {
            require_once 'ZendL/Reflection/Exception.php';
            throw new ZendL_Reflection_Exception('File ' . $file . ' must be required before it can be reflected.');
        }

        $this->_fileName = $fileName;
        $this->_contents = file_get_contents($this->_fileName);
        $this->_reflect();
    }
    
    /**
     * Return the file name of the reflected file
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->_fileName;
    }
    
    /**
     * Get the start line - Always 1, staying consistent with the Reflection API
     *
     * @return int
     */
    public function getStartLine()
    {
        return $this->_startLine;
    }
    
    /**
     * Get the end line - number of lines
     *
     * @return int
     */
    public function getEndLine()
    {
        return $this->_endLine;
    }
    
    /**
     * Return the doc comment
     *
     * @return string
     */
    public function getDocComment()
    {
        return $this->_docComment;
    }
    
    /**
     * Return the docblock
     *
     * @return ZendL_Reflection_Docblock
     */
    public function getDocblock()
    {
        return $this->_docblock;
    }
    
    /**
     * Return the reflection classes of the classes found inside this file
     *
     * @return ZendL_Reflection_Class[]
     */
    public function getClasses()
    {
        return $this->_classes;
    }
    
    /**
     * Enter description here...
     *
     * @param string $name
     * @return ZendL_Reflection_Class
     */
    public function getClass($name)
    {
        foreach ($this->_classes as $class) {
            if ($class->getName() == $name) {
                return $class;
            }
        }
        
        require_once 'ZendL/Reflection/Exception.php';
        throw new ZendL_Reflection_Exception('Class by name ' . $name . ' not found.');
    }

    /**
     * Return the full contents of file.
     *
     * @return string
     */
    public function getContents()
    {
        return $this->_contents;
    }
    
    public function __toString()
    {
        // @todo what does this do?
    }
    
    protected function _reflect()
    {
        $contents = $this->_contents;
        
        // find the page/file level docblock
        if (preg_match('#^<\?php[ \t]*[\r?\n]{1,2}(\/\*(?:.*?)\*\/)#si', $contents, $matches)) {
            $this->_docComment = $matches[1];
            $this->_startLine = count(explode("\n", $matches[0])) + 1;
            $this->_docblock = new ZendL_Reflection_Docblock($this);
        }
        
        // find the classes inside this file
        if (preg_match_all('#class\s([A-Za-z0-9_]*)\s(?:[extends|implements]?[\s\w,\n]+)?{#Us', $contents, $matches)) {
            foreach ($matches[1] as $classMatch) {
                $this->_classes[] = new ZendL_Reflection_Class($classMatch);
            }
        }

        $this->_endLine = count(explode("\n", $this->_contents));
        
        return;
    }
    
}
