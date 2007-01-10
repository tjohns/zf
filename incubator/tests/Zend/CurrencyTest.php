<?php
/**
 * @package    Zend_Currency
 * @subpackage UnitTests
 */

error_reporting( E_ALL | E_STRICT ); // now required for each test suite

/**
 * Zend_Currency
 */
require_once 'Zend.php';
require_once 'Zend/Locale.php';
require_once 'Zend/Currency.php';


/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework.php';


/**
 * @package    Zend_Currency
 * @subpackage UnitTests
 */
class Zend_CurrencyTest extends PHPUnit_Framework_TestCase
{
    
    /**
     * tests the creation of Zend_Currency
     *
     */
    public function testCreation()
    {
        $locale = new Zend_Locale('en_US');
        
        $currency = new Zend_Currency('USD','en_US','Arab');
        $this->assertTrue($currency instanceof Zend_Currency);
                
        $currency = new Zend_Currency('USD',$locale,'Arab');
        $this->assertTrue($currency instanceof Zend_Currency);
        
        
        $currency = new Zend_Currency('en_US', 'USD','Arab');
        $this->assertTrue($currency instanceof Zend_Currency);
                
        $currency = new Zend_Currency($locale, 'USD','Arab');
        $this->assertTrue($currency instanceof Zend_Currency);

        
        $currency = new Zend_Currency('en_US','Arab','USD');
        $this->assertTrue($currency instanceof Zend_Currency);
                
        $currency = new Zend_Currency($locale,'Arab', 'USD');
        $this->assertTrue($currency instanceof Zend_Currency);
        
        
        $currency = new Zend_Currency('en_US', 'USD');
        $this->assertTrue($currency instanceof Zend_Currency);
                
        $currency = new Zend_Currency($locale, 'USD');
        $this->assertTrue($currency instanceof Zend_Currency);
        
        
        $currency = new Zend_Currency('USD', 'en_US');
        $this->assertTrue($currency instanceof Zend_Currency);
                
        $currency = new Zend_Currency('USD', $locale);
        $this->assertTrue($currency instanceof Zend_Currency);
        
        
        $currency = new Zend_Currency('Arab', 'en_US');
        $this->assertTrue($currency instanceof Zend_Currency);
                
        $currency = new Zend_Currency('Arab', $locale);
        $this->assertTrue($currency instanceof Zend_Currency);
        
        
        $currency = new Zend_Currency('en_US', 'Arab' );
        $this->assertTrue($currency instanceof Zend_Currency);
                
        $currency = new Zend_Currency($locale, 'Arab');
        $this->assertTrue($currency instanceof Zend_Currency);
        
        
        $currency = new Zend_Currency('en_US');
        $this->assertTrue($currency instanceof Zend_Currency);
                
        $currency = new Zend_Currency($locale);
        $this->assertTrue($currency instanceof Zend_Currency);
        
        
        
        /*
         * failure tests
         */
        
        try{
            $currency = new Zend_Currency('USD');
            $this->fail();
        }catch(Zend_Currency_Exception $e){}
        
        
        try{
            $currency = new Zend_Currency('Arab');
            $this->fail();
        }catch(Zend_Currency_Exception $e){}

        
        try{
            $currency = new Zend_Currency('aG_ea');
            $this->fail();
        }catch(Zend_Currency_Exception $e){}
        
        
    }
    
    
}

?>