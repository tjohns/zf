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
        $this->assertEquals('staging', $array['hostname']); // inherited and overridden
        $this->assertEquals('dbstaging', $array['db']['name']); // inherited and overridden
    }

    public function testTrueValues()
    {
        $array = Zend_Config_Ini::load($this->_iniFileConfig, 'debug');

        $this->assertType('string', $array['debug']);
        $this->assertEquals('1', $array['debug']);
        $this->assertType('string', $array['values']['changed']);
        $this->assertEquals('1', $array['values']['changed']);
    }

    public function testEmptyValues()
    {
        $array = Zend_Config_Ini::load($this->_iniFileConfig, 'debug');

        $this->assertType('string', $array['special']['no']);
        $this->assertEquals('', $array['special']['no']);
        $this->assertType('string', $array['special']['null']);
        $this->assertEquals('', $array['special']['null']);
        $this->assertType('string', $array['special']['false']);
        $this->assertEquals('', $array['special']['false']);
    }

    public function testMultiDepthExtends()
    {
        $array = Zend_Config_Ini::load($this->_iniFileConfig, 'other_staging');

        $this->assertEquals('otherStaging', $array['only_in']); // only in other_staging
        $this->assertEquals('', $array['debug']); // 1 level down: only in staging
        $this->assertEquals('thisname', $array['name']); // 2 levels down: only in all
        $this->assertEquals('username', $array['db']['user']); // 2 levels down: only in all (nested version)
        $this->assertEquals('staging', $array['hostname']); // inherited from two to one and overridden
        $this->assertEquals('dbstaging', $array['db']['name']); // inherited from two to one and overridden
        $this->assertEquals('anotherpwd', $array['db']['pass']); // inherited from two to other_staging and overridden
    }

    public function testErrorNoInitialSection()
    {
        try {
            $array = @Zend_Config_Ini::load($this->_iniFileConfig);
            $this->fail('An expected Zend_Config_Exception has not been raised');
        } catch (Zend_Config_Exception $expected) {
            $this->assertContains('Section is not set', $expected->getMessage());
        }
    }

    public function testErrorNoExtendsSection()
    {
        try {
            $array = Zend_Config_Ini::load($this->_iniFileConfig, 'extendserror');
            $this->fail('An expected Zend_Config_Exception has not been raised');
        } catch (Zend_Config_Exception $expected) {
            $this->assertContains('cannot be found', $expected->getMessage());
        }
    }

    public function testInvalidKeys()
    {
        $sections = array('leadingdot', 'onedot', 'twodots', 'threedots', 'trailingdot');
        foreach ($sections as $section) {
            try {
                $array = Zend_Config_Ini::load($this->_iniFileConfig, $section);
                var_dump($array);
                $this->fail('An expected Zend_Config_Exception has not been raised');
            } catch (Zend_Config_Exception $expected) {
                $this->assertContains('Invalid key', $expected->getMessage());
            }
        }
    }

}
