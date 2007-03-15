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
 * @package    Zend_Log
 * @subpackage Writer
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/** Zend_Log_Writer_Abstract */
require_once 'Zend/Log/Writer/Abstract.php';

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage Writer
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */ 
class Zend_Log_Writer_Db extends Zend_Log_Writer_Abstract
{
    /**
     * Database adapter instance
     * @var Zend_Db_Adapter
     */
    private $_db;

    /**
     * Name of the log table in the database
     * @var string
     */
    private $_table;

    /**
     * Options to be set by setOption().  Sets the field names in the database table.
     *
     * @var array
     */
    protected $_options = array('fieldMessage'  => 'message',
                                'fieldPriority' => 'priority');

    /**
     * Class constructor
     *
     * @param Zend_Db_Adapter $db   Database adapter instance
     * @param string $table         Log table in database
     */
    public function __construct($db, $table)
    {
        $this->_db    = $db;
        $this->_table = $table;
    }

    /**
     * Write a message to the log.
     *
     * @param  $message    Message to log
     * @param  $priority   Priority of message
     * @return bool        Always True
     */
    public function write($message, $priority)
    {
        $fields = array($this->_options['fieldMessage'] => $message,
                        $this->_options['fieldPriority']   => $priority);
        
        $this->_db->insert($this->_table, $fields);
        return true;
    }

}