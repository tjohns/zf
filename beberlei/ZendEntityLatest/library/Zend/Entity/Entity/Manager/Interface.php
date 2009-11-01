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
 * An Entity Manager handles the lifecycle of entity objects.
 *
 * @category   Zend
 * @package    Zend_Entity
 * @subpackage Manager
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface Zend_Entity_Manager_Interface
{
    /**
     * @param string $entityName
     * @param Zend_Entity_Query_ResultSetMapping
     * @return Zend_Entity_Query_QueryAbstract
     */
    public function createNativeQuery($nativeInput, $resultSetMapping=null);

    /**
     * @param string $queryName
     * @return Zend_Entity_Query_QueryAbstract
     */
    public function createNamedQuery($queryName);

    /**
     * Find by primary key
     *
     * @param string $entityName
     * @param string $key
     * @param string $notFound
     * @return object
     */
    public function load($entityName, $key, $notFound="null");

    /**
     * Save entity by registering it with UnitOfWork or hitting the database mapper.
     *
     * @param  object $entity
     * @return void
     */
    public function persist($entity);

    /**
     * Try to delete entity by checking with UnitOfWork or directly going to mapper.
     *
     * @param  object $entity
     * @return void
     */
    public function remove($entity);

    /**
     * Merge the state of a detached entity which has an identity back into the persistence context.
     * 
     * @param object $entity
     * @return void
     */
    public function merge($entity);

    /**
     * Refresh the state of an entity.
     * 
     * @param object $entity
     * @return void
     */
    public function refresh($entity);

    /**
     * Get a reference of an object.
     *
     * A reference is either a LazyLoad entity of the type {@see Zend_Entity_LazyLoad_Entity}
     * or if the entity was loaded before and is found in the identity map the original is used.
     *
     * @param string $class
     * @param int|string $id
     */
    public function getReference($class, $id);

    /**
     * Detach entity from persistence context, so that it will become unmanaged. Any unflushed changes will be lost.
     *
     * @param object $entity
     */
    public function detach($entity);

    /**
     * Check if entity instance belongs to the persistence context.
     *
     * @param  object $entity
     * @return boolean
     */
    public function contains($entity);

    /**
     * Begin new transaction and return the Zend_Entity_Transaction instance
     *
     * @retun Zend_Entity_Transaction
     */
    public function beginTransaction();

    /**
     * Get the entity transcation
     *
     * @return Zend_Entity_Transaction
     */
    public function getTransaction();

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
     * @return Zend_Entity_MetadataFactory_FactoryAbstract
     */
    public function getMetadataFactory();

    /**
     * @return Zend_Entity_MapperAbstract
     */
    public function getMapper();
}