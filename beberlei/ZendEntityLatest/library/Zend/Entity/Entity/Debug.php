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
 * @package    Zend_Entity
 * @subpackage Debug
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Allows to dump data-structures that potentially containt he Entity Manager with its deep recursive data structure.
 *
 * @category   Zend
 * @package    Zend_Entity
 * @subpackage Debug
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Entity_Debug
{
    /**
     * @param mixed $variable
     */
    static public function dump($variable, $maxDepth=2)
    {
        $visited = array();
        var_dump(self::_deepExtract($variable, $maxDepth, $visited));
    }

    static protected function _deepExtract($variable, $maxDepth=2, &$visited=array())
    {
        if($maxDepth <= 0) {
            return "*MAXDEPTHREACHED*";
        }

        if(is_object($variable)) {
            $hash = spl_object_hash($variable);
            if(isset($visited[$hash])) {
                $data = "*RECURSION*";
            } else {
                $data[$hash] = true;
                $refl = new ReflectionObject($variable);
                $data = array();
                $data['__CLASSNAME__'] = $refl->getName();
                if(!($variable instanceof Zend_Entity_Manager_Interface)) {
                    foreach($refl->getProperties() AS $property) {
                        $propertyName = $property->getName();
                        $value = self::_readObjectAttribute($variable, $property);;
                        $data[$propertyName] = self::_deepExtract($value, $maxDepth-1, $visited);
                    }
                } else {
                    $data = "*ENTITYMANAGER*";
                }
            }
        } elseif(is_array($variable)) {
            $data = array();
            foreach($variable AS $k => $v) {
                $data[$k] = self::_deepExtract($v, $maxDepth-1, $visited);
            }
        } else {
            $data = $variable;
        }
        return $data;
    }

    /**
     * @link http://www.phpunit.de/browser/phpunit/branches/release/3.3/PHPUnit/Framework/Assert.php
     * @author     Sebastian Bergmann <sb@sebastian-bergmann.de>
     * @copyright  2002-2009 Sebastian Bergmann <sb@sebastian-bergmann.de>
     * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @param  object $object
     * @param  ReflectionProperty $attribute
     * @return mixed
     */
    protected static function _readObjectAttribute($object, $attribute)
    {
        $attributeName = $attribute->getName();
        if ($attribute->isPublic()) {
            return $object->$attributeName;
        } else {
            $array         = (array)$object;
            $protectedName = "\0*\0" . $attributeName;

            if (array_key_exists($protectedName, $array)) {
                return $array[$protectedName];
            } else {
                $classes = self::_getHierarchy(get_class($object));

                foreach ($classes as $class) {
                    $privateName = sprintf(
                      "\0%s\0%s",

                      $class,
                      $attributeName
                    );

                    if (array_key_exists($privateName, $array)) {
                        return $array[$privateName];
                    }
                }
            }
        }
        return "*NOTFOUND*";
    }

    /**
     * Returns the class hierarchy for a given class.
     *
     * @link http://www.phpunit.de/browser/phpunit/branches/release/3.3/PHPUnit/Util/Class.php
     * @author     Sebastian Bergmann <sb@sebastian-bergmann.de>
     * @copyright  2002-2009 Sebastian Bergmann <sb@sebastian-bergmann.de>
     * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @param  string  $className
     * @param  boolean $asReflectionObjects
     * @return array
     */
    protected static function _getHierarchy($className, $asReflectionObjects = FALSE)
    {
        $classes = array($className);

        $done = FALSE;

        while (!$done) {
            $class = new ReflectionClass($classes[count($classes)-1]);

            $parent = $class->getParentClass();

            if ($parent !== FALSE) {
                $classes[] = $parent->getName();
            } else {
                $done = TRUE;
            }
        }

        return $classes;
    }
}