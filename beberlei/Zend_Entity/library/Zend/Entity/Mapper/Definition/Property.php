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
    const FETCH_LAZY      = "lazy";
    const FETCH_EAGER     = "eager";

    /**
     * Convert Database field into a PHP integer when retrieved.
     *
     * @var int
     */
    const TYPE_INT       = 1;

    /**
     * Convert Database field into a PHP string when retrieved.
     */
    const TYPE_STRING    = 2;

    /**
     * Convert Database field to a PHP boolean when retrieved,
     * and encode entity property to 0 (false) or 1 (true) when persisted.
     */
    const TYPE_BOOLEAN   = 3;

    /**
     * Convert database field into a PHP Float when retrieved.
     */
    const TYPE_FLOAT     = 4;

    /**
     * Convert database field into a datetime object when retrieved and
     * save as 'Y-m-d' format when persisted.
     */
    const TYPE_DATE      = 5;

    /**
     * Convert database field into a datetime object when retrieved and
     * save as 'Y-m-d H:i:s' format when persisted.
     */
    const TYPE_DATETIME  = 6;

    /**
     * Convert database field into a datetime object when retrieved and
     * save as UNIX Timestamp when persisted.
     */
    const TYPE_TIMESTAMP = 7;

    /**
     * Array is converted into simple xml structure.
     */
    const TYPE_ARRAY     = 8;

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
     * Compile this property state.
     * 
     * @param Zend_Entity_Mapper_Definition_Entity $entityDef
     * @param Zend_Entity_Resource_Interface $map
     */
    public function compile(Zend_Entity_Mapper_Definition_Entity $entityDef, Zend_Entity_Resource_Interface $map)
    {
        if($this->getColumnName() == null) {
            $this->setColumnName(($this->getPropertyName()));
        }
    }
}