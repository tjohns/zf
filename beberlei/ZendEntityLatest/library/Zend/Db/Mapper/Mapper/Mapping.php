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
 * @package    Zend_Db
 * @subpackage Mapper
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Contains Mapping Instructions for Database Storage Loader and Persister
 *
 * @uses       Zend_Entity_MappingAbstract
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Mapper
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_Mapper_Mapping extends Zend_Entity_MappingAbstract
{
    /**
     * @var string
     */
    public $table;

    /**
     * @var string
     */
    public $schema;

    /**
     * Accept Entity Definition for Database Schema and Table definitions.
     *
     * @param Zend_Entity_Definition_Entity $entity
     */
    protected function _doAcceptEntity($entity)
    {
        $this->table = $entity->table;
        if(!is_string($this->table) || strlen($this->table) == 0) {
            throw new Zend_Entity_Exception("Invalid table name given for entity '".$this->class."'.");
        }
        $this->schema = $entity->schema;
    }


    /**
     * Accept the primary key and configure database specific information.
     *
     * @param Zend_Entity_Definition_PrimaryKey $primaryKey
     * @param Zend_Entity_MetadataFactory_FactoryAbstract
     * @return void
     */
    protected function _doAcceptPrimaryKey($primaryKey, $metadataFactory)
    {
        if($primaryKey->getGenerator() === null) {
            $idGenClass = $metadataFactory->getDefaultIdGeneratorClass();

            if($idGenClass == null) {
                $idGenClass = "Zend_Db_Mapper_Id_AutoIncrement";
            }

            $gen = new $idGenClass();
            if($gen instanceof Zend_Db_Mapper_Id_AutoIncrement) {
                $gen->setTableName($this->table);
                $gen->setPrimaryKey($primaryKey->columnName);
            } else if($gen instanceof Zend_Db_Mapper_Id_Sequence) {
                $sequenceName = $this->table."_".$primaryKey->columnName."_seq";
                $gen->setSequenceName($sequenceName);
            }
            $primaryKey->setGenerator($gen);
        }
    }
}