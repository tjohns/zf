<?php

interface Zend_Entity_Manager_Interface
{
    /**
     * Return concrete mapper implementation of the given Entity Type
     *
     * @param  string|Zend_Entity_Interface $entity
     * @return Zend_Db_Mapper_Interface
     */
    public function getMapperByEntity($entity);

    /**
     * Return Query Object for given Entity
     *
     * @param  string $entityClass
     * @return object
     */
    public function select($entity);

    /**
     * Find all entities matching Query Object statement
     *
     * @param object $select
     * @param string $entityName
     */
    public function find($entityName, $select);

    /**
     * Find one entity matching select statement
     *
     * @param string $entityName
     * @param object $select
     * @return Zend_Entity_Interface
     */
    public function findOne($entityName, $select);

    /**
     * Find all entities of a type
     *
     * @param string $entityName
     * @param string $subcondition
     * @param int    $limit
     * @param string $order
     * @return Zend_Entity_Collection
     */
    public function findAll($entityName, $subcondition=null, $limit=null, $order=null);

    /**
     * Find by primary key
     *
     * @param string $entityName
     * @param string $key
     * @return Zend_Entity_Interface
     */
    public function findByKey($entityName, $key);

    /**
     * Save entity by registering it with UnitOfWork or hitting the database mapper.
     *
     * @param  Zend_Entity_Interface $record
     * @return void
     */
    public function save(Zend_Entity_Interface $entity);

    /**
     * Try to delete entity by checking with UnitOfWork or directly going to mapper.
     *
     * @param  Zend_Entity_Interface $record
     * @return void
     */
    public function delete(Zend_Entity_Interface $entity);

    /**
     * Refresh object state from the database
     *
     * @param  Zend_Entity_Interface $record
     * @return void
     */
    public function refresh(Zend_Entity_Interface $entity);

    /**
     * Retrieve the underyling datasource adapter
     *
     * @return object
     */
    public function getAdapter();

    /**
     * Tell Unit Of work to begin transaction
     *
     * @retun void
     */
    public function beginTransaction();

    /**
     * Tell Unit of Work to commit transaction.
     */
    public function commit();

    /**
     * Tell Unit of Work to rollback transaction.
     */
    public function rollBack();

    /**
     * Flush Transaction to database and immediatly open up new one.
     */
    public function flush();

    /**
     * Clear persistence session, rolling back all current changes if transaction is open
     * and deleting the UnitOfWork and Identity Map states.
     */
    public function clear();

    /**
     * Close connection to database, commit transaction if any is open and call clear().
     */
    public function closeConnection();

    /**
     * Set this session to read only, which might lead to faster object destruction and memory management.
     *
     * @return void
     */
    public function setReadOnly();

    /**
     * Retrieve Unit of Work instance from the EntityManager
     *
     * @return Zend_Db_Mapper_UnitOfWork_Interface
     */
    public function getUnitOfWork();

    /**
     * Retrieve Identity Map instance from EntityManager
     *
     * @return Zend_Db_Mapper_IdentityMap
     */
    public function getIdentityMap();
}