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

class Zend_Entity_Mapper_UnitOfWork
{
    const STATE_CLEAN   = 1;
    const STATE_NEW     = 2;
    const STATE_DIRTY   = 4;
    const STATE_DELETED = 8;

    /**
     * Database Adapter of this UnitOfWork
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db;

    /**
     * Entity Manager this UnitOfWork belongs to.
     *
     * @var Zend_Entity_Manager_Interface
     */
    protected $_entityManager;

    /**
     * Entities and their state in regards to the UnitOfWork
     *
     * @var array
     */
    protected $_entities = array();

    /**
     * Transaction nested inside UnitOfWork currently enabled?
     * 
     * @var boolean
     */
    protected $_currentlyEnabled = false;

    /**
     * Read Only flag. Does not save entitys.
     *
     * @var boolean
     */
    protected $_readOnly = false;

    /**
     * Construct new UnitOfWork object
     *
     * @param Zend_Db_Adapter_Abstract $db
     * @param Zend_Entity_Manager_Interface $entityManager
     */
    public function __construct(Zend_Db_Adapter_Abstract $db=null, Zend_Entity_Manager_Interface $entityManager=null)
    {
        if($db !== null) {
            $this->setAdapter($db);
        }
        if($entityManager !== null) {
            $this->setManager($entityManager);
        }
    }

    /**
     * Set Adapter for this UnitOfWork
     *
     * @param  Zend_Db_Adapter_Abstract $db
     * @return Zend_Entity_Mapper_UnitOfWork
     */
    public function setAdapter(Zend_Db_Adapter_Abstract $db)
    {
        $this->_db = $db;
        return $this;
    }

    /**
     * Set Entity Manager of this UnitOfWork
     * 
     * @param  Zend_Entity_Manager_Interface $entityManager
     * @return Zend_Entity_Mapper_UnitOfWork
     */
    public function setManager(Zend_Entity_Manager_Interface $entityManager)
    {
        $this->_entityManager = $entityManager;
    }

    /**
     * Return the current state the given entitiy is in.
     *
     * @param  Zend_Entity_Interface $entity
     * @return string
     */
    public function getState(Zend_Entity_Interface $entity)
    {
        $hash = spl_object_hash($entity);
        if(!isset($this->_entities[$hash])) {
            return Zend_Entity_Mapper_UnitOfWork::STATE_DIRTY;
        }
        return $this->_entities[$hash]['state'];
    }

    /**
     * Actual registering task for entities.
     *
     * @param  Zend_Entity_Interface $entity
     * @param  string $state
     * @return void
     */
    protected function _register(Zend_Entity_Interface $entity, $state)
    {
        if($this->isManagingCurrentTransaction() == true && $this->_readOnly == false) {
            $hash = spl_object_hash($entity);
            $this->_entities[$hash] = array(
                'object' => $entity,
                'state'  => $state,
            );
        }
    }

    /**
     * Register entity as new and to be saved into the database.
     *
     * @param Zend_Entity_Interface $entity
     */
    public function registerNew(Zend_Entity_Interface $entity)
    {
        $this->_register($entity, Zend_Entity_Mapper_UnitOfWork::STATE_NEW);
    }

    /**
     * Mark the entity as clean.
     *
     * @param  Zend_Entity_Interface $entity
     * @return void
     */
    public function registerClean(Zend_Entity_Interface $entity)
    {
        $this->_register($entity, Zend_Entity_Mapper_UnitOfWork::STATE_CLEAN);
    }

    /**
     * Mark the entity as dirty.
     *
     * @param Zend_Entity_Interface $entity
     * @return void
     */
    public function registerDirty(Zend_Entity_Interface $entity)
    {
        $this->_register($entity, Zend_Entity_Mapper_UnitOfWork::STATE_DIRTY);
    }

    /**
     * Schedule entity for removal.
     *
     * @param Zend_Entity_Interface $entity
     */
    public function registerDeleted(Zend_Entity_Interface $entity)
    {
        $this->_register($entity, Zend_Entity_Mapper_UnitOfWork::STATE_DELETED);
    }

    /**
     * Is the UnitOfWork managing a transaction?
     *
     * @return boolean
     */
    public function isManagingCurrentTransaction()
    {
        return $this->_currentlyEnabled;
    }

    /**
     * Request Beginning of an Transaction
     *
     * @throws Zend_Entity_Exception
     * @return boolean
     */
    public function beginTransaction()
    {
        if($this->isReadOnly()) {
            throw new Zend_Entity_Exception("UnitOfWork is currently in read only mode and cannot start a transaction.");
        }

        $this->_currentlyEnabled = true;
        return $this->_db->beginTransaction();
    }

    /**
     * Process all entities
     *
     * @return void
     */
    protected function processWork()
    {
        foreach($this->_entities AS $entity) {
            switch($entity['state']) {
                case Zend_Entity_Mapper_UnitOfWork::STATE_DIRTY:
                case Zend_Entity_Mapper_UnitOfWork::STATE_NEW:
                    $mapper = $this->_entityManager->getMapperByEntity($entity['object']);
                    $mapper->save($entity['object'], $this->_entityManager);
                    break;
                case Zend_Entity_Mapper_UnitOfWork::STATE_DELETED:
                    $mapper = $this->_entityManager->getMapperByEntity($entity['object']);
                    $mapper->delete($entity['object'], $this->_entityManager);
                    break;
            }
            $this->registerClean($entity['object']);
        }
        $this->_entities = array();
    }

    /**
     * Commit the current transaction
     *
     * @throws Zend_Entity_Exception When entity manager is in read only mode.
     * @throws Exception When commit fails due to exception.
     * @return void
     */
    public function commit()
    {
        if($this->isReadOnly()) {
            throw new Zend_Entity_Exception("UnitOfWork is currently in read only mode and cannot commit a transaction.");
        }

        if($this->isManagingCurrentTransaction() == true) {
            $this->processWork();
            $this->_db->commit();
            $this->_currentlyEnabled = false;
        } else {
            throw new Zend_Entity_Exception("Cannot commit transaction that is not open!");
        }
    }

    /**
     * Rollback the current transaction if the UnitOfWork is managing it.
     *
     * @throws Zend_Entity_Exception
     * @return void
     */
    public function rollBack()
    {
        if($this->isReadOnly()) {
            throw new Zend_Entity_Exception("UnitOfWork is currently in read only mode and cannot rollback a transaction.");
        }

        if($this->isManagingCurrentTransaction() == true) {
            $this->_db->rollBack();
            $this->_currentlyEnabled = false;
        }
    }

    /**
     * Set the Unit Of Work to read only mode.
     *
     * @return void
     */
    public function setReadOnly()
    {
        $this->_readOnly = true;
    }

    /**
     * Return if the unit of work is in read only mode and does not need to save entities.
     * 
     * @return boolean
     */
    public function isReadOnly()
    {
        return $this->_readOnly;
    }

    /**
     * Clear the unit of work. A rollback is requested and entities are emptied.
     *
     * @return void
     */
    public function clear()
    {
        $this->rollBack();
        $this->_entities = array();
    }
}