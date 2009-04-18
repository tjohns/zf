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

class Zend_Entity_Mapper_Definition_Relation_ManyToOne extends Zend_Entity_Mapper_Definition_Property
    implements Zend_Entity_Mapper_Definition_Relation_Interface, Zend_Entity_Mapper_Definition_Relation_Reference
{
    protected $_class;

    protected $_foreignKey;
    
    protected $_propertyReference;

    protected $_fetch       = self::FETCH_SELECT;

    protected $_load        = self::LOAD_LAZY;

    protected $_notFound    = self::NOTFOUND_NULL;

    protected $_cascade     = self::CASCADE_NONE;


    public function getClass()
    {
        return $this->_class;
    }

    public function setClass($class)
    {
        $this->_class = $class;
    }

    public function setPropertyRef($foreignPropertyReference)
    {
        $this->_propertyReference = $foreignPropertyReference;
    }

    public function getPropertyRef()
    {
        return $this->_propertyReference;
    }

    public function getForeignKey()
    {
        return $this->_foreignKey;
    }

    public function setForeignKey($foreignKey)
    {
        $this->_foreignKey = $foreignKey;
    }

    public function getFetch()
    {
        return $this->_fetch;
    }

    public function setFetch($fetch)
    {
        $this->_fetch = $fetch;
    }

    public function setNotFound($notFound)
    {
        $this->_notFound = $notFound;
    }

    public function getNotFound()
    {
        return $this->_notFound;
    }

    public function getCascade()
    {
        return $this->_cascade;
    }

    public function setCascade($cascade)
    {
        $this->_cascade = $cascade;
    }

    public function getLoad()
    {
        return $this->_load;
    }

    public function setLoad($load)
    {
        $this->_load = $load;
    }

    public function compile(Zend_Entity_Mapper_Definition_Entity $entityDef, Zend_Entity_Resource_Interface $map)
    {
        if($this->getClass() == null) {
            throw new Exception("Cannot compile definition ManyToOne due to missing class reference for property: ".$this->getPropertyName());
        }
        if($this->getColumnName() == null) {
            $this->setColumnName(($this->getPropertyName()));
        }
        if($this->getPropertyRef() == null) {
            $foreignDef = $map->getDefinitionByEntityName($this->getClass());
            $this->setPropertyRef($foreignDef->getPrimaryKey()->getPropertyName());
        }
        if($this->getForeignKey() == null) {
            $foreignDef = $map->getDefinitionByEntityName($this->getClass());
            $this->setForeignKey($foreignDef->getPrimaryKey()->getColumnName());
        }
    }
}