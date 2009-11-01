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
 * Database Persister of persistent ArrayObjects.
 *
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Mapper
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_Mapper_Persister_Array
{
    /**
     * @var Zend_Entity_Definition_Array
     */
    protected $_arrayDef = null;

    /**
     * @param Zend_Entity_Definition_Array $arrayDef
     */
    public function __construct(Zend_Entity_Definition_Array $arrayDef)
    {
        $this->_arrayDef = $arrayDef;
    }

    /**
     * @param int $ownerId
     * @param Zend_Entity_Collection_Array $arrayObject
     * @param Zend_Entity_Manager_Interface $entityManager
     */
    public function persist($ownerId, $arrayObject, $entityManager)
    {
        $arrayDef = $this->_arrayDef;
        $db = $entityManager->getMapper()->getAdapter();

        foreach($arrayObject->__ze_getRemoved() AS $k => $v) {
            $db->delete(
                $arrayDef->table,
                implode(" AND ", array(
                    $db->quoteInto($arrayDef->key." = ?", $ownerId),
                    $db->quoteInto($arrayDef->mapKey." = ?", $k)
                ))
            );
        }

        foreach($arrayObject->__ze_getAdded() AS $k => $v) {
            $db->insert(
                $arrayDef->table,
                array(
                    $arrayDef->key => $ownerId,
                    $arrayDef->mapKey=> $k,
                    $arrayDef->element => $v,
                )
            );
        }
    }
}
