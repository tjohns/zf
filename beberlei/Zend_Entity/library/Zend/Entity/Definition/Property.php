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

class Zend_Entity_Definition_Property
{
    const FETCH_SELECT    = "select";
    const FETCH_LAZY      = "lazy";
    const FETCH_EAGER     = "eager";

    /**
     * Convert Database field into a PHP integer when retrieved.
     *
     * @var int
     */
    const TYPE_INT       = "int";

    /**
     * Convert Database field into a PHP string when retrieved.
     */
    const TYPE_STRING    = "string";

    /**
     * Convert Database field to a PHP boolean when retrieved,
     * and encode entity property to 0 (false) or 1 (true) when persisted.
     */
    const TYPE_BOOLEAN   = "bool";

    /**
     * Convert database field into a PHP Float when retrieved.
     */
    const TYPE_FLOAT     = "float";

    /**
     * Convert database field into a datetime object when retrieved and
     * save as 'Y-m-d' format when persisted.
     */
    const TYPE_DATE      = "date";

    /**
     * Convert database field into a datetime object when retrieved and
     * save as 'Y-m-d H:i:s' format when persisted.
     */
    const TYPE_DATETIME  = "datetime";

    /**
     * Convert database field into a datetime object when retrieved and
     * save as UNIX Timestamp when persisted.
     */
    const TYPE_TIMESTAMP = "timestamp";

    /**
     * Array is converted into simple xml structure.
     */
    const TYPE_ARRAY     = "array";

    const DB_STRING = 1;

    const LOAD_EXTRA    = "extra";
    const LOAD_LAZY     = "lazy";
    const LOAD_DIRECTLY = "directly";

    const CASCADE_ALL     = "all";
    const CASCADE_NONE    = "none";
    const CASCADE_SAVE    = "save";
    const CASCADE_DELETE  = "delete";

    const NOTFOUND_EXCEPTION = "exception";
    const NOTFOUND_NULL      = "null";

    /**
     * @var string
     */
    public $propertyName;

    /**
     * @var string
     */
    public $columnName = null;

    /**
     * @var string
     */
    public $propertyType = Zend_Entity_Definition_Property::TYPE_STRING;

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
    public function __construct($propertyNameOrOptions, $options=array())
    {
        if(is_array($propertyNameOrOptions)) {
            $options = $propertyNameOrOptions;
        } else {
            $options['propertyName'] = $propertyNameOrOptions;
        }

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
    public function castPropertyToStorageType($propertyValue)
    {
        if($propertyValue === null && $this->_isNullable) {
            return $propertyValue;
        }

        switch($this->getPropertyType()) {
            case Zend_Entity_Definition_Property::TYPE_BOOLEAN:
                $propertyValue = ($propertyValue===true||$propertyValue===1)?1:0;
                break;
            case Zend_Entity_Definition_Property::TYPE_INT:
                $propertyValue = (int)$propertyValue;
                break;
            case Zend_Entity_Definition_Property::TYPE_STRING:
                $propertyValue = (string)$propertyValue;
                break;
            case Zend_Entity_Definition_Property::TYPE_FLOAT:
                $propertyValue = (float)$propertyValue;
                break;
            case Zend_Entity_Definition_Property::TYPE_DATE:
                /* @var datetime $propertyValue */
                $propertyValue = $propertyValue->format('Y-m-d');
                break;
            case Zend_Entity_Definition_Property::TYPE_DATETIME:
                /* @var datetime $propertyValue */
                $propertyValue = $propertyValue->format('Y-m-d H:i:s');
                break;
            case Zend_Entity_Definition_Property::TYPE_TIMESTAMP:
                /* @var datetime $propertyValue */
                $propertyValue = $propertyValue->format('U');
                break;
            case Zend_Entity_Definition_Property::TYPE_ARRAY:
                $propertyValue = Zend_Entity_StateTransformer_XmlSerializer::toXml($propertyValue);
                break;

        }
        return $propertyValue;
    }

    public function castColumnToPhpType($columnValue)
    {
        if($columnValue === null && $this->_isNullable) {
            return $columnValue;
        }

        switch($this->propertyType) {
            case Zend_Entity_Definition_Property::TYPE_BOOLEAN:
                $columnValue = ((int)$columnValue==1)?true:false;
                break;
            case Zend_Entity_Definition_Property::TYPE_INT:
                $columnValue = (int)$columnValue;
                break;
            case Zend_Entity_Definition_Property::TYPE_STRING:
                $columnValue = (string)$columnValue;
                break;
            case Zend_Entity_Definition_Property::TYPE_FLOAT:
                $columnValue = (float)$columnValue;
                break;
            case Zend_Entity_Definition_Property::TYPE_DATE:
            case Zend_Entity_Definition_Property::TYPE_DATETIME:
                $columnValue = new DateTime($columnValue);
                break;
            case Zend_Entity_Definition_Property::TYPE_TIMESTAMP:
                $columnValue = (int)$columnValue;
                $columnValue = new DateTime('@'.$columnValue);
                break;
            case Zend_Entity_Definition_Property::TYPE_ARRAY:
                $columnValue = Zend_Entity_StateTransformer_XmlSerializer::fromXml($columnValue);
                break;
        }
        return $columnValue;
    }
}