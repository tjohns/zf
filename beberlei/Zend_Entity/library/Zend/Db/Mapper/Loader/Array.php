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
 * Array Loader
 *
 * @uses       Zend_Db_Mapper_Loader_LoaderAbstract
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Mapper
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_Mapper_Loader_Array extends Zend_Db_Mapper_Loader_LoaderAbstract
{
    /**
     * @param  array $resultSet
     * @param  string $fetchMode
     * @return Zend_Entity_Collection_Interface
     */
    public function processResultset($resultSet, Zend_Entity_Query_ResultSetMapping $rsm)
    {
        $resultArray = array();
        foreach($resultSet AS $row) {
            foreach($rsm->entityResult AS $entityName => $entityDef) {
                $mapping = $this->_mappings[$entityName];
                $resultArray[] = $this->renameAndCastColumnToPropertyKeys($row, $mapping);
            }
        }
        return $resultArray;
    }
}
