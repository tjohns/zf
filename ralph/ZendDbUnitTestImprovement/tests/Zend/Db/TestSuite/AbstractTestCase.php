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
 * @version    $Id: TestSetup.php 12004 2008-10-18 14:29:41Z mikaelkael $
 */

/**
 * no requires needed, this will be required as part of a testcase which is in
 * turn part of a test suite
 */


/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Db_TestSuite_AbstractTestCase extends PHPUnit_Framework_TestCase
{

    protected $_clonedUtilities = array();

    /**
     * Subclasses should call parent::setUp() before
     * doing their own logic, e.g. creating metadata.
     */
    public function setUp()
    {
        $this->_getUtility()
            ->resetState()
            ->loadDefaultTableData();
    }

    /**
     * Subclasses should call parent::tearDown() after
     * doing their own logic, e.g. deleting metadata.
     */
    public function tearDown()
    {
        $this->_getUtility()->deleteTableData();
        while ($clonedUtility = array_pop($this->_clonedUtilities)) {
            $clonedUtility->cleanupResources();
            unset($clonedUtility);
        }
    }
    
    /**
     * _getUtility()
     *
     * @return Zend_Db_TestSuite_DbUtility_AbstractUtility
     */
    protected function _getUtility()
    {
        return $this->sharedFixture->dbUtility;
    }
    
    /**
     * _getClonedUtility()
     *
     * @param bool $withNewAdapter
     * @param array $adapterParams
     * @return Zend_Db_TestSuite_DbUtility_AbstractUtility
     */
    protected function _getClonedUtility($withNewAdapter = true, $adapterParams = array())
    {
        $clonedUtility = clone $this->sharedFixture->dbUtility;
        
        $this->_clonedUtilities[] = $clonedUtility;
        
        if ($withNewAdapter) {
            $params = $clonedUtility->getDriverConfigurationAsParams();
            if ($adapterParams) {
                $params = array_merge($params, $adapterParams);
            }
            $dbAdapter = Zend_Db::factory($clonedUtility->getDriverName(), $params);
            $clonedUtility->setDbAdapter($dbAdapter);
        }
        
        return $clonedUtility;
    }

}
