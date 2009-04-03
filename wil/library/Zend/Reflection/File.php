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

require_once 'Zend/Reflection/Factory.php';

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
    
    protected $_factory;

    /**
     * __construct()
     *
     * @param string $file
     */
    public function __construct($file, $factory = null) {
        
        if (!isset($factory)) {
            $factory = new Zend_Reflection_Factory();
        }
        
        $this->_factory = $factory;
        
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
        $tokens = token_get_all($contents);
        
        $functionTrapped = false;
        $classTrapped    = false;
        $requireTrapped  = false;
        $openParens      = 0;
        
        $this->_checkFileDocBlock($tokens);
                        
        foreach($tokens as $token) {
            /*
             * Tokens are characters representing symbols or arrays representing strings.
             * The keys/values in the arrays are 0=>token id, 1=>string, 2=>line number.
             * Token ID's are explained here: http://www.php.net/manual/en/tokens.php.
             */
            
            if(is_array($token)) {
                $type =    $token[0];
                $value =   $token[1];
                $lineNum = $token[2];
            } else {
                
                // It's a symbol

                // Maintain the count of open parens
                if ($token == '{') {
                    $openParens++;
                } else if ($token == '}') {
                    $openParens--;
                }
                
                continue;
            }
            
            switch($type) {
                // Name of something
                case T_STRING:
                    if ($functionTrapped) {
                        $this->_functions[] = $this->_factory->createFunction($value);
                        $functionTrapped = false;
                    } else if ($classTrapped) {
                        $this->_classes[] = $this->_factory->createClass($value);
                        $classTrapped = false;
                    }
                    
                    continue;
                    
                // Required file names are T_CONSTANT_ENCAPSED_STRING
                case T_CONSTANT_ENCAPSED_STRING:
                    if ($requireTrapped) {
                        $this->_requiredFiles[] = $value ."\n";
                        $requireTrapped = false;
                    }
                    
                    continue;
                    
                // Functions
                case T_FUNCTION:
                    
                    if($openParens == 0) {
                        $functionTrapped = true;
                    }
                    
                    break;
                    
                // Classes
                case T_CLASS:

                    $classTrapped = true;
                    
                    break;
                    
                // All types of requires
                case T_REQUIRE:
                case T_REQUIRE_ONCE:
                case T_INCLUDE:
                case T_INCLUDE_ONCE:
                    
                    $requireTrapped = true;
                    
                    break;
                    
            }
        }
        
        $this->_endLine = count(explode("\n", $this->_contents));
    }
    
    protected function _checkFileDocBlock($tokens) {
        foreach ($tokens as $token) {
            $type  =   $token[0];
            $value =   $token[1];
            $lineNum = $token[2];
            if($type == T_OPEN_TAG || $type == T_WHITESPACE) {
                continue;
            } else if ($type == T_DOC_COMMENT) {
                $this->_docComment = $value;
                $this->_startLine = $lineNum + substr_count($value, "\n") + 1;
                $this->_docblock = $this->_factory->createDocblock($this);
                return;
            } else {
                // Only whitespace is allowed before file docblocks
                return;
            }
        }
    }
}