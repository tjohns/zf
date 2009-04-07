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
 * @package    Zend_CodeGenerator
 * @subpackage PHP
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @see Zend_CodeGenerator_Abstract
 */
require_once 'Zend/CodeGenerator/Abstract.php';

/**
 * @see Zend_CodeGenerator_Php_Docblock_Tag_Param
 */
require_once 'Zend/CodeGenerator/Php/Docblock/Tag/Param.php';

/**
 * @see Zend_CodeGenerator_Php_Docblock_Tag_Return
 */
require_once 'Zend/CodeGenerator/Php/Docblock/Tag/Return.php';

/**
 * @category   Zend
 * @package    Zend_CodeGenerator
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_CodeGenerator_Php_Docblock_Tag extends Zend_CodeGenerator_Abstract
{

    /**
     * @var array
     */
    protected static $_tagClasses = array(
        'param'  => 'Zend_CodeGenerator_Php_Docblock_Tag_Param',
        'return' => 'Zend_CodeGenerator_Php_Docblock_Tag_Return',
        );

    /**
     * @var string
     */
    protected $_name = null;
    
    /**
     * @var string
     */
    protected $_description = null;

    /**
     * fromReflection()
     *
     * @param Zend_Reflection_Docblock_Tag $reflectionTag
     * @return Zend_CodeGenerator_Php_Docblock_Tag
     */
    public static function fromReflection(Zend_Reflection_Docblock_Tag $reflectionTag)
    {
        $tagName = $reflectionTag->getName();
        
        if (array_key_exists($tagName, self::$_tagClasses)) {
            $tagClass = self::$_tagClasses[$tagName];
            if (!class_exists($tagClass)) {
                require_once 'Zend/Loader.php';
                Zend_Loader::loadClass($tagClass);
            }
            $tag = call_user_func(array($tagClass, 'fromReflection'), $reflectionTag); 
        } else {
            $tag = new self();
            $tag->setName($reflectionTag->getName());
            $tag->setDescription($reflectionTag->getDescription());
        }
        
        return $tag;
    }
    
    /**
     * setName()
     *
     * @param string $name
     * @return Zend_CodeGenerator_Php_Docblock_Tag
     */
    public function setName($name)
    {
        $this->_name = ltrim($name, '@');
        return $this;
    }
    
    /**
     * getName()
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }
    
    /**
     * setDescription()
     *
     * @param string $description
     * @return Zend_CodeGenerator_Php_Docblock_Tag
     */
    public function setDescription($description)
    {
        $this->_description = $description;
        return $this;
    }
    
    /**
     * getDescription()
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * generate()
     *
     * @return string
     */
    public function generate()
    {
        return '@' . $this->_name . ' ' . $this->_description . PHP_EOL;
    }
    
}