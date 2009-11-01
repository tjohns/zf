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
 * AutoIncrement/Identity Id Generator
 *
 * @uses       Zend_Entity_Definition_Id_Interface
 * @category   Zend
 * @package    Zend_Entity
 * @subpackage Definition
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_Mapper_Id_AutoIncrement implements Zend_Entity_Definition_Id_Interface
{
    /**
     * @var string
     */
    protected $_tableName = null;

    /**
     * @var string
     */
    protected $_primaryKey = null;

    /**
     * @param string $tableName
     * @param string $primaryKey
     */
    public function __construct($tableName=null, $primaryKey=null)
    {
        $this->setTableName($tableName);
        $this->setPrimaryKey($primaryKey);
    }

    /**
     * @param string $tableName
     */
    public function setTableName($tableName)
    {
        $this->_tableName = $tableName;
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->_tableName;
    }

    /**
     * @param string $primaryKey
     */
    public function setPrimaryKey($primaryKey)
    {
        $this->_primaryKey = $primaryKey;
    }

    /**
     * @return string
     */
    public function getPrimaryKey()
    {
        return $this->_primaryKey;
    }

    /**
     * @return bool
     */
    public function isPrePersistGenerator()
    {
        return false;
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
        return $db->lastInsertId($this->_tableName, $this->_primaryKey);
    }
}