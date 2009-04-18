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

interface Zend_Entity_Mapper_Interface
{
    public function findByKey($key, Zend_Entity_Manager $entityManager);

    public function find(Zend_Db_Select $select, Zend_Entity_Manager $entityManager);

    public function findOne(Zend_Db_Select $select, Zend_Entity_Manager $entityManager);

    /**
     * @return Zend_Db_Select
     */
    public function select();

    /**
     * Save Entity
     * 
     * @param Zend_Entity_Interface $entity
     */
    public function save(Zend_Entity_Interface $entity, Zend_Entity_Manager $entityManager);

    /**
     * Delete Entity
     *
     * @param Zend_Entity_Interface $entity
     */
    public function delete(Zend_Entity_Interface $entity, Zend_Entity_Manager $entityManager);

    /**
     * @return Zend_Entity_Mapper_Definition_EntityClass
     */
    public function getDefinition();

    /**
     * @return string
     */
    public function getEntityInterfaceTypeResponsibleFor();
}