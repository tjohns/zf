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
 * @package    Zend_Build
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once 'Zend/Build/TestBase.php';

/**
 * Include manifest file
 */
require_once 'Zend/Build/Manifest.php';

class Zend_Build_ManifestTest extends Zend_Build_TestBase
{
    const ACTION_TYPE        = 'action';
    const RESOURCE_TYPE      = 'resource';
    const TASK_TYPE          = 'task';
    
    protected $_manifest;
    
    /**
     * Set up test configuration
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    public function setup()
    {
    	$this->_cdToTestProject();
    	$this->_manifest = Zend_Build_Manifest::getInstance();
    }
    
    public function teardown()
    {
        unset($_manifest);
    }

    public function testGetInternalActionContext()
    {
        $this->_testContext('internal-test', 'it');
    }
    
    public function testGetInternalResourceContext()
    {
        
    }
    
    public function testGetExternalActionContext()
    {
        
    }
    
    public function testGetExternalResourceContext()
    {
        
    }
    
    private function _testContext($contextName, $contextAlias)
    {
        $_manifestArray = $this->_manifest->toArray();
        $this->assertArrayHasKey(self::ACTION_TYPE, $_manifestArray);
        $contextName = 'internal-test';
        $contextAlias = 'it';
        $this->assertArrayHasKey($contextName, $_manifestArray[self::ACTION_TYPE]);
        $this->assertArrayHasKey($contextAlias, $_manifestArray[self::ACTION_TYPE]);
        $this->assertSame($_manifestArray[self::ACTION_TYPE][$contextName], $_manifestArray[self::ACTION_TYPE][$contextAlias]);
    }
}