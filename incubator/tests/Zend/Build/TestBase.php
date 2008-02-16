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
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * PHPUnit_Framework_TestCase
 */
require_once 'PHPUnit/Framework/TestCase.php';

abstract class Zend_Build_TestBase extends PHPUnit_Framework_TestCase
{
    /**
     * Path to test files
     *
     * @var string
     */
    protected $_filesPath;
    
    /**
     * Path to the root dir of the test project
     * 
     * @var string
     */ 
    protected $_test_project_root;

    /**
     * Set up test configuration
     *
     * @return void
     */
    public function __construct()
    {
        $this->_filesPath = dirname(__FILE__) . '/_files';
        $this->_test_project_root = $this->_filesPath . DIRECTORY_SEPARATOR . "TestProject" . DIRECTORY_SEPARATOR;
    }
    
    protected function _cdToTestProject()
    {
        chdir($this->_test_project_root);
    }
    
    protected function _getGoldenOutput($filename)
    {
        return file_get_contents($this->_filesPath . DIRECTORY_SEPARATOR . $filename);
    }
}