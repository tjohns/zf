<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Reflection
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @see Zend_Reflection_Class
 */
require_once 'Zend/Reflection/Class.php';

/**
 * @see Zend_Reflection_Function
 */
require_once 'Zend/Reflection/Function.php';

/**
 * @category   Zend
 * @package    Zend_Reflection
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Reflection_File implements Reflector
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
     * @var Zend_Reflection_Docblock
     */
    protected $_docblock        = null;
    
    /**
     * @var int
     */
    protected $_startLine       = 1;
    
    /**
     * @var int
     */
    protected $_endLine         = null;
    
    /**
     * @var string[]
     */
    protected $_requiredFiles   = array();
    
    /**
     * @var Zend_Reflection_Class[]
     */
    protected $_classes         = array();
    
    /**
     * @var Zend_Reflection_Function[]
     */
    protected $_functions       = array();
    
    /**
     * @var string
     */
    protected $_contents        = null;

    /**
     * findRealpathInIncludePath()
     *
     * @param string $fileName
     * @return string
     */
    public static function findRealpathInIncludePath($fileName)
    {
        $includePaths = explode(PATH_SEPARATOR, get_include_path());
        while (count($includePaths) > 0) {
            $filePath = array_shift($includePaths) . DIRECTORY_SEPARATOR . $fileName;
            
            if ( ($foundRealpath = realpath($filePath)) !== false) {
                break;
            }
        }
        
        return $foundRealpath;
    }
    
    /**
     * export() - required by the Reflector interface
     *
     */
    public static function export()
    {
        // @todo what does this do?
        return null;
    }
    
    /**
     * __construct()
     *
     * @param string $file
     */
    public function __construct($file)
    {
        $fileName = $file;
        
        if (($fileRealpath = realpath($fileName)) === false) {
            $fileRealpath = self::findRealpathInIncludePath($file);
        }
        
        if (!$fileRealpath || !in_array($fileRealpath, get_included_files())) {
            require_once 'Zend/Reflection/Exception.php';
            throw new Zend_Reflection_Exception('File ' . $file . ' must be required before it can be reflected.');
        }

        $this->_fileName = $fileRealpath;
        $this->_contents = file_get_contents($this->_fileName);
        $this->_reflect();
    }
    
    /**
     * getFileName() - Return the file name of the reflected file
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->_fileName;
    }
    
    /**
     * getStartLine() - Get the start line - Always 1, staying consistent with the Reflection API
     *
     * @return int
     */
    public function getStartLine()
    {
        return $this->_startLine;
    }
    
    /**
     * getEndLine() - Get the end line - number of lines
     *
     * @return int
     */
    public function getEndLine()
    {
        return $this->_endLine;
    }
    
    /**
     * getDocComment() - Return the doc comment
     *
     * @return string
     */
    public function getDocComment()
    {
        return $this->_docComment;
    }
    
    /**
     * getDocblock() - Return the docblock
     *
     * @return Zend_Reflection_Docblock
     */
    public function getDocblock()
    {
        return $this->_docblock;
    }
    
    /**
     * getClasses() Return the reflection classes of the classes found inside this file
     *
     * @return array Array of Zend_Reflection_Class
     */
    public function getClasses()
    {
        return $this->_classes;
    }
    
	/**
     * getFunctions() - Return the reflection functions of the functions found inside this file
     *
     * @return array Array of Zend_Reflection_Functions
     */
    public function getFunctions()
    {
        return $this->_functions;
    }
    
    /**
     * getClass()
     *
     * @param string $name
     * @return Zend_Reflection_Class
     */
    public function getClass($name = null)
    {
        if ($name == null) {
            reset($this->_classes);
            return current($this->_classes);
        }
        
        foreach ($this->_classes as $class) {
            if ($class->getName() == $name) {
                return $class;
            }
        }
        
        require_once 'Zend/Reflection/Exception.php';
        throw new Zend_Reflection_Exception('Class by name ' . $name . ' not found.');
    }

    /**
     * getContents() - Return the full contents of file.
     *
     * @return string
     */
    public function getContents()
    {
        return $this->_contents;
    }
    
    /**
     * __toString() - Required by the Reflector interface
     *
     */
    public function __toString()
    {
        // @todo what does this do?
        return null;
    }
    
    /**
     * _reflect() - this method does the work of "reflecting" the file
     *
     */
    protected function _reflect()
    {
        $contents = $this->_contents;
        
        $matches = array();
        
        // find the page/file level docblock
        if (preg_match('#^<\?php[ \t]*[\r?\n]{1,2}(\/\*(?:.*?)\*\/)#si', $contents, $matches)) {
            $this->_docComment = $matches[1];
            $this->_startLine = count(explode("\n", $matches[0])) + 1;
            $this->_docblock = new Zend_Reflection_Docblock($this);
        }
        
        if (preg_match_all('#require_once\s\'([A-Za-z0-9\.\/]*)\';#si', $contents, $matches)) {
            foreach ($matches[1] as $requireMatch) {
                $this->_requiredFiles[] = $requireMatch;
            }
        }
        
        // find the classes inside this file
        if (preg_match_all('#class\s([A-Za-z0-9_]*)\s(?:[extends|implements]?[\s\w,\n]+)?{#Us', $contents, $matches)) {
            foreach ($matches[1] as $classMatch) {
                $this->_classes[] = new Zend_Reflection_Class($classMatch);
            }
        }
        
        // find the functions inside this file
        if (preg_match_all('#(function|(public\s+function)|(protected\s+function))\s*\([\w-\$\s\,]*\)\s*{#Us', $contents, $matches)) {
            foreach ($matches[1] as $functionMatch) {
                $this->_functions[] = new Zend_Reflection_Function($functionMatch);
            }
        }

        $this->_endLine = count(explode("\n", $this->_contents));
        
        return;
    }
    
}
