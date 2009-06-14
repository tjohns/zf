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

require_once "Abstract.php";

class Zend_Entity_Mapper_Loader_Basic extends Zend_Entity_Mapper_Loader_Abstract
{
    /**
     * @param Zend_Db_Select $select
     */
    public function initSelect(Zend_Db_Select $select)
    {
        $select->from($this->_table);
    }

    /**
     * @param Zend_Db_Select $select
     */
    public function initColumns(Zend_Db_Select $select)
    {
        $select->columns($this->_sqlColumnAliasMap);
    }

    /**
     * @param  Zend_Db_Statement_Interface $stmt
     * @param  Zend_Entity_Manager $entityManager
     * @param  string $fetchMode
     * @return array|Zend_Entity_Collection_Interface
     */
    public function processResultset(Zend_Db_Statement_Interface $stmt, Zend_Entity_Manager $entityManager, $fetchMode=Zend_Entity_Manager::FETCH_ENTITIES)
    {
        $unitOfWork = $entityManager->getUnitOfWork();

        $collection = array();
        while($row = $stmt->fetch(Zend_Db::FETCH_ASSOC)) {
            if($fetchMode == Zend_Entity_Manager::FETCH_ARRAY) {
                $entity = $this->renameAndCastColumnToPropertyKeys($row);
            } else {
                $entity = $this->createEntityFromRow($row, $entityManager);

                if($unitOfWork->isManagingCurrentTransaction() == true) {
                    $unitOfWork->registerClean($entity);
                }
            }
            $collection[] = $entity;
        }
        $stmt->closeCursor();

        if($fetchMode == Zend_Entity_Manager::FETCH_ENTITIES) {
            $collection = new Zend_Entity_Collection($collection);
        }
        return $collection;
    }
}