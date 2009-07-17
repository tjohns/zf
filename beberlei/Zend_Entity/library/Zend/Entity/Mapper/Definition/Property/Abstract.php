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

abstract class Zend_Entity_Mapper_Definition_Property_Abstract
{
    /**
     * @var string
     */
    protected $propertyName;

    /**
     * @var string
     */
    protected $columnName = null;

    /**
     * @var string
     */
    protected $propertyType = Zend_Entity_Mapper_Definition_Property::TYPE_STRING;

    /**
     * @var boolean
     */
    protected $_isNullable = false;

    /**
     * @var boolean
     */
    protected $_unique = false;

    /**
     * Construct a property and call existing methods for all options if present.
     *
     * @param string $propertyName
     * @param array $options
     */
    public function __construct($propertyName, $options=array())
    {
        $this->setPropertyName($propertyName);
        if(is_array($options)) {
            foreach($options AS $k => $v) {
                $method = "set".ucfirst($k);
                if(method_exists($this, $method)) {
                    call_user_func_array(array($this, $method), array($v));
                }
            }
        }
    }

    /**
     * @return string
     */
    public function getPropertyName()
    {
        return $this->propertyName;
    }

    /**
     * @param string $propertyName
     */
    public function setPropertyName($propertyName)
    {
        $this->propertyName = $propertyName;
    }

    /**
     * Get Database column name of this property.
     *
     * @return string
     */
    public function getColumnName()
    {
        return $this->columnName;
    }

    /**
     * Get Database column name of this property.
     *
     * @return string
     */
    public function getColumnSqlName()
    {
        return $this->getColumnName();
    }

    /**
     * Set database column name of this property.
     *
     * @param string $columnName
     */
    public function setColumnName($columnName)
    {
        $this->columnName = $columnName;
    }

    /**
     * Get type of this property.
     *
     * @return int
     */
    public function getPropertyType()
    {
        return $this->propertyType;
    }

    /**
     * Set type of this property.
     *
     * @param int $type
     */
    public function setPropertyType($type)
    {
        $this->propertyType = $type;
    }

    /**
     * @return boolean
     */
    public function isNullable()
    {
        return $this->_isNullable;
    }

    /**
     * @param boolean $nullable
     */
    public function setNullable($nullable)
    {
        $this->_isNullable = $nullable;
    }

    /**
     * @return boolean
     */
    public function isUnique()
    {
        return $this->_unique;
    }

    /**
     * @param boolean $unique
     */
    public function setUnique($unique)
    {
        $this->_unique = $unique;
    }

    /**
     * @param  mixed $propertyValue
     * @return mixed
     */
    public function castPropertyToSqlType($propertyValue)
    {
        // TODO: Nullable
        if($propertyValue === null) {
            return $propertyValue;
        }

        switch($this->getPropertyType()) {
            case Zend_Entity_Mapper_Definition_Property::TYPE_BOOLEAN:
                $propertyValue = ($propertyValue!=false)?1:0;
                break;
            case Zend_Entity_Mapper_Definition_Property::TYPE_INT:
                $propertyValue = (int)$propertyValue;
                break;
            case Zend_Entity_Mapper_Definition_Property::TYPE_STRING:
                $propertyValue = (string)$propertyValue;
                break;
            case Zend_Entity_Mapper_Definition_Property::TYPE_FLOAT:
                $propertyValue = (float)$propertyValue;
                break;
            case Zend_Entity_Mapper_Definition_Property::TYPE_DATE:
                /* @var datetime $propertyValue */
                $propertyValue = $propertyValue->format('Y-m-d');
                break;
            case Zend_Entity_Mapper_Definition_Property::TYPE_DATETIME:
                /* @var datetime $propertyValue */
                $propertyValue = $propertyValue->format('Y-m-d H:i:s');
                break;
            case Zend_Entity_Mapper_Definition_Property::TYPE_TIMESTAMP:
                /* @var datetime $propertyValue */
                $propertyValue = $propertyValue->format('U');
                break;
            case Zend_Entity_Mapper_Definition_Property::TYPE_ARRAY:
                $propertyValue = Zend_Entity_StateTransformer_XmlSerializer::toXml($propertyValue);
                break;

        }
        return $propertyValue;
    }

    public function castColumnToPhpType($columnValue)
    {
        // TODO: Nullable
        if($columnValue === null) {
            return $columnValue;
        }

        switch($this->getPropertyType()) {
            case Zend_Entity_Mapper_Definition_Property::TYPE_BOOLEAN:
                $columnValue = ((int)$columnValue==1)?true:false;
                break;
            case Zend_Entity_Mapper_Definition_Property::TYPE_INT:
                $columnValue = (int)$columnValue;
                break;
            case Zend_Entity_Mapper_Definition_Property::TYPE_STRING:
                $columnValue = (string)$columnValue;
                break;
            case Zend_Entity_Mapper_Definition_Property::TYPE_FLOAT:
                $columnValue = (float)$columnValue;
                break;
            case Zend_Entity_Mapper_Definition_Property::TYPE_DATE:
            case Zend_Entity_Mapper_Definition_Property::TYPE_DATETIME:
                $columnValue = new DateTime($columnValue);
                break;
            case Zend_Entity_Mapper_Definition_Property::TYPE_TIMESTAMP:
                $columnValue = (int)$columnValue;
                $columnValue = new DateTime('@'.$columnValue);
                break;
        }
        return $columnValue;
    }

    /**
     * @param Zend_Entity_Mapper_Definition_Entity $entityDef
     * @param Zend_Entity_MetadataFactory_Interface $map
     */
    abstract public function compile(Zend_Entity_Mapper_Definition_Entity $entityDef, Zend_Entity_MetadataFactory_Interface $map);
}