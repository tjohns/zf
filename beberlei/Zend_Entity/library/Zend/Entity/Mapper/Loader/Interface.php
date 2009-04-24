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

interface Zend_Entity_Mapper_Loader_Interface
{
    /**
     * Initialize the Select Object by configuring certain query options upfront that are required by this loader.
     * 
     * @param Zend_Db_Select $select
     */
    public function initSelect(Zend_Db_Select $select);

    /**
     * Initialize the columns required for selecting the entity
     * 
     * @param Zend_Db_Select $select
     */
    public function initColumns(Zend_Db_Select $select);

    /**
     * @param  Zend_Db_Statement_Interface $stmt
     * @param  Zend_Entity_Manager $entityManager
     * @param  string $fetchMode
     * @return Zend_Entity_Collection_Interface
     */
    public function processResultset(Zend_Db_Statement_Interface $stmt, Zend_Entity_Manager $entityManager, $fetchMode=Zend_Entity_Manager::FETCH_ENTITIES);

    public function loadRow(array $row, Zend_Entity_Manager $entityManager);
}