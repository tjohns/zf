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
 * @subpackage Definition
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Database supported Pre-Persist Id Generation for Mysql, Mssql and Oracle
 *
 * @uses       Zend_Entity_Definition_Id_Interface
 * @category   Zend
 * @package    Zend_Entity
 * @subpackage Definition
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_Mapper_Id_GUID implements Zend_Entity_Definition_Id_Interface
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
        
        if($db instanceof Zend_Db_Adapter_Pdo_Mysql || $db instanceof Zend_Db_Adapter_Mysqli) {
            $stmt = $db->query("SELECT UUID()");
        } elseif($db instanceof Zend_Db_Adapter_Sqlsrv || $db instanceof Zend_Db_Adapter_Pdo_Mssql) {
            $stmt = $db->query("SELECT newid()");
        } elseif($db instanceof Zend_Db_Adapter_Oracle || $db instanceof Zend_Db_Adapter_Pdo_Oci) {
            $stmt = $db->query("SELECT sys_guid() FROM dual");
        } else {
            throw new Zend_Entity_Exception("Invalid Database adapter for GUID Generator.");
        }

        /* @var $stmt Zend_Db_Statement_Interface */
        return $stmt->fetchColumn();
    }
}