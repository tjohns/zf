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
 * @subpackage Definition
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Basic Property Definition
 *
 * @category   Zend
 * @package    Zend_Entity
 * @subpackage Definition
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Entity_Definition_Property
{
    const FETCH_SELECT    = "select";
    const FETCH_LAZY      = "lazy";
    const FETCH_EAGER     = "eager";

    /**
     * Convert Database field into a PHP integer when retrieved.
     *
     * @var string
     */
    const TYPE_INT       = "int";

    /**
     * Convert Database field into a PHP string when retrieved.
     *
     * For portability storage systems only have to support strings up to a size of 255, larger values may be unportable.
     * In the realm of databases strings are only allowed to be a maximum of 4000 chars were Oracle sets the limit with
     * the smallest of all database VARCHAR types.
     *
     * @var string
     */
    const TYPE_STRING    = "string";

    /**
     * Convert Database field to a PHP boolean when retrieved,
     * and encode entity property to 0 (false) or 1 (true) when persisted.
     *
     * @var string
     */
    const TYPE_BOOLEAN   = "bool";

    /**
     * Convert database field into a PHP Float when retrieved.
     *
     * @var string
     */
    const TYPE_FLOAT     = "float";

    /**
     * Convert database field into a datetime object when retrieved and
     * save as 'Y-m-d' format when persisted.
     *
     * @var string
     */
    const TYPE_DATE      = "date";

    /**
     * Convert database field into a datetime object when retrieved and
     * save as 'Y-m-d H:i:s' format when persisted.
     *
     * @var string
     */
    const TYPE_DATETIME  = "datetime";

    /**
     * Convert database field into a datetime object when retrieved and
     * save as UNIX Timestamp when persisted.
     *
     * @var string
     */
    const TYPE_TIMESTAMP = "timestamp";

    /**
     * Array is converted into simple xml structure.
     *
     * @var string
     */
    const TYPE_ARRAY     = "array";

    /**
     * Large text field
     * 
     * @var string
     */
    const TYPE_TEXT      = "text";

    /**
     * Binary object
     *
     * @var string
     */
    const TYPE_BINARY    = "binary";

    const DB_STRING = 1;

    const LOAD_EXTRA    = "extra";
    const LOAD_LAZY     = "lazy";
    const LOAD_DIRECTLY = "directly";

    const CASCADE_SAVE    = "persist";
    const CASCADE_PERSIST = "persist";
    const CASCADE_DELETE  = "remove";
    const CASCADE_REMOVE  = "remove";
    const CASCADE_MERGE   = "merge";
    const CASCADE_REFRESH = "refresh";
    const CASCADE_DETACH  = "detach";
    const CASCADE_ALL     = "all";

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
    public $nullable = false;

    /**
     * @var int
     */
    public $length = 255;

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
        $this->propertyType = strtolower($type);
    }

    /**
     * @return boolean
     */
    public function isNullable()
    {
        return $this->nullable;
    }

    /**
     * @param boolean $nullable
     */
    public function setNullable($nullable)
    {
        $this->nullable = (bool)$nullable;
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
        $this->_unique = (bool)$unique;
    }

    /**
     * @return int
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     *
     * @param int $length
     */
    public function setLength($length)
    {
        $this->length = (int)$length;
    }
}
