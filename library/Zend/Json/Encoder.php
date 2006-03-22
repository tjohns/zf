<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to version 1.0 of the Zend Framework
 * license, that is bundled with this package in the file LICENSE,
 * and is available through the world-wide-web at the following URL:
 * http://www.zend.com/license/framework/1_0.txt. If you did not
 * receive a copy of the Zend Framework license and are unable to
 * obtain it through the world-wide-web, please send a note to
 * license@zend.com so we can mail you a copy immediately.
 *
 * @package    Zend_Json
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com/)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */


/**
 * Zend_Json_Exception
 */
require_once 'Zend/Json/Exception.php';


/**
 * Encode PHP constructs to JSON
 *
 * @package    Zend_Json
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com/)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
class Zend_Json_Encoder
{
    /**
     * Array of visited objects; used to prevent cycling.
     *
     * @var array
     */
    protected $_visited = array();

    /**
     * Constructor
     */
    protected function __construct()
    {
    }

    /**
     * Use the JSON encoding scheme for the value specified
     *
     * @param mixed $value  value the object to be encoded
     * @return string  The encoded value
     */
    public static function encode($value)
    {
    	if (empty($value)) {
    	    return('NULL');
    	}

        $encoder = new Zend_Json_Encoder();

        return $encoder->_encodeValue($value);
    }

    /**
     * Recursive driver which determines the type of value to be encoded
     * and then dispatches to the appropriate method. $values are either
     *    - objects (returns from {@link _encodeObject()})
     *    - arrays (returns from {@link _encodeArray()})
     *    - basic datums (e.g. numbers or strings) (returns from {@link _encodeDatum()})
     *
     * @param $value mixed The value to be encoded
     * @return string Encoded value
     */
    protected function _encodeValue(&$value)
    {
    	if (is_object($value)) {
            return $this->_encodeObject($value);
    	} else if (is_array($value)) {
            return $this->_encodeArray($value);
    	}

        return $this->_encodeDatum($value);
    }



    /**
     * Encode an object to JSON by encoding each of the public properties
     *
     * A special property is added to the JSON object called '__className'
     * that contains the name of the class of $value. This is used to decode
     * the object on the client into a specific class.
     *
     * @param $value object
     * @return string
     * @throws Zend_Json_Exception
     */
    protected function _encodeObject(&$value)
    {
        if ($this->_wasVisited($value)) {
    	    throw new Zend_Json_Exception(
                'Cycles not supported in JSON encoding, cycle introduced by '
                . 'class "' . get_class($value) . '"'
            );
    	}

        $this->_visited[] = $value;

    	$props = '';
    	foreach (get_object_vars($value) as $name => $propValue) {
    	    if (isset($propValue)) {
        		$props .= ', '
                        . $this->_encodeValue($name)
        		        . ' : '
                        . $this->_encodeValue($propValue);
    	    }
    	}

    	return '{' . '"__className": "' . get_class($value) . '"'
                . $props . '}';
    }


    /**
     * Determine if an object has been serialized already
     *
     * @access protected
     * @param mixed $value
     * @return boolean
     */
    protected function _wasVisited(&$value)
    {
        if (in_array($value, $this->_visited, true)) {
            return true;
        }

        return false;
    }


    /**
     * JSON encode an array value
     *
     * Recursively encodes each value of an array and returns a JSON encoded
     * array string.
     *
     * @param $array array
     * @return string
     */
    protected function _encodeArray(&$array)
    {
    	$result = '[';
    	$length = count($array);

        $tmpArray = array();
        for ($i = 0; $i < $length; $i++) {
            $tmpArray[] = $this->_encodeValue($array[$i]);
        }
        $result .= implode(', ', $tmpArray);

    	return $result . ']';
    }


    /**
     * JSON encode a basic data type (string, number, boolean, null)
     *
     * If value type is not a string, number, boolean, or null, the string
     * 'NULL' is returned.
     *
     * @param $value mixed
     * @return string
     */
    protected function _encodeDatum(&$value)
    {
        $result = 'NULL';

    	if (is_numeric($value)) {
    	    $result = (string)$value;
        } elseif (is_string($value)) {
            $result = $this->_encodeString($value);
    	} elseif (is_bool($value)) {
    	    $result = $value ? 'TRUE' : 'FALSE';
        }

    	return $result;
    }


    /**
     * JSON encode a string value by escaping characters as necessary
     *
     * @param $value string
     * @return string
     */
    protected function _encodeString(&$string)
    {
        // Escape these characters with a backslash:
        // " \ / \n \r \t
        $string = preg_replace('/(["\\/\n\r\t])/', '\\\\$1', $string);

        // Escape certain ASCII characters:
        // 0x08 => \b
        // 0x0c => \f
        $string = str_replace(array(chr(0x08), chr(0x0C)), array('\b', '\f'), $string);

    	return '"' . $string . '"';
    }


    /**
     * Encode the constants associated with the ReflectionClass
     * parameter. The encoding format is based on the class2 format
     *
     * @param $cls ReflectionClass
     * @return string Encoded constant block in class2 format
     */
    static private function _encodeConstants(ReflectionClass $cls)
    {
    	$result    = "constants : {";
    	$constants = $cls->getConstants();

        $tmpArray = array();
    	if (!empty($constants)) {
            foreach ($constants as $key => $value) {
                $tmpArray[] = "\n\t$key: " . self::encode($value);
            }

            $result .= implode(', ', $tmpArray);
        }

    	return $result . "\n}";
    }


    /**
     * Encode the public methods of the ReflectionClass in the
     * class2 format
     *
     * @param $cls ReflectionClass
     * @return string Encoded method fragment
     *
     */
    static private function _encodeMethods(ReflectionClass $cls)
    {
    	$methods = $cls->getMethods();
    	$result = 'methods : {'."\n";

        $started = false;
        foreach ($methods as $method) {
    	    if (! $method->isPublic() || !$method->isUserDefined()) {
        		continue;
    	    }

    	    if ($started) {
        		$result .= ",\n";
    	    }
            $started = true;

    	    $result .= "\t" .$method->getName(). ': function(';

    	    if ('__construct' != $method->getName()) {
        		$parameters  = $method->getParameters();
                $paramCount  = count($parameters);
                $argsStarted = false;

        		$argNames = "\t\tvar argNames = [";
                foreach ($parameters as $param) {
        		    if ($argsStarted) {
            			$result .= ', ';
        		    }

        		    $result .= $param->getName();

        		    if ($argsStarted) {
            			$argNames .= ', ';
        		    }

        		    $argNames .= '"' . $param->getName() . '"';

                    $argsStarted = true;
        		}
        		$argNames .= "];\n";

        		$result .= ") {\n"
        		         . $argNames
            		     . "\t\tvar result = ZAjaxEngine.invokeRemoteMethod("
            		     . "this, '" . $method->getName()
                         . "', argNames, arguments);\n"
                		 . "\t\treturn(result);\n\t}";
    	    } else {
        		$result .= ") {\n\t}";
    	    }
    	}

    	return $result . "\n}";
    }


    /**
     * Encode the public properties of the ReflectionClass in the class2
     * format.
     *
     * @param $cls ReflectionClass
     * @return string Encode properties list
     *
     */
    static private function _encodeVariables(ReflectionClass $cls)
    {
    	$properties = $cls->getProperties();
    	$propValues = get_class_vars($cls->getName());
    	$result = "variables : {\n";
    	$cnt = 0;

        $tmpArray = array();
    	foreach ($properties as $prop) {
    	    if (! $prop->isPublic()) {
        		continue;
    	    }

            $tmpArray[] = "\n\t"
                        . $prop->getName()
                        . self::encode($propValues[$prop->getName()]);
        }
        $result .= implode(',', $tmpArray);

    	return $result . "\n}";
    }

    /**
     * Encodes the given $className into the class2 model of encoding PHP
     * classes into JavaScript class2 classes.
     * NOTE: Currently only public methods and variables are proxied onto
     * the client machine
     *
     * @param $className string The name of the class, the class must be
     * instantiable using a null constructor
     * @param $package string Optional package name appended to JavaScript
     * proxy class name
     * @return string The class2 (JavaScript) encoding of the class
     * @throws Zend_Json_Exception
     */
    static public function encodeClass($className, $package = '')
    {
    	$cls = new ReflectionClass($className);
    	if (! $cls->isInstantiable()) {
    	    throw new Zend_Json_Exception("$className must be instantiable");
    	}

    	return "Class.create('$package$className', {\n"
    	        . self::_encodeConstants($cls)    .",\n"
    	        . self::_encodeMethods($cls)      .",\n"
    	        . self::_encodeVariables($cls)    .'});';
    }


    /**
     * Encode several classes at once
     *
     * Returns JSON encoded classes, using {@link encodeClass()}.
     *
     * @param array $classNames
     * @param string $package
     * @return string
     */
    static public function encodeClasses($classNames, $package = '')
    {
    	$result = '';
    	foreach ($classNames as $className) {
    	    $result .= self::encodeClass($className, $package);
    	}

    	return $result;
    }

}

