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
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * include needed classes
 */
require_once 'Zend/Locale/Data.php';
require_once 'Zend/Locale.php';
require_once 'Zend/Locale/Format.php';
require_once 'Zend/Currency/Exception.php';


/**
 * @category   Zend
 * @package    Zend_Currency
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Currency implements Serializable { 
 
    
    /**
     * constants for enabling and disabling the use of currency Symbols
     */
    const USESYMBOL = 'US';
    const NOSYMBOL  = 'NS';
    
    
    /**
     * constants for enabling and disabling the use of currency Names
     */
    const USENAME = 'UN';
    const NONAME  = 'NN';
    
    
    //TODO: declare the needed properties
    
    
    /**
     * Constructor 
     *
     * @param string $currency    - OPTIONAL currency short name
     * @param string $locale      - OPTIONAL locale name
     * @param string $script      - OPTIONAL script name
     * @return object Zend_Currency
     */ 
    public function __construct($currency = false, $locale = false, $script = false)
    {
    
    } 

    
    /**
     * Serialization Interface [Serializable]
     *
     * @return string
     */ 
    public function serialize()
    {
        $data = array(
                );
        return serialize($data);
    } 
    
    
    /**
     * Serialization Interface [Serializable]
     *
     * @param string $serialized  - a serialized version
     * @return object Zend_Currency
     */ 
    public function unserialize($serialized) 
    {
    
    } 
 

 
    /**
     * Returns a complete currency string
     *
     * @param int|float $value  - the number
     * @param string    $locale - OPTIONAL the locale for formatting the number
     * @param string    $script - OPTIONAL the script of the numbers
     * @return string
     */ 
    public function toCurrency($value, $locale = false, $script = false) 
    {
    
    } 
    
    
    /**
     * sets the formating options of the outputed numbers
     * if no parameters passed, the currency's locale will be used
     *
     * @param string $locale    - OPTIONAL locale name
     * @param string $script    - OPTIONAL the script of the numbers
     * @return object Zend_Currency
     */
    public function setFormat($locale = false, $script = false)
    {
    
    }
    
 
    /**
     * sets the options of the outputed sign
     * if no parameters, the currency's locale will be used and the use of
     * currency Symbols and Names will be enabled
     *
     * @param string $locale    - OPTIONAL the locale of the currency's name
     * @param string $side      - OPTIONAL the place of the sign [right|left]
     * @param const  $useSymbol - OPTIONAL use currency Symbols or not
     * @param const  $useName   - OPTIONAL use currency Names or not
     * @return object Zend_Currency
     */
    public function setSign ($locale = false, $side = false, $useSymbol = false, $useName = false)
    {
    
    }
    
    
    /**
     * Returns the currency symbol, 
     * when no symbol is avaiable it returns the currency shortname (f.e. FIM for Finnian Mark)
     *
     * @param string $currency   - Currency name
     * @param string $locale     - The locale which the currency belongs to
     * @return string
     */ 
    public static function getCurrencySign($currency, $locale) 
    {
    
    } 

 
    /**
     * Returns the name of the currency
     *
     * @param string $currency   - Currency's short name
     * @param string $locale     - OPTIONAL the locale
     * @return string
     */ 
    public static function getCurrencyName($currency, $locale) 
    {
    
    } 
 
 
    /**
     * Returns a list of regions where this currency is or was known
     *
     * @param string $currency - Currency's short name
     * @return array           - List of regions
     */ 
    public static function getRegionForCurrency($currency) 
    {
    
    } 
    
 
    /**
     * Returns a list of currencies which are used in this region
     * a region name should be 2 charachters only (f.e. EG, DE, US)
     *
     * @param string $region - Currency Type
     * @return array         - List of currencys
     */ 
    public static function getCurrencyForRegion($region) 
    {
    
    } 
     
     
    /**
     * Returns the currency name 
     *
     * @return string
     */ 
    public function toString()
    {
    
    } 


    /**
     * Returns the currency name 
     *
     * @return string
     */ 
    public function __toString()
    {
    
    }
 

} 