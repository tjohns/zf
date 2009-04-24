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

class Zend_Entity_Mapper_Definition_PrimaryKey extends Zend_Entity_Mapper_Definition_Property
{
    /**
     * @var Zend_Entity_Mapper_Definition_Id_Interface
     */
    protected $_generator;

    /**
     * Get Key String identifier
     * 
     * @return string
     */
    public function getKey()
    {
        return $this->getPropertyName();
    }

    /**
     * Does the database state of an entity contain a valid primary key?
     *
     * @param  array $values
     * @return boolean
     */
    public function containValidPrimaryKey(array $values)
    {
        $key = $this->getColumnName();
        if(!isset($values[$key]) || $values[$key] === null) {
            return false;
        }
        return true;
    }

    /**
     * Build a where condition for finding a specific entity using the primary key.
     * 
     * @param  Zend_Db_Adapter_Abstract $db
     * @param  string $tableName
     * @param  array $forValues
     * @return string
     */
    public function buildWhereCondition(Zend_Db_Adapter_Abstract $db, $tableName, array $forValues)
    {
        if($this->containValidPrimaryKey($forValues)) {
            $primaryKeyValue = $forValues[$this->getKey()];
            $whereCondition = $db->quoteInto(
                sprintf('%s.%s = ?', $tableName, $this->getKey()),
                $primaryKeyValue
            );
        
            return $whereCondition;
        } else {
            require_once "Zend/Entity/Exception.php";
            throw new Zend_Entity_Exception("Invalid data given for matching primary key of table '".$tableName."'.");
        }
    }

    /**
     * Get Id Generator
     * 
     * @return Zend_Entity_Mapper_Definition_Id_Interface
     */
    public function getGenerator()
    {
        return $this->_generator;
    }

    /**
     * Set Id Generator
     *
     * @param Zend_Entity_Mapper_Definition_Id_Interface $generator
     */
    public function setGenerator(Zend_Entity_Mapper_Definition_Id_Interface $generator)
    {
        $this->_generator = $generator;
    }

    /**
     * Compile Primary Key
     *
     * @param Zend_Entity_Mapper_Definition_Entity $entityDef
     * @param Zend_Entity_Resource_Interface $map
     */
    public function compile(Zend_Entity_Mapper_Definition_Entity $entityDef, Zend_Entity_Resource_Interface $map)
    {
        if($this->getColumnName() === null) {
            $this->setColumnName($this->getPropertyName());
        }

        if($this->getGenerator() === null) {
            $this->setGenerator(new Zend_Entity_Mapper_Definition_Id_AutoIncrement());
        }
    }

    /**
     * Apply the net sequence id as column of the entities database state.
     * 
     * @param  Zend_Db_Adapter_Abstract $db
     * @param  array $entityDatabaseState
     * @return array
     */
    public function applyNextSequenceId(Zend_Db_Adapter_Abstract $db, array $entityDatabaseState)
    {
        $sequenceId = $this->getGenerator()->nextSequenceId($db);
        $entityDatabaseState[$this->getColumnName()] = $sequenceId;
        return $entityDatabaseState;
    }

    /**
     *
     * @param  Zend_Db_Adapter_Abstract $db
     * @param  array $entityDatabaseState
     * @return array
     */
    public function getSequenceState(Zend_Db_Adapter_Abstract $db, array $entityDatabaseState)
    {
        $lastId = $this->getGenerator()->lastSequenceId($db);
        $newSequenceState = array($this->getPropertyName() => $lastId);
        return $newSequenceState;
    }

    /**
     * @param  array $databaseState
     * @return array
     */
    public function removeSequenceFromState($databaseState)
    {
        $columnKey = $this->getColumnName();
        if(isset($databaseState[$columnKey])) {
            unset($databaseState[$columnKey]);
        }
        return $databaseState;
    }

    /**
     * Get an array with a null key property.
     * 
     * @return array
     */
    public function getEmptyKeyProperties()
    {
        return array($this->getPropertyName() => null);
    }

    /**
     * Return array of primary key fields values of a state.
     *
     * @param  array $state
     * @return array
     */
    public function retrieveKeyValuesFromProperties(array $state)
    {
        return array(
            $this->getColumnName() => $state[$this->getColumnName()]
        );
    }
}