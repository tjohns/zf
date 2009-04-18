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

class Zend_Entity_Mapper_Definition_Property extends Zend_Entity_Mapper_Definition_Property_Abstract
{
    const FETCH_SELECT    = "select";
    const FETCH_JOIN      = "join";
    const FETCH_SUBSELECT = "subselect";

    const TYPE_INT     = 1;
    const TYPE_STRING  = 2;
    const TYPE_BOOLEAN = 3;

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

    protected $propertyName = null;

    protected $columnName = null;

    protected $propertyType = self::TYPE_STRING;

    public function getColumnName()
    {
        return $this->columnName;
    }

    public function getColumnSqlName()
    {
        return $this->getColumnName();
    }

    public function setColumnName($columnName)
    {
        $this->columnName = $columnName;
    }

    public function getPropertyType()
    {
        return $this->propertyType;
    }

    public function setPropertyType($type)
    {
        $this->propertyType = $type;
    }

    public function compile(Zend_Entity_Mapper_Definition_Entity $entityDef, Zend_Entity_Resource_Interface $map)
    {
        if($this->getColumnName() == null) {
            $this->setColumnName(($this->getPropertyName()));
        }
    }
}