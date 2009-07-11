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
     * @param string $entityName
     * @return Zend_Entity_Mapper_Select
     */
    public function createNativeQuery($entityName);

    /**
     * @param string $entityName
     * @return Zend_Entity_Mapper_Query
     */
    public function createQuery($entityName);

    /**
     * Return Query Object for given Entity
     *
     * @deprecated
     * @param  string $entityClass
     * @return object
     */
    public function select($entity);

    /**
     * Find all entities matching query statement
     *
     * @deprecated
     * @param string $entityName
     * @param Zend_Db_Select|string $sql
     * @return Zend_Entity_Collection
     */
    public function find($entityName, $select);

    /**
     * Find one entity matching select statement
     *
     * @deprecated
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
    public function load($entityName, $key);

    /**
     * Save entity by registering it with UnitOfWork or hitting the database mapper.
     *
     * @param  Zend_Entity_Interface $entity
     * @return void
     */
    public function save(Zend_Entity_Interface $entity);

    /**
     * Try to delete entity by checking with UnitOfWork or directly going to mapper.
     *
     * @param  Zend_Entity_Interface $entity
     * @return void
     */
    public function delete(Zend_Entity_Interface $entity);

    /**
     * Refresh object state from the database
     *
     * @param  Zend_Entity_Interface $entity
     * @return void
     */
    public function refresh(Zend_Entity_Interface $entity);

    /**
     * Get a reference of an object.
     *
     * A reference is either a LazyLoad entity of the type {@see Zend_Entity_Mapper_LazyLoad_Entity}
     * or if the entity was loaded before and is found in the identity map the original is used.
     *
     * @param string $class
     * @param int|string $id
     */
    public function getReference($class, $id);

    /**
     * Check if entity instance belongs to the persistence context.
     *
     * @param  Zend_Entity_Interface $entity
     * @return boolean
     */
    public function contains(Zend_Entity_Interface $entity);

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
     * Clear persistence session, rolling back all current changes if transaction is open
     * and deleting the UnitOfWork and Identity Map states.
     */
    public function clear();

    /**
     * Close connection to database, commit transaction if any is open and call clear().
     */
    public function close();

    /**
     * Retrieve Identity Map instance from EntityManager
     *
     * @return Zend_Entity_IdentityMap
     */
    public function getIdentityMap();

    /**
     * @return Zend_Entity_MetadataFactory_Interface
     */
    public function getMetadataFactory();
}