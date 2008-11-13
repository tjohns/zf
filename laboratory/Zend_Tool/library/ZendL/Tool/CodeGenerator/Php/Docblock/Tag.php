<?php

require_once 'ZendL/Tool/CodeGenerator/Abstract.php';
require_once 'Zend/Loader.php';

class ZendL_Tool_CodeGenerator_Php_Docblock_Tag extends ZendL_Tool_CodeGenerator_Abstract
{

    protected static $_tagClasses = array(
        'param'  => 'ZendL_Tool_CodeGenerator_Php_Docblock_Tag_Param',
        'return' => 'ZendL_Tool_CodeGenerator_Php_Docblock_Tag_Return',
        );

    protected $_name = null;
        
    public static function fromReflection(ZendL_Reflection_Docblock_Tag $reflectionTag)
    {
        $tagName = $reflectionTag->getName();
        
        if (array_key_exists($tagName, self::$_tagClasses)) {
            $tagClass = self::$_tagClasses[$tagName];
            if (!class_exists($tagClass)) {
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
    
    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }
    
    public function getName()
    {
        return $this->_name;
    }
    
    public function setDescription($description)
    {
        $this->_description = $description;
        return $this;
    }
    
    public function getDescription()
    {
        return $this->_description;
    }
        
    public function generate() {}
    
}