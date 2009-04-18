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
    protected $generator;

    public function getKey()
    {
        return $this->getPropertyName();
    }

    public function uniqueStringIdentifier($forValues)
    {
        // TODO: Compound and matching
        if(is_string($forValues)) {
            return md5($forValues);
        } else if(is_array($forValues)) {
            if(isset($forValues[$this->getKey()])) {
                $forValues = $forValues[$this->getKey()];
                return md5($forValues);
            }
        }
    }

    public function containValidPrimaryKey($values)
    {
        $key = $this->getColumnName();
        if(!isset($values[$key]) || $values[$key] === null) {
            return false;
        }
        return true;
    }

    public function buildWhereCondition(Zend_Db_Adapter_Abstract $db, $tableName, $forValues)
    {
        if($this->containValidPrimaryKey($forValues)) {
            $whereCondition = array();
            foreach($forValues AS $k => $v) {
                if($k == $this->getColumnName()) {
                    $whereCondition[] = $db->quoteInto(
                        sprintf('%s.%s = ?', $tableName, $k
                    ), $v);
                }
            }
            return implode(" AND ", $whereCondition);
        } else {
            throw new Exception("Invalid key data given.");
        }
    }

    public function getGenerator()
    {
        return $this->generator;
    }

    public function setGenerator($generator)
    {
        $this->generator = $generator;
    }

    public function compile(Zend_Entity_Mapper_Definition_Entity $entityDef, Zend_Entity_Resource_Interface $map)
    {
        if($this->getColumnName() === null) {
            $this->setColumnName($this->getPropertyName());
        }

        if($this->getGenerator() === null) {
            $this->setGenerator(new Zend_Entity_Mapper_Definition_Id_AutoIncrement());
        }
    }

    public function applyNextSequenceId(Zend_Db_Adapter_Abstract $db, $entityDatabaseState)
    {
        $sequenceId = $this->getGenerator()->nextSequenceId($db);
        $entityDatabaseState[$this->getColumnName()] = $sequenceId;
        return $entityDatabaseState;
    }

    public function getSequenceState(Zend_Db_Adapter_Abstract $db, $entityDatabaseState)
    {
        $lastId = $this->getGenerator()->lastSequenceId($db);

        $newSequenceState = array($this->getPropertyName() => $lastId);
        return $newSequenceState;
    }

    public function removeSequenceFromState($databaseState)
    {
        unset($databaseState[$this->getColumnName()]);
        return $databaseState;
    }

    public function getEmptyKeyProperties()
    {
        return array($this->getPropertyName() => null);
    }

    public function retrieveKeyValuesFromProperties(array $state)
    {
        $key = array();
        foreach($state AS $k => $v) {
            if($k == $this->getPropertyName()) {
                $key[$this->getPropertyName()] = $v;
                break;
            }
        }
        return $key;
    }
}