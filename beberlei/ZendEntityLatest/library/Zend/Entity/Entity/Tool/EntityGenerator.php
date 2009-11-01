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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Entity Generator
 *
 * @category   Zend
 * @package    Zend_Entity
 * @subpackage Tool
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Entity_Tool_EntityGenerator
{
    /**
     * @var Zend_Entity_Definition_Entity
     */
    protected $_entityDef;
    
    /**
     * Abstract class name suffix
     *
     * @var string
     */
    protected $_abstractSuffix = 'Abstract';
    
    /**
     * Entity definition setter
     *
     * @param Zend_Entity_Definition_Entity $entityDef 
     * @return void
     */
    public function setEntityDefinition(Zend_Entity_Definition_Entity $entityDef)
    {
        $this->_entityDef = $entityDef;
    }
    
    public function getAbstractSuffix()
    {
        return $this->_abstractSuffix;
    }
    
    public function setAbstractSuffix($abstractSuffix)
    {
        $this->_abstractSuffix = $abstractSuffix;
    }
    
    /**
     * Generate abstract Entity class
     *
     * @return Zend_CodeGenerator_Php_Class
     */
    public function generateClass()
    {
        if (!$this->_entityDef instanceof Zend_Entity_Definition_Entity) {
            /**
             * @see Zend_Entity_Exception
             */
            require_once "Zend/Entity/Exception.php";
            throw new Zend_Entity_Exception(
                "Entity definition must be an instance of Zend_Entity_Definition_Entity."
            );
        }
        
        /**
         * @see Zend_CodeGenerator_Php_Class
         */
        require_once 'Zend/CodeGenerator/Php/Class.php';
        $entityClass = new Zend_CodeGenerator_Php_Class();
        $entityClass->setName($this->_entityDef->getClass() . $this->getAbstractSuffix());
        
        //add generated class comment warning
        $docblock = new Zend_CodeGenerator_Php_Docblock(array(
            'shortDescription' => "{$this->_entityDef->getClass()} abstract class",
            'longDescription'  => 'This class is generated. Do not attempt to modify
                                   as changes will be lost when regenerated.',
        ));
        

        $methods = array();
        foreach($this->_entityDef->getProperties() as $property) {
            //add class property
            $entityClass->setProperty($this->_generateProperty($property));
            
            //add getters / setters for class property
            $propertyMethods = $this->_generatePropertyMethods($property);
            $methods = array_merge($methods, $propertyMethods);
        }
        $entityClass->setMethods($methods);
        
        return $entityClass;
    }
    
    /**
     * Generate Zend_CodeGenerator_Php_Property for Zend_Entity_Definition_Property
     *
     * @param Zend_Entity_Definition_Property $property 
     * @return Zend_CodeGenerator_Php_Property
     */
    protected function _generateProperty(Zend_Entity_Definition_Property $property)
    {
        $phpProperty = new Zend_CodeGenerator_Php_Property();
        $phpProperty->setName($property->getPropertyName());
        $phpProperty->setVisibility(Zend_CodeGenerator_Php_Property::VISIBILITY_PROTECTED);
        return $phpProperty;
    }
    
    /**
     * Generate getters / setters for Zend_Entity_Definition_Property
     *
     * @param Zend_Entity_Definition_Property $property 
     * @return array
     */
    protected function _generatePropertyMethods(Zend_Entity_Definition_Property $property)
    {
        $class = get_class($property);
        switch($class) {
            case 'Zend_Entity_Definition_Property':
                return $this->_generatePropertyGetSetMethods($property);
                break;
                
            case 'Zend_Entity_Definition_ManyToOneRelation':
                return $this->_generateManyToOnePropertyGetSetMethods($property);
                break;
        }
        return array();
    }
    
    /**
     * Generate property getter method
     *
     * @param Zend_Entity_Definition_Property $property 
     * @return Zend_CodeGenerator_Php_Method
     */
    protected function _generatePropertyGetMethod(Zend_Entity_Definition_Property $property)
    {
        $getter = new Zend_CodeGenerator_Php_Method();
        $propertyName = $property->getPropertyName();
        $getter->setName('get' . ucfirst($propertyName));
        $getter->setBody('return $this->' . $propertyName . ';');
        return $getter;
    }
    
    /**
     * Generate getters / setters specifically for plain Zend_Entity_Definition_Property
     *
     * @param Zend_Entity_Definition_Property $property 
     * @return array
     */
    protected function _generatePropertyGetSetMethods(Zend_Entity_Definition_Property $property)
    {
        $getter = $this->_generatePropertyGetMethod($property);
        $propertyName = $property->getPropertyName();
        $setter = new Zend_CodeGenerator_Php_Method();
        $setter->setName('set' . ucfirst($propertyName));
        $setter->setBody('$this->' . $propertyName . ' = $' . $propertyName);
        $parameter = new Zend_CodeGenerator_Php_Parameter();
        $parameter->setName($propertyName);
        $setter->setParameter($parameter);
        
        return array($getter, $setter);
    }
    
    protected function _generateManyToOnePropertyGetSetMethods(Zend_Entity_Definition_Property $property)
    {
        $getter = $this->_generatePropertyGetMethod($property);
        $propertyName = $property->getPropertyName();
        $setter = new Zend_CodeGenerator_Php_Method();
        $setter->setName('set' . ucfirst($propertyName));
        $setter->setBody(
            '$this->' . $propertyName . ' = $' . $propertyName . PHP_EOL .
            '$this->' . $propertyName . '->'
            );
        $parameter = new Zend_CodeGenerator_Php_Parameter();
        $parameter->setName($propertyName);
        $setter->setParameter($parameter);

        return array($getter, $setter);
    }
}