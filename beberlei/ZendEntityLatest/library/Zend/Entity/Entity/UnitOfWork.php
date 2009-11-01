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
 * @package    Zend_Entity
 * @subpackage Manager
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * The UnitOfWork handles the change policies of each entity and upon flush stores data to persistence
 *
 * @category   Zend
 * @package    Zend_Entity
 * @subpackage Manager
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Entity_UnitOfWork implements Countable
{
    const STATE_NEW = 1;
    const STATE_MANAGED = 2;
    const STATE_DIRTY = 3;
    const STATE_DELETED = 4;
    const STATE_DETACHED = 5;

    /**
     * @var Zend_Entity_Manager_Interface
     */
    protected $_entityManager = null;

    /**
     * @var Zend_Entity_MetadataFactory_FactoryAbstract
     */
    protected $_metadata = null;

    /**
     * @var array
     */
    protected $_changePolicies = array();

    /**
     * @var array
     */
    protected $_entityStates = array();

    /**
     * @var array
     */
    protected $_enqueuedForInsert = array();

    /**
     * @var array
     */
    protected $_enqueuedForUpdate = array();

    /**
     * @var array
     */
    protected $_enqueuedForDelete = array();

    /**
     * @var array
     */
    protected $_enqueuedCollections = array();

    /**
     * @var array
     */
    protected $_enqueuedArrays = array();

    /**
     * @param Zend_Entity_Manager_Interface $em
     */
    public function setEntityManager(Zend_Entity_Manager_Interface $em)
    {
        $this->_entityManager = $em;
        $this->_metadata = $em->getMetadataFactory();
    }

    /**
     * @param  object $entity
     * @return int
     */
    public function getState($entity)
    {
        $h = spl_object_hash($entity);
        if(!isset($this->_entityStates[$h])) {
            return self::STATE_DETACHED;
        } else {
            return $this->_entityStates[$h];
        }
    }

    /**
     * @param object $entity
     */
    public function markManaged($entity)
    {
        $h = spl_object_hash($entity);
        $this->_entityStates[$h] = self::STATE_MANAGED;
    }

    /**
     * @param object $entity
     */
    public function markNew($entity)
    {
        $h = spl_object_hash($entity);
        $this->_entityStates[$h] = self::STATE_NEW;
        $this->_enqueuedForInsert[] = $entity;
    }

    /**
     * @param object $entity
     */
    public function markDirty($entity)
    {
        $h = spl_object_hash($entity);
        $this->_entityStates[$h] = self::STATE_DIRTY;
        $this->_enqueuedForUpdate[] = $entity;
    }

    /**
     * @param object $entity
     */
    public function markDeleted($entity)
    {
        $h = spl_object_hash($entity);
        $this->_entityStates[$h] = self::STATE_DELETED;
        $this->_enqueuedForDelete[] = $entity;
    }

    /**
     * Flush the complete unit of work to the database.
     *
     * If an exception occurs during this phase the state and diff of database and application is unknown
     * and the identity map and unit of work backlogs are cleared and the transaction is rolled back.
     * You are advised to close the entity manager and restart the process from the beginning if this would occur.
     *
     * @throws Exception
     * @return void
     */
    public function flush()
    {
        if($this->count() == 0) {
            return;
        }

        $this->_findCascades();
        $this->_findArrays();

        try {
            $transaction = $this->_entityManager->beginTransaction();

            $mapper = $this->_entityManager->getMapper();
            foreach($this->_enqueuedForInsert AS $insertEntity) {
                $mapper->save($insertEntity, $this->_entityManager);
                $this->markManaged($insertEntity);
            }
            
            foreach($this->_enqueuedForUpdate AS $updateEntity) {
                $mapper->save($updateEntity, $this->_entityManager);
                $this->markManaged($updateEntity);
            }

            foreach($this->_enqueuedForDelete AS $deleteEntity) {
                $mapper->delete($deleteEntity, $this->_entityManager);
                $h = spl_object_hash($deleteEntity);
                unset($this->_entityStates[$h]);
            }

            foreach($this->_enqueuedCollections AS $collection) {
                
            }

            foreach($this->_enqueuedArrays AS $array) {
                
            }

            $transaction->commit();
            $this->clear();
        } catch(Exception $e) {
            $this->_entityManager->getIdentityMap()->clear();
            $this->clear();
            $transaction->rollback();

            throw $e;
        }
    }

    protected function _findCascades()
    {
    }

    protected function _findArrays()
    {
        
    }

    public function clear()
    {
        $this->_enqueuedForDelete = array();
        $this->_enqueuedForInsert = array();
        $this->_enqueuedForUpdate = array();
    }

    public function count()
    {
        return (
            count($this->_enqueuedForDelete) +
            count($this->_enqueuedForInsert) +
            count($this->_enqueuedForUpdate)
        );
    }
}