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
 * @subpackage Mapper
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */



/**
 * Abstract Storage Engine Mapper which allows access to CRUD behaviour for the underlying entities.
 *
 * @category   Zend
 * @package    Zend_Entity
 * @subpackage Mapper
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Entity_MapperAbstract
{
    /**
     * @var Zend_Entity_Definition_MappingVisitor[]
     */
    protected $_mappings = array();

    /**
     * @var array
     */
    protected $_classAliases = array();

    /**
     * @var Zend_Entity_StateTransformer_TypeConverter
     */
    protected $_typeConverter = null;

    /**
     *
     * @param  Zend_Entity_Manager_Interface $entityManager
     * @param  string $entityName
     * @param  mixed $keyValue
     * @param  string $notFound
     * @return object
     */
    public function load($entityManager, $entityName, $keyValue, $notFound=Zend_Entity_Manager::NOTFOUND_NULL)
    {
        if(!isset($this->_mappings[$entityName])) {
            throw new Zend_Entity_InvalidEntityException($entityName);
        }

        $query = $this->_doLoad($entityManager, $entityName, $keyValue);
        if($notFound == Zend_Entity_Manager::NOTFOUND_NULL) {
            try {
                return $query->getSingleResult();
            } catch(Zend_Entity_NoResultException $e) {
                return null;
            }
        } else {
            return $query->getSingleResult();
        }
    }

    /**
     * @param  Zend_Entity_Manager_Interface $entityManager
     * @param  string $entityName
     * @param  mixed $keyValue
     * @return object
     */
    abstract protected function _doLoad($entityManager, $entityName, $keyValue);

    /**
     * Refresh the internal state of the given entity.
     * 
     * @param object $entity
     * @param Zend_Entity_Manager_Interface $entityManager
     */
    abstract public function refresh($entity, $entityManager);

    /**
     * @param Zend_Entity_MetadataFactory_FactoryAbstract $metadataFactory
     */
    abstract public function initializeMappings(Zend_Entity_MetadataFactory_FactoryAbstract $metadataFactory);

    /**
     * Save a entity into persistence.
     *
     * @param  object $entity
     * @param  Zend_Entity_Manager_Interface $entityManager
     * @return void
     */
    public function save($entity, Zend_Entity_Manager_Interface $entityManager)
    {
        $entityName = $this->_mappings->getEntityName($entity);
        $this->_doSave($entity, $entityName, $entityManager);
    }

    /**
     * @param string $entity
     * @param string $entityName
     * @param Zend_Entity_Manager_Interface $entityManager
     * @return void
     */
    abstract protected function _doSave($entity, $entityName, $entityManager);

    /**
     * Delete a entity from persistence
     *
     * @param  object $entity
     * @param  object $entityManager
     * @return void
     */
    public function delete($entity, Zend_Entity_Manager_Interface $entityManager)
    {
        $entityName = $this->_mappings->getEntityName($entity);
        $this->_doDelete($entity, $entityName, $entityManager);
    }

    /**
     *
     * @param object $entity
     * @param string $entityName
     * @param Zend_Entity_Manager_Interface $entityManager
     */
    abstract protected function _doDelete($entity, $entityName, $entityManager);

    /**
     * @param  object $entity
     * @return string
     */
    protected function _getEntityName($entity)
    {
        return $this->_mappings->getEntityName($entity);
    }

    /**
     * @param string $input
     * @param Zend_Entity_Manager_Interface $entityManager
     * @return Zend_Entity_Query_QueryAbstract
     */
    abstract public function createNativeQuery($sqlQuery, $resultSetMapping, $entityManager);

    /**
     * @return Zend_Entity_Transaction
     */
    abstract public function getTransaction();

    /**
     * Close the current connection context.
     *
     * @return void
     */
    abstract public function closeConnection();

    /**
     * @return Zend_Entity_StateTransformer_TypeConverter
     */
    public function getTypeConverter()
    {
        if($this->_typeConverter == null) {
            $this->_typeConverter = new Zend_Entity_StateTransformer_TypeConverter();
        }
        return $this->_typeConverter;
    }

    /**
     * @param Zend_Entity_StateTransformer_TypeConverter $typeConverter
     * @return Zend_Entity_MapperAbstract
     */
    public function setTypeConverter(Zend_Entity_StateTransformer_TypeConverter $typeConverter)
    {
        $this->_typeConverter = $typeConverter;
        return $this;
    }

    public function __call($method, $args)
    {
        throw new Zend_Entity_StorageMissmatchException(
            "Method '".$method."' does not exist on this mapper: ".get_class($this)
        );
    }
}