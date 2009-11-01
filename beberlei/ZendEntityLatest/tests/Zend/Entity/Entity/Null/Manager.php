<?php

class Zend_Entity_Null_Manager implements Zend_Entity_Manager_Interface
{
    /**
     * @var Zend_Entity_Null_Transaction
     */
    public $transaction = null;

    /**
     * @var Zend_Entity_IdentityMap
     */
    public $identityMap = null;

    /**
     * @var Zend_Entity_Null_Mapper
     */
    public $mapper = null;

    /**
     * @var Zend_Entity_MetadataFactory_Testing
     */
    public $metadata = null;

    public function __construct()
    {
        $this->transaction = new Zend_Entity_Null_Transaction();
        $this->identityMap = new Zend_Entity_IdentityMap();
        $this->mapper = new Zend_Entity_Null_Mapper();
        $this->metadata = new Zend_Entity_MetadataFactory_Testing();
    }

    /**
     * @param string $entityName
     * @param Zend_Entity_Query_ResultSetMapping
     * @return Zend_Entity_Query_QueryAbstract
     */
    public function createNativeQuery($nativeInput, $resultSetMapping=null)
    {

    }

    /**
     * @param string $queryName
     * @return Zend_Entity_Query_QueryAbstract
     */
    public function createNamedQuery($queryName)
    {

    }

    /**
     * Find by primary key
     *
     * @param string $entityName
     * @param string $key
     * @param string $notFound
     * @return object
     */
    public function load($entityName, $key, $notFound="null")
    {

    }

    /**
     * Save entity by registering it with UnitOfWork or hitting the database mapper.
     *
     * @param  object $entity
     * @return void
     */
    public function save($entity)
    {

    }

    /**
     * Try to delete entity by checking with UnitOfWork or directly going to mapper.
     *
     * @param  object $entity
     * @return void
     */
    public function delete($entity)
    {

    }

    /**
     * Merge the state of a detached entity which has an identity back into the persistence context.
     *
     * @param object $entity
     * @return void
     */
    public function merge($entity)
    {

    }

    /**
     * Refresh the state of an entity.
     *
     * @param object $entity
     * @return void
     */
    public function refresh($entity)
    {

    }

    /**
     * Get a reference of an object.
     *
     * A reference is either a LazyLoad entity of the type {@see Zend_Entity_LazyLoad_Entity}
     * or if the entity was loaded before and is found in the identity map the original is used.
     *
     * @param string $class
     * @param int|string $id
     */
    public function getReference($class, $id)
    {

    }

    /**
     * Detach entity from persistence context, so that it will become unmanaged. Any unflushed changes will be lost.
     *
     * @param object $entity
     */
    public function detach($entity)
    {
        $this->identityMap->remove($entity);
    }

    /**
     * Check if entity instance belongs to the persistence context.
     *
     * @param  object $entity
     * @return boolean
     */
    public function contains($entity)
    {
        return $this->identityMap->contains($entity);
    }

    /**
     * Begin new transaction and return the Zend_Entity_Transaction instance
     *
     * @retun Zend_Entity_Transaction
     */
    public function beginTransaction()
    {
        return $this->transaction;
    }

    /**
     * Get the entity transcation
     *
     * @return Zend_Entity_Transaction
     */
    public function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * Clear persistence session, rolling back all current changes if transaction is open
     * and deleting the UnitOfWork and Identity Map states.
     */
    public function clear()
    {
        $this->identityMap->clear();
    }

    /**
     * Close connection to database, commit transaction if any is open and call clear().
     */
    public function close()
    {

    }

    /**
     * Retrieve Identity Map instance from EntityManager
     *
     * @return Zend_Entity_IdentityMap
     */
    public function getIdentityMap()
    {
        return $this->identityMap;
    }

    /**
     * @return Zend_Entity_MetadataFactory_FactoryAbstract
     */
    public function getMetadataFactory()
    {
        return $this->metadata;
    }

    /**
     * @return Zend_Entity_MapperAbstract
     */
    public function getMapper()
    {
        return $this->mapper;
    }
}