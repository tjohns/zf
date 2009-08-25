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
 * @package    Db
 * @subpackage Mapper
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Database QueryObject an extension of Zend_Db_Select
 *
 * @uses       Zend_Db_Select
 * @category   Zend
 * @package    Db
 * @subpackage Mapper
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Entity_Mapper_QueryObject extends Zend_Db_Select
{
    /**
     * Prevents Wildcards to cluster the loaded columns, because Mapper enforces required columns anyways.
     *
     * @param string $type
     * @param string $name
     * @param string $cond
     * @param string|array $cols
     * @param string $schema
     */
    protected function _join($type, $name, $cond, $cols, $schema = null)
    {
        if($cols == Zend_Db_Select::SQL_WILDCARD) {
            $cols = array();
        }
        return parent::_join($type, $name, $cond, $cols, $schema);
    }
}
