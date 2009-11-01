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
            throw new Zend_Entity_Exception(
                "Cannot load by entity strategy when no entity ".
                "is part of the ResultSetMapping."
            );
        }

        $hasScalar = count($rsm->scalarResult)>0;

        $result = array();
        $seenBeforeRootEntities = new SplObjectStorage();
        $isValidated = false;
        
        foreach($resultSet AS $row) {
            if(!$isValidated) {
                $this->_validateResultSet($row, $rsm);
                $isValidated = true;
            }

            $data = array();
            $scalars = array();
            $this->_prepareData($row, $data, $scalars, $rsm);
            
            foreach($rsm->joinedEntity AS $joinedEntityAlias => $joinedEntityData) {
                $joinedEntity = $rsm->aliasToEntity[$joinedEntityAlias];
                $mapping = $this->_mappings[$joinedEntity];
                $joinedEntity = $this->createEntityFromState($data[$joinedEntityAlias], $mapping);

                if($joinedEntityData['parentAlias'] !== null && $joinedEntityData['parentProperty'] !== null) {
                    $data[$joinedEntityData['parentAlias']][$joinedEntityData['parentProperty']] = $joinedEntity;
                }
            }
            foreach($rsm->rootEntity AS $aliasName => $entityName) {
                $mapping = $this->_mappings[$entityName];

                $entity = $this->createEntityFromState($data[$aliasName], $mapping);

                if($seenBeforeRootEntities->contains($entity)) {
                    continue;
                }
                
                $seenBeforeRootEntities->attach($entity);

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