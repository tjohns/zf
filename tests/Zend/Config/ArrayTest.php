<?php

/**
 * @category   Zend
 * @package    Zend_Config
 * @subpackage UnitTests
 */


/**
 * Zend_Config_Array
 */
require_once 'Zend/Config/Array.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @category   Zend
 * @package    Zend_Config
 * @subpackage UnitTests
 */
class Zend_Config_ArrayTest extends PHPUnit_Framework_TestCase
{
    protected $_configFile;

    public function setUp()
    {
        $this->_configFile = dirname(__FILE__) . '/_files/config.php';
    }

    public function testLoadSingleSection()
    {
        $config = new Zend_Config_Array($this->_configFile, 'all');

        $this->assertEquals('all', $config->hostname);
        $this->assertEquals('live', $config->db->name);
        $this->assertEquals('multi', $config->one->two->three);
        $this->assertNull(@$config->nonexistent); // property doesn't exist
    }

    public function testSectionInclude()
    {
        $config = new Zend_Config_Array($this->_configFile, 'staging');
        $this->assertEquals(false, $config->debug); // only in staging
        $this->assertEquals('thisname', $config->name); // only in all
        $this->assertEquals('username', $config->db->user); // only in all (nested version)
        $this->assertEquals('staging', $config->hostname); // inherited and overridden
        $this->assertEquals('dbstaging', $config->db->name); // inherited and overridden
    }

    public function testMultiDepthExtends()
    {
        $config = new Zend_Config_Array($this->_configFile, 'other_staging');

        $this->assertEquals('otherStaging', $config->only_in); // only in other_staging
        $this->assertEquals(false, $config->debug); // 1 level down: only in staging
        $this->assertEquals('thisname', $config->name); //  2 levels down: only in all
        $this->assertEquals('username', $config->db->user); // 2 levels down: only in all (nested version)
        $this->assertEquals('staging', $config->hostname); // inherited from two to one and overridden
        $this->assertEquals('dbstaging', $config->db->name); // inherited from two to one and overridden
        $this->assertEquals('anotherpwd', $config->db->pass); // inherited from two to other_staging and overridden
    }

    public function testErrorNoInitialSection()
    {
        try {
            $config = @new Zend_Config_Array($this->_configFile);
            $this->fail('An expected Zend_Config_Exception has not been raised');
        } catch (Zend_Config_Exception $expected) {
            $this->assertContains('Section is not set', $expected->getMessage());
        }
    }

    public function testErrorInvalidSection()
    {
        try {
            $config = new Zend_Config_Array($this->_configFile, 'extendserror');
            $this->fail('An expected Zend_Config_Exception has not been raised');
        } catch (Zend_Config_Exception $expected) {
            $this->assertContains('is not an array', $expected->getMessage());
        }
    }
}
