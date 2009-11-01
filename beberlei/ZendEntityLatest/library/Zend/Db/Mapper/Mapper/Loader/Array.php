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
 * @package    Zend_Db
 * @subpackage Mapper
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Array Loader
 *
 * @uses       Zend_Db_Mapper_Loader_LoaderAbstract
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Mapper
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_Mapper_Loader_Array extends Zend_Db_Mapper_Loader_LoaderAbstract
{
    /**
     * @param  array $resultSet
     * @param  string $fetchMode
     * @return Zend_Entity_Collection_Interface
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
        $seenBeforeRootEntities = array();
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
                $joinedEntity = $data[$joinedEntityAlias];

                if($joinedEntityData['parentAlias'] !== null && $joinedEntityData['parentProperty'] !== null) {
                    $data[$joinedEntityData['parentAlias']][$joinedEntityData['parentProperty']] = $joinedEntity;
                } else {
                    throw new Zend_Entity_Query_InvalidResultSetMappingException(
                        "Array result loading mode requires parent entity/alias and property references for joined entities."
                    );
                }
            }
            foreach($rsm->rootEntity AS $aliasName => $entityName) {
                $mapping = $this->_mappings[$entityName];
                /* @var $mapping Zend_Db_Mapper_Mapping */

                $pkName = $mapping->primaryKey->propertyName;

                $entity = $data[$aliasName];

                if(!isset($seenBeforeRootEntities[$entity[$pkName]])) {
                    if($hasScalar) {
                        $result[] = array_merge(array($entity), $scalars);
                    } else {
                        $result[] = $entity;
                    }
                }
            }
        }
        return $result;
    }
}
