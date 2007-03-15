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
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/** PHPUnit_Framework_TestCase */
require_once 'PHPUnit/Framework/TestCase.php';

/** Zend_Log_Writer_Mock */
require_once 'Zend/Log/Writer/Db.php';

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */
class Zend_Log_Writer_DbTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->tableName = 'db-table-name';
        
        $this->db     = new Zend_Log_Writer_DbTest_MockDbAdapter();
        $this->writer = new Zend_Log_Writer_Db($this->db, $this->tableName);
    }
    
    public function testWriteWithDefaults()
    {
        // log to the mock db adapter
        $message  = 'message-to-log';
        $priority = 2;
        $this->writer->write($message, $priority);

        // insert should be called once...
        $this->assertContains('insert', array_keys($this->db->calls));
        $this->assertEquals(1, count($this->db->calls['insert']));

        // ...with the correct table and binds for the database
        $binds = array('message'  => $message, 
                       'priority' => $priority);
        $this->assertEquals(array($this->tableName, $binds), 
                            $this->db->calls['insert'][0]);
    }

    public function testWriteUsesOptionalCustomColumnNames()
    {
        $this->writer->setOption('fieldMessage',  $messageField = 'new-message-field');
        $this->writer->setOption('fieldPriority', $priorityField   = 'new-priority-field');

        // log to the mock db adapter
        $message  = 'message-to-log';
        $priority = 2;
        $this->writer->write($message, $priority);
        
        // insert should be called once...
        $this->assertContains('insert', array_keys($this->db->calls));
        $this->assertEquals(1, count($this->db->calls['insert']));

        // ...with the correct table and binds for the database
        $binds = array($messageField  => $message, 
                       $priorityField => $priority);
        $this->assertEquals(array($this->tableName, $binds), 
                            $this->db->calls['insert'][0]);
    }

}


class Zend_Log_Writer_DbTest_MockDbAdapter
{
    public $calls = array();

    public function __call($method, $params) 
    { 
        $this->calls[$method][] = $params; 
    }

}