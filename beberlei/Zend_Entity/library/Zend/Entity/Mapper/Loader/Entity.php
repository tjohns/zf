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

class Zend_Entity_Mapper_Loader_Entity extends Zend_Entity_Mapper_Loader_LoaderAbstract
{
    /**
     * @param  array $resultSet
     * @param  string $fetchMode
     * @return Zend_Entity_Collection_Interface
     */
    public function processResultset($resultSet, Zend_Entity_Mapper_ResultSetMapping $rsm)
    {
        $result = array();
        foreach($resultSet AS $row) {
            foreach($rsm->entityResult AS $entityName => $entityDef) {
                $mapping = $this->_mappings[$entityName];
                if($entityDef['joined'] == false) {
                    $result[] = $this->createEntityFromRow($row, $mapping);
                }
            }
        }
        return $result;
    }
}