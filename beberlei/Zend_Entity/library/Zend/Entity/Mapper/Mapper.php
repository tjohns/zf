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

class Zend_Entity_Mapper_Mapper extends Zend_Entity_MapperAbstract
{
    /**
     * Factory method to create the database mapper.
     * 
     * @param array $options
     * @return Zend_Entity_Mapper_Mapper
     */
    static public function create(array $options)
    {
        if(!isset($options['db']) || (!($options['db'] instanceof Zend_Db_Adapter_Abstract))) {
            throw new Zend_Entity_Exception("Missing Database Adapter while creating Mapper.");
        }

        if(!isset($options['metadataFactory']) ||
            (!($options['metadataFactory'] instanceof Zend_Entity_MetadataFactory_Interface))) {
            throw new Zend_Entity_Exception("Missing Metadata Factory while creating Mapper.");
        }

        $db = $options['db'];
        $metadataFactory = $options['metadataFactory'];
        $mappingInstructions = $metadataFactory->transform('Zend_Entity_Mapper_MappingInstruction');

        return new self($db, $metadataFactory, $mappingInstructions);
    }

    /**
     * Construct DataMapper
     *
     * @param  Zend_Db_Adapter_Abstract  $db
     * @param  Zend_Entity_MetadataFactory_Interface $metadataFactory
     * @param  Zend_Entity_Mapper_MappingInstruction[] $mappingInstructions
     */
    public function __construct(Zend_Db_Adapter_Abstract $db, Zend_Entity_MetadataFactory_Interface $metadataFactory, array $mappingInstructions=array())
    {
        $this->_db = $db;
        $this->_metadataFactory = $metadataFactory;
        $this->_mappingInstructions = $mappingInstructions;
    }

    public function createNativeQuery($input, $entityManager)
    {
        $q = new Zend_Entity_Mapper_NativeQuery($this, $entityManager);
        if(in_array($input, $this->_metadataFactory->getDefinitionEntityNames())) {
            $q->with($input);
        } else {
            throw new Exception("Missing Native Query Parser/Builder/Whatever!");
        }
        return $q;
    }

    public function createQuery($entityName, $entityManager)
    {
        throw new Exception("not implemented yet");
    }

    /**
     * @return Zend_Entity_Transaction
     */
    public function getTransaction()
    {
        return new Zend_Entity_Mapper_Transaction($this->_db);
    }

    /**
     * Close the current connection context.
     *
     * @return void
     */
    public function closeConnection()
    {
        $this->_db->closeConnection();
    }
}