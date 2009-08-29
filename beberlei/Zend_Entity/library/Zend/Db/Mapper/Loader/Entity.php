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

class Zend_Db_Mapper_Loader_Entity extends Zend_Db_Mapper_Loader_LoaderAbstract
{
    /**
     * @param  array $resultSet
     * @param  string $fetchMode
     * @return array
     */
    public function processResultset($resultSet, Zend_Entity_Query_ResultSetMapping $rsm)
    {
        $hasEntity = count($rsm->entityResult)>0;

        if(!$hasEntity) {
            throw new Zend_Entity_Exception("Cannot load by entity strategy when no entity is part of the ResultSetMapping.");
        }

        $hasScalar = count($rsm->scalarResult)>0;

        $result = array();
        foreach($resultSet AS $row) {
            $data = array();
            $scalars = array();
            foreach($row AS $k => $v) {
                if(in_array($k, $rsm->scalarResult)) {
                    $scalars[$k] = $v;
                } else {
                    $entityName = $rsm->storageFieldEntity[$k];
                    $propertyName = $rsm->entityResult[$entityName]['properties'][$k];
                    $data[$entityName][$propertyName] = $v;
                }
            }
            foreach($rsm->joinedEntity AS $joinedEntity => $joinedEntityData) {
                $mapping = $this->_mappings[$joinedEntity];
                $this->createEntityFromRow($row, $mapping);
            }
            foreach($rsm->rootEntity AS $entityName) {
                $mapping = $this->_mappings[$entityName];

                $entity = $this->createEntityFromRow($row, $mapping);
                if($hasScalar) {
                    $result[] = array_merge(array($entity), $scalars);
                } else {
                    $result[] = $entity;
                }
            }
        }
        return $result;
    }
}