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
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: TestSetup.php 15668 2009-05-21 22:04:13Z ralph $
 */


/**
 * @see Zend_Db_TestCase
 */
require_once 'Zend/Db/TestCase.php';


PHPUnit_Util_Filter::addFileToFilter(__FILE__);


/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Db_Table_TestSetup extends Zend_Db_TestCase
{

    /**
     * @var array of Zend_Db_Table_Abstract
     */
    protected $_table = array();

    protected $_runtimeIncludePath = null;
    
    public function setUp()
    {
        parent::setUp();

        $this->_table['accounts']      = $this->sharedFixture->tableUtility->getTable('My_ZendDbTable_TableAccounts');
        $this->sharedFixture->tableUtility->getTableById('Bugs')          = $this->sharedFixture->tableUtility->getTable('My_ZendDbTable_TableBugs');
        $this->sharedFixture->tableUtility->getTableById('BugsProducts') = $this->sharedFixture->tableUtility->getTable('My_ZendDbTable_TableBugsProducts');
        $this->sharedFixture->tableUtility->getTableById('Products')      = $this->sharedFixture->tableUtility->getTable('My_ZendDbTable_TableProducts');
    }

    public function tearDown()
    {
        if ($this->_runtimeIncludePath) {
            $this->_restoreIncludePath();
        }
    }
    
    protected function _getTable($tableClass, $options = array())
    {
        if (is_array($options) && !isset($options['db'])) {
            $options['db'] = $this->sharedFixture->dbAdapter;
        }
        if (!class_exists($tableClass)) {
            $this->_useMyIncludePath();
            Zend_Loader::loadClass($tableClass);
            $this->_restoreIncludePath();
        }
        $table = new $tableClass($options);
        return $table;
    }
    
    protected function _useMyIncludePath()
    {
        $this->_runtimeIncludePath = get_include_path();
        set_include_path(dirname(__FILE__) . '/_files/' . PATH_SEPARATOR . $this->_runtimeIncludePath);
    }
    
    protected function _restoreIncludePath()
    {
        set_include_path($this->_runtimeIncludePath);
        $this->_runtimeIncludePath = null;
    }

}
