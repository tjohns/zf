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
 * Increment ID by performing a MAX(*)+1 on the the entities primary key column.
 *
 * Important: This generator only works for single-threaded applications, since it cannot
 * guarantee that an id is only assigned once. However it has value in testing enviroments
 * for example as simulation for a sequence generator on Oracle databases which require
 * specific ids in tests for example in conjunction with {@see Zend_Db_Test_PHPUnit_DatabaseTestCase}.
 *
 * @uses       Zend_Entity_Definition_Id_Interface
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Mapper
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_Mapper_Id_Increment implements Zend_Entity_Definition_Id_Interface
{
    /**
     * @return bool
     */
    public function isPrePersistGenerator()
    {
        return true;
    }

    /**
     * Generate a Id for the given entity.
     *
     * @param  Zend_Entity_Manager_Interface $manager
     * @param  object $entity
     * @return mixed
     */
    public function generate(Zend_Entity_Manager_Interface $manager, $entity)
    {
        $db = $manager->getMapper()->getAdapter();
        $entityName = $manager->getMetadataFactory()->getEntityName($entity);
        $mapping = $manager->getMetadataFactory()->getDefinitionByEntityName($entityName);

        $idColumn = $mapping->primaryKey->columnName;
        $table = $mapping->table;

        $query = "SELECT MAX(".$db->quoteIdentifier($idColumn).")+1 AS nextVal FROM ".$db->quoteIdentifier($table);
        return $db->fetchOne($query);
    }
}