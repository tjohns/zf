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
 * @package    Zend_Currency
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: DataTest.php 3776 2007-03-06 22:50:56Z thomas $
 */

/**
 * Test helper
 */
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'TestHelper.php';

/**
 * Zend_Currency
 */
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

    public function setUp()
    {
        require_once 'Zend/Cache.php';
        $cache = Zend_Cache::factory('Core', 'File', 
                 array('lifetime' => 120, 'automatic_serialization' => true), 
                 array('cache_dir' => dirname(__FILE__) . '/_files/'));
        Zend_Currency::setCache($cache);
    }

    /**
     * tests the creation of Zend_Currency
     */
    public function testSingleCreation()
    {
        $locale = new Zend_Locale('de_AT');

        $currency = new Zend_Currency();
        $this->assertTrue($currency instanceof Zend_Currency);

        $currency = new Zend_Currency('de_AT');
        $this->assertTrue($currency instanceof Zend_Currency);
        $this->assertSame($currency->toCurrency(1000), '€ 1.000,00');

        $currency = new Zend_Currency($locale);
        $this->assertTrue($currency instanceof Zend_Currency);
        $this->assertSame($currency->toCurrency(1000), '€ 1.000,00');

        try {
            $currency = new Zend_Currency('de_XX');
            $this->fail("locale should always include region and therefor not been recognised");
        } catch (Zend_Currency_Exception $e) {
            // success
        }

        try {
            $currency = new Zend_Currency('xx_XX');
            $this->fail("unknown locale should not have been recognised");
        } catch (Zend_Currency_Exception $e) {
            // success
        }

        $currency = new Zend_Currency('EUR');
        $this->assertTrue($currency instanceof Zend_Currency);

        $currency = new Zend_Currency('USD');
        $this->assertTrue($currency instanceof Zend_Currency);

        $currency = new Zend_Currency('AWG');
        $this->assertTrue($currency instanceof Zend_Currency);

        try {
            $currency = new Zend_Currency('XYZ');
            $this->fail("unknown shortname should not have been recognised");
        } catch (Zend_Currency_Exception $e) {
            // success
        }
    }


    /**
     * tests the creation of Zend_Currency
     */
    public function testDualCreation()
    {
        $locale = new Zend_Locale('de_AT');

        $currency = new Zend_Currency('USD', 'de_AT');
        $this->assertTrue($currency instanceof Zend_Currency);
        $this->assertSame($currency->toCurrency(1000), '$ 1.000,00');

        $currency = new Zend_Currency('USD', $locale);
        $this->assertTrue($currency instanceof Zend_Currency);
        $this->assertSame($currency->toCurrency(1000), '$ 1.000,00');

        $currency = new Zend_Currency('de_AT', 'USD');
        $this->assertTrue($currency instanceof Zend_Currency);
        $this->assertSame($currency->toCurrency(1000), '$ 1.000,00');

        $currency = new Zend_Currency($locale, 'USD');
        $this->assertTrue($currency instanceof Zend_Currency);
        $this->assertSame($currency->toCurrency(1000), '$ 1.000,00');

        $currency = new Zend_Currency('EUR', 'de_AT');
        $this->assertTrue($currency instanceof Zend_Currency);
        $this->assertSame($currency->toCurrency(1000), '€ 1.000,00');

        try {
            $currency = new Zend_Currency('EUR', 'xx_YY');
            $this->fail("unknown locale should not have been recognised");
        } catch (Zend_Currency_Exception $e) {
            // success
        }
    }


    /**
     * tests the creation of Zend_Currency
     */
    public function testTripleCreation()
    {
        $locale = new Zend_Locale('de_AT');

        $currency = new Zend_Currency('USD', 'de_AT');
        $this->assertTrue($currency instanceof Zend_Currency);
        $this->assertSame($currency->toCurrency(1000), '$ 1.000,00');

        $currency = new Zend_Currency('USD', $locale);
        $this->assertTrue($currency instanceof Zend_Currency);
        $this->assertSame($currency->toCurrency(1000), '$ 1.000,00');

        try {
            $currency = new Zend_Currency('XXX', 'Latin', $locale);
            $this->fail("unknown shortname should not have been recognised");
        } catch (Zend_Currency_Exception $e) {
            // success
        }

        try {
            $currency = new Zend_Currency('USD', 'Xyzz', $locale);
            $this->fail("unknown script should not have been recognised");
        } catch (Zend_Currency_Exception $e) {
            // success
        }

        try {
            $currency = new Zend_Currency('USD', 'Latin', 'xx_YY');
            $this->fail("unknown locale should not have been recognised");
        } catch (Zend_Currency_Exception $e) {
            // success
        }

        $currency = new Zend_Currency('USD', 'de_AT');
        $this->assertTrue($currency instanceof Zend_Currency);
        $this->assertSame($currency->toCurrency(1000), '$ 1.000,00');

        $currency = new Zend_Currency('Euro', 'de_AT');
        $this->assertTrue($currency instanceof Zend_Currency);
        $this->assertSame($currency->toCurrency(1000), 'EUR 1.000,00');

        $currency = new Zend_Currency('USD', $locale);
        $this->assertTrue($currency instanceof Zend_Currency);
        $this->assertSame($currency->toCurrency(1000), '$ 1.000,00');

        $currency = new Zend_Currency('de_AT', 'EUR');
        $this->assertTrue($currency instanceof Zend_Currency);
        $this->assertSame($currency->toCurrency(1000), '€ 1.000,00');

        $currency = new Zend_Currency($locale, 'USD');
        $this->assertTrue($currency instanceof Zend_Currency);
        $this->assertSame($currency->toCurrency(1000), '$ 1.000,00');

        $currency = new Zend_Currency('EUR', 'en_US');
        $this->assertTrue($currency instanceof Zend_Currency);
        $this->assertSame($currency->toCurrency(1000), '€ 1.000,00');

        $currency = new Zend_Currency('en_US', 'USD');
        $this->assertTrue($currency instanceof Zend_Currency);
        $this->assertSame($currency->toCurrency(1000), '$ 1.000,00');

        $currency = new Zend_Currency($locale, 'EUR');
        $this->assertTrue($currency instanceof Zend_Currency);
        $this->assertSame($currency->toCurrency(1000), '€ 1.000,00');
    }


    /**
     * tests failed creation of Zend_Currency
     */
    public function testFailedCreation()
    {
        $locale = new Zend_Locale('de_AT');

        try {
            $currency = new Zend_Currency('de_AT', 'en_US');
            $this->fail();
        } catch (Zend_Currency_Exception $e) {
            // success
        }

        try {
            $currency = new Zend_Currency('USD', 'EUR');
            $this->fail();
        } catch (Zend_Currency_Exception $e) {
            // success
        }

        try {
            $currency = new Zend_Currency('Arab', 'Latn');
            $this->fail();
        } catch (Zend_Currency_Exception $e) {
            // success
        }

        try {
            $currency = new Zend_Currency('EUR');
            $currency->toCurrency('value');
            $this->fail();
        } catch (Zend_Currency_Exception $e) {
            // success
        }

        $currency = new Zend_Currency('EUR', 'de_AT');
        $currency->setFormat(array('display' => 'SIGN'));
        $this->assertSame($currency->toCurrency(1000), 'SIGN 1.000,00');

        try {
            $currency = new Zend_Currency('EUR');
            $currency->setFormat(array('format' => 'xy_ZY'));
            $this->fail();
        } catch (Zend_Currency_Exception $e) {
            // success
        }
    }


    /*
     * testing toCurrency
     */
    public function testToCurrency()
    {
        $USD = new Zend_Currency('USD','en_US');
        $EGP = new Zend_Currency('EGP','ar_EG');

        $this->assertSame($USD->toCurrency(53292.18), '$ 53,292.18');
        $this->assertSame($USD->toCurrency(53292.18, array('script' => 'Arab')), '$ ٥٣,٢٩٢.١٨');
        $this->assertSame($USD->toCurrency(53292.18, array('script' => 'Arab', 'format' => 'de_AT')), '$ ٥٣.٢٩٢,١٨');
        $this->assertSame($USD->toCurrency(53292.18, array('format' => 'de_AT')), '$ 53.292,18');

        $this->assertSame($EGP->toCurrency(53292.18), 'ج.م.‏ 53٬292٫18');
        $this->assertSame($EGP->toCurrency(53292.18, array('script' => 'Arab')), 'ج.م.‏ ٥٣٬٢٩٢٫١٨');
        $this->assertSame($EGP->toCurrency(53292.18, array('script' =>'Arab', 'format' => 'de_AT')), 'ج.م.‏ ٥٣.٢٩٢,١٨');
        $this->assertSame($EGP->toCurrency(53292.18, array('format' => 'de_AT')), 'ج.م.‏ 53.292,18');

        $USD = new Zend_Currency('en_US');
        $this->assertSame($USD->toCurrency(53292.18), '$ 53,292.18');
    }


    /**
     * testing setFormat
     *
     */
    public function testSetFormat()
    {
        $locale = new Zend_Locale('en_US');
        $USD    = new Zend_Currency('USD','en_US');

        $USD->setFormat(array('script' => 'Arab'));
        $this->assertSame($USD->toCurrency(53292.18), '$ ٥٣,٢٩٢.١٨');

        $USD->setFormat(array('script' => 'Arab', 'format' => 'de_AT'));
        $this->assertSame($USD->toCurrency(53292.18), '$ ٥٣.٢٩٢,١٨');

        $USD->setFormat(array('script' => 'Latn', 'format' => 'de_AT'));
        $this->assertSame($USD->toCurrency(53292.18), '$ 53.292,18');

        $USD->setFormat(array('script' => 'Latn', 'format' => $locale));
        $this->assertSame($USD->toCurrency(53292.18), '$ 53,292.18');

        // allignment of currency signs
        $USD->setFormat(array('position' => Zend_Currency::RIGHT, 'format' => 'de_AT'));
        $this->assertSame($USD->toCurrency(53292.18), '53.292,18 $');

        $USD->setFormat(array('position' => Zend_Currency::RIGHT, 'format' => $locale));
        $this->assertSame($USD->toCurrency(53292.18), '53,292.18 $');

        $USD->setFormat(array('position' => Zend_Currency::LEFT, 'format' => 'de_AT'));
        $this->assertSame($USD->toCurrency(53292.18), '$ 53.292,18');

        $USD->setFormat(array('position' => Zend_Currency::LEFT, 'format' => $locale));
        $this->assertSame($USD->toCurrency(53292.18), '$ 53,292.18');

        $USD->setFormat(array('position' => Zend_Currency::STANDARD, 'format' => 'de_AT'));
        $this->assertSame($USD->toCurrency(53292.18), '$ 53.292,18');

        $USD->setFormat(array('position' => Zend_Currency::STANDARD, 'format' => $locale));
        $this->assertSame($USD->toCurrency(53292.18), '$ 53,292.18');

        // enable/disable currency symbols & currency names
        $USD->setFormat(array('display' => Zend_Currency::NO_SYMBOL, 'format' => 'de_AT'));
        $this->assertSame($USD->toCurrency(53292.18), '53.292,18');

        $USD->setFormat(array('display' => Zend_Currency::NO_SYMBOL, 'format' => $locale));
        $this->assertSame($USD->toCurrency(53292.18), '53,292.18');

        $USD->setFormat(array('display' => Zend_Currency::USE_SHORTNAME, 'format' => 'de_AT'));
        $this->assertSame($USD->toCurrency(53292.18), 'USD 53.292,18');

        $USD->setFormat(array('display' => Zend_Currency::USE_SHORTNAME, 'format' => $locale));
        $this->assertSame($USD->toCurrency(53292.18), 'USD 53,292.18');

        $USD->setFormat(array('display' => Zend_Currency::USE_NAME, 'format' => 'de_AT'));
        $this->assertSame($USD->toCurrency(53292.18), 'US Dollar 53.292,18');

        $USD->setFormat(array('display' => Zend_Currency::USE_NAME, 'format' => $locale));
        $this->assertSame($USD->toCurrency(53292.18), 'US Dollar 53,292.18');

        $USD->setFormat(array('display' => Zend_Currency::USE_SYMBOL, 'format' => 'de_AT'));
        $this->assertSame($USD->toCurrency(53292.18), '$ 53.292,18');

        $USD->setFormat(array('display' => Zend_Currency::USE_SYMBOL, 'format' => $locale));
        $this->assertSame($USD->toCurrency(53292.18), '$ 53,292.18');
    }


    /**
     * test getSign
     */
    public function testGetSign()
    {
        $locale   = new Zend_Locale('ar_EG');
        $currency = new Zend_Currency('ar_EG');

        $this->assertSame($currency->getSymbol('EGP','ar_EG'), 'ج.م.‏');
        $this->assertSame($currency->getSymbol('EUR','de_AT'), '€');
        $this->assertSame($currency->getSymbol('ar_EG'), 'ج.م.‏');
        $this->assertSame($currency->getSymbol('de_AT'), '€');

        try {
            $currency->getSymbol('EGP', 'de_XX');
            $this->fail();
        } catch (Zend_Currency_Exception $e) {
            // success
        }
    }


    /**
     * test getName
     */
    public function testGetName()
    {
        $locale   = new Zend_Locale('ar_EG');
        $currency = new Zend_Currency('ar_EG');

        $this->assertSame($currency->getName('EGP','ar_EG'), 'جنيه مصرى');
        $this->assertSame($currency->getName('EEK','de_AT'), 'Estnische Krone');
        $this->assertSame($currency->getName('EGP',$locale), 'جنيه مصرى');
        $this->assertSame($currency->getName('ar_EG'), 'جنيه مصرى');
        $this->assertSame($currency->getName('de_AT'), 'Euro');

        try {
            $currency->getName('EGP', 'xy_XY');
            $this->fail();
        } catch (Zend_Currency_Exception $e) {
            // success
        }
    }


    /**
     * test getShortName
     */
    public function testGetShortName()
    {
        $locale   = new Zend_Locale('de_AT');
        $currency = new Zend_Currency('de_AT');

        $this->assertSame($currency->getShortName('Euro','de_AT'), 'EUR');
        $this->assertSame($currency->getShortName('Euro',$locale), 'EUR');
        $this->assertSame($currency->getShortName('US-Dollar','de_AT'), 'USD');
        $this->assertSame($currency->getShortName('de_AT'), 'EUR');

        try {
            $currency->getShortName('EUR', 'xy_ZT');
            $this->fail();
        } catch (Zend_Currency_Exception $e) {
            // success
        }
    }


    /**
     * testing getRegionList
     */
    public function testGetRegionList()
    {
        $currency = new Zend_Currency('USD');
        $this->assertTrue(in_array('US', $currency->getRegionList()));
    }


    /**
     * testing getCurrencyList
     */
    public function testGetCurrencyList()
    {
        $currency = new Zend_Currency('ar_EG');
        $this->assertTrue(array_key_exists('EGP', $currency->getCurrencyList()));
    }


    /**
     * testing toString
     *
     */
    public function testToString()
    {
        $USD = new Zend_Currency('USD','en_US');
        $this->assertSame($USD->toString(), 'US Dollar');
        $this->assertSame($USD->__toString(), 'US Dollar');
    }
}
