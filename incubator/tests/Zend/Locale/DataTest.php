<?php
/**
 * @package    Zend_Locale
 * @subpackage UnitTests
 */


/**
 * Zend_Locale_Data
 */
require_once 'Zend/Locale/Data.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @package    Zend_Locale
 * @subpackage UnitTests
 */
class Zend_Locale_DataTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
    }
    
    
    /**
     * test for reading one language from locale
     * expected array
     */
    public function testLDMLReadingLanguage()
    {
        $value = Zend_Locale_Data::getContent('de','language','de');
        $this->assertEquals($value['de'],'Deutsch');
    }


    /**
     * test for reading one script from locale
     * expected array
     */
    public function testLDMLReadingScript()
    {
        $value = Zend_Locale_Data::getContent('de_AT','script','Arab');
        $this->assertEquals($value['Arab'],'Arabisch');
    }


    /**
     * test for negative string in fa
     * expected exception
     */
/*    public function testGetNumberNegativeFa()
    {
        $value = Zend_Locale_Format::getNumber('-123','fa');
        $this->assertEquals(-123,$value);
    }
*/
    /**
     * test for decimal string in de
     * expected exception
     */
/*    public function testGetNumberDecimal()
    {
        $value = Zend_Locale_Format::getNumber('-123.456','de');
        $this->assertEquals(-123.456,$value);
    }
*/
}
