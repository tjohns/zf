<?php
/**
 * Mapper
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.
 * 
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to kontakt@beberlei.de so we can send you a copy immediately.
 *
 * @category   Zend
 * @category   Zend_Entity
 * @copyright  Copyright (c) 2009 Benjamin Eberlei
 * @license    New BSD License
 */

class Zend_Entity_Definition_Join extends Zend_Entity_Definition_Table
{
    /**
     * @var string
     */
    protected $_propertyName = null;

    /**
     * @var string
     */
    protected $_key;

    /**
     * @var boolean
     */
    protected $_optional = false;

    /**
     * Construct a join-table that is loaded into the entity.
     * 
     * @param string $propertyName
     * @param array $options
     */
    public function __construct($propertyName, array $options=array())
    {
        $this->setPropertyName($propertyName);
        parent::__construct($propertyName, $options);
    }

    /**
     * Get property name
     *
     * @return string
     */
    public function getPropertyName()
    {
        return $this->_propertyName;
    }

    /**
     * Set property name
     * 
     * @param  string $propertyName
     * @return void
     */
    public function setPropertyName($propertyName)
    {
        $this->_propertyName = $propertyName;
    }

    /**
     * Set Key of join table.
     * 
     * @param string $key
     */
    public function setKey($key)
    {
        $this->_key = $key;
    }

    /**
     * Get key of join table.
     * 
     * @return string
     */
    public function getKey()
    {
        return $this->_key;
    }

    /**
     * Are values in the join table optional or not?
     *
     * @return boolean
     */
    public function getOptional()
    {
        return $this->_optional;
    }

    /**
     * Set join table optional status.
     * 
     * @param boolean $optional
     */
    public function setOptional($optional)
    {
        $this->_optional = $optional;
    }
    
    /**
     * Compile Join-Table
     *
     * @param  Zend_Entity_Definition_Entity $entityDef
     * @param  Zend_Entity_MetadataFactory_Interface $map
     * @return void
     */
    public function compile(Zend_Entity_Definition_Entity $entityDef, Zend_Entity_MetadataFactory_Interface $map)
    {
        foreach($this->getProperties() AS $property) {
            $property->compile($entityDef, $map);
        }
    }
}