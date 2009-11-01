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

class Zend_Db_Mapper_Loader_Refresh extends Zend_Db_Mapper_Loader_LoaderAbstract
{
    /**
     * @param  array $resultSet
     * @param  string $fetchMode
     * @return array
     */
    public function processResultset($resultSet, Zend_Entity_Query_ResultSetMapping $rsm)
    {
        $hasOneRootEntity = (count($rsm->rootEntity)==1);

        if(!$hasOneRootEntity) {
            throw new Zend_Entity_Query_InvalidResultSetMappingException(
                "Refresh Loader requires exactly one root entity, but '".count($rsm->rootEntity)."' where defined."
            );
        }

        $isValidated = false;

        
        $identityMap = $this->_em->getIdentityMap();
        /* @var $identityMap Zend_Entity_IdentityMap */

        foreach($resultSet AS $row) {
            if(!$isValidated) {
                $this->_validateResultSet($row, $rsm);
                $isValidated = true;
            }

            $data = array();
            $scalars = array();
            $this->_prepareData($row, $data, $scalars, $rsm);

            foreach($data AS $aliasName => $state) {
                $entityName = $rsm->aliasToEntity[$aliasName];
                $mapping = $this->_mappings[$entityName];
                $pk = $mapping->primaryKey->propertyName;
                $pkValue = $state[$pk];

                
                $entity = $identityMap->getObject($entityName, $pkValue);
                $this->loadState($entity, $state, $mapping);
            }
        }
    }
}