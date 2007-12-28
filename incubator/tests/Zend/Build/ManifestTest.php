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
    const ACTION_TYPE               = 'action';
    const RESOURCE_TYPE             = 'resource';
    const TASK_TYPE                 = 'task';
    
    const INTERNAL_ACTION_MF        = 'lib/Zend/Build/Action/TestIntAction-ZFManifest.xml';
    const INTERNAL_RESOURCE_MF      = 'lib/Zend/Build/Resource/TestIntResource-ZFManifest.xml';
    const EXTERNAL_ACTION_MF        = 'lib/Extra/Build/Action/TestExtAction-ZFManifest.xml';
    const EXTERNAL_RESOURCE_MF      = 'lib/Extra/Build/Resource/TestExtResource-ZFManifest.xml';
    
    private $_manifest = null;
    
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
        unset($this->_manifest);
    }
    
    public function testActionResourceSeparateNamespaces()
    {
        print(getcwd());
        $this->_manifest->init(array(self::INTERNAL_ACTION_MF, self::INTERNAL_RESOURCE_MF));
        $internalActionContext = $this->_manifest->getContext(self::ACTION_TYPE, 'internal-test');
        $internalActionContextAlias = $this->_manifest->getContext(self::ACTION_TYPE, 'it');
        $internalResourceContext = $this->_manifest->getContext(self::RESOURCE_TYPE, 'internal-test');
        $internalResourceContextAlias = $this->_manifest->getContext(self::RESOURCE_TYPE, 'it');
        $this->assertNotNull($internalActionContext);
        $this->assertNotNull($internalActionContextAlias);
        $this->assertNotNull($internalResourceContext);
        $this->assertNotNull($internalResourceContextAlias);
        $this->assertSame($internalActionContext, $internalActionContextAlias);
        $this->assertSame($internalResourceContext, $internalResourceContextAlias);
        $this->assertNotSame($internalActionContext, $internalResourceContext);
        $this->assertNotSame($internalActionContextAlias, $internalResourceContextAlias);
        $this->assertNotSame($internalActionContext, $internalResourceContextAlias);
        $this->assertNotSame($internalActionContextAlias, $internalResourceContext);
    }

    public function testGetInternalActionContext()
    {
        $this->_testContext(self::ACTION_TYPE, 'internal-test', 'it');
    }
    
    public function testGetInternalResourceContext()
    {
        $this->_testContext(self::RESOURCE_TYPE, 'internal-test', 'it');
    }
    
    public function testGetExternalActionContext()
    {
        $this->_testContext(self::ACTION_TYPE, 'external-test', 'et');
    }
    
    public function testGetExternalResourceContext()
    {
        $this->_testContext(self::RESOURCE_TYPE, 'external-test', 'et');
    }
    
    private function _testContext($contextType, $contextName, $contextAlias)
    {
        // Look through all dirs in the include path
        $this->_manifest->init();
        $this->assertNotNull($this->_manifest->getContext($contextType, $contextName));
        $this->assertNotNull($this->_manifest->getContext($contextType, $contextAlias));
        $this->assertSame($this->_manifest->getContext($contextType, $contextName),
                          $this->_manifest->getContext($contextType, $contextAlias));
    }
}