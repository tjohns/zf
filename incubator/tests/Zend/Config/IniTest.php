<?php

/**
 * @category   Zend
 * @package    Zend_Config
 * @subpackage UnitTests
 */


/**
 * Zend_Config_Ini
 */
require_once 'Zend/Config/Ini.php';

/**
 * PHPUnit2 test case
 */
require_once 'PHPUnit2/Framework/TestCase.php';


/**
 * @category   Zend
 * @package    Zend_Config
 * @subpackage UnitTests
 */
class Zend_Config_IniTest extends PHPUnit2_Framework_TestCase
{
    protected $_iniFileConfig;
    protected $_iniFileNested;

    public function setUp()
    {
        $this->_iniFileConfig = dirname(__FILE__) . '/_files/config.ini';
    }

    public function testLoadSingleSection()
    {
        $array = Zend_Config_Ini::load($this->_iniFileConfig, 'all');

        $this->assertEquals('all', $array['hostname']);
        $this->assertEquals('live', $array['db']['name']);
        $this->assertEquals('multi', $array['one']['two']['three']);
        $this->assertNull(@$array['nonexistent']); // property doesn't exist
    }

    public function testSectionInclude()
    {
        $array = Zend_Config_Ini::load($this->_iniFileConfig, 'staging');

        $this->assertEquals('', $array['debug']); // only in staging
        $this->assertEquals('thisname', $array['name']); // only in all
        $this->assertEquals('username', $array['db']['user']); // only in all (nested version)
        $this->assertEquals('staging', $array['hostname']); // inherited
        $this->assertEquals('dbstaging', $array['db']['name']); //inherited
    }

    public function testMultiDepthExtends()
    {
        $array = Zend_Config_Ini::load($this->_iniFileConfig, 'other_staging');

        $this->assertEquals('otherStaging', $array['only_in']); // only in other_staging
        $this->assertEquals('', $array['debug']); // 1 level down: only in staging
        $this->assertEquals('thisname', $array['name']); //  2 levels down: only in all
        $this->assertEquals('username', $array['db']['user']); // 2 levels down: only in all (nested version)
        $this->assertEquals('staging', $array['hostname']); // inherited from two to one
        $this->assertEquals('dbstaging', $array['db']['name']); //inherited from two to one
        $this->assertEquals('anotherpwd', $array['db']['pass']); //inherited from two to other_staging
    }

    public function testErrorNoInitialSection()
    {
        try {
            $array = @Zend_Config_Ini::load($this->_iniFileConfig);
        } catch (Zend_Config_Exception $expected) {
            $this->assertContains('Section is not set', $expected->getMessage());
            return;
        }

        $this->fail('An expected Zend_Config_Exception has not been raised');
    }

    public function testErrorNoExtendsSection()
    {
        try {
            $array = Zend_Config_Ini::load($this->_iniFileConfig, 'extendserror');
        } catch (Zend_Config_Exception $expected) {
            $this->assertContains('cannot be found', $expected->getMessage());
            return;
        }

        $this->fail('An expected Zend_Config_Exception has not been raised');
    }

}
