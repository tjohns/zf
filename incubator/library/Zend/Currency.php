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
    const USESYMBOL = 'Zend_Currecny::USESYMBOL';
    const NOSYMBOL  = 'Zend_Currency::NOSYMBOL' ;
    
    
    /**
     * constants for enabling and disabling the use of currency Names
     */
    const USENAME = 'Zend_Currency::USENAME';
    const NONAME  = 'Zend_Currency::NONAME' ;
    
    
    /**
     * the locale name of the region that uses the currency
     * 
     * @var string|Zend_Locale
     */
    private $_CurrencyLocale = null ;
    
    
    /**
     * the short name of the currency
     * 
     * @var string
     */
    private $_CurrencyShortName = null ;
    
    
    /**
     * the script name which used to format the outputed numbers
     * 
     * @var string
     */
    private $_NumberScript;
    
    //TODO declare the other needed properties
    
    
    /**
     * Constructor 
     *
     * @param  string $currency     OPTIONAL currency short name
     * @param  string $locale       OPTIONAL locale name
     * @param  string $script       OPTIONAL script name
     * @return object Zend_Currency
     * @throws Zend_Currency_Exception
     */ 
    public function __construct($currency = false, $locale = false, $script = false)
    {
        /*
         * supporting flexible parameters
         */
        $params = array(
                      1 => $currency,
                      2 => $locale,
                      3 => $script
                        );
                        
         $currency = $locale = $script = false ;
                        
        foreach ($params as $num => $param){
            
            //get the locale
            if ( (is_object($param) && $param instanceof Zend_Locale) || 
                 (is_string($param) && preg_match('/^[a-z]{2}_[A-Z]{2}$/',$param)) ) {
                
                if (empty($this->_CurrencyLocale)){
                    $this->_setCurrencyLocale($param);
                }else{
                    throw new Zend_Currency_Exception('many locales passed');
                }
            //get the currency short name   
            }elseif (is_string($param) && strlen($param) == 3) {
                
                if(empty($this->_CurrencyShortName)) {
                    $this->_setCurrencyShortName($param);
                }else{
                    throw new Zend_Currency_Exception('many currency names passed');
                }
            //get the script name
            }elseif (is_string($param) && strlen($param) == 4) {
                
                if(empty($this->_NumberScript)) {
                    $this->_setNumberScript($param);
                }else{
                    throw new Zend_Currency_Exception('many number script names passed');
                }
            //unknown data passed in this param  
            }elseif($param !== false){
                throw new Zend_Currency_Exception('unknown value passed at param #' . $num);
            }
            
        }
        
        
        //make sure that the locale is passed
        if(empty($this->_CurrencyLocale)) {
            throw new Zend_Currency_Exception('you should pass the locale of the currency');
        }
        
        
        //TODO create another method to get the data from Zend_Locale_Data
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
        //TODO finish this method
    } 
    
    
    /**
     * Serialization Interface [Serializable]
     *
     * @param string $serialized  serialized Zend_Currency object
     * @return object Zend_Currency
     */ 
    public function unserialize($serialized) 
    {
        //TODO finish this method
    } 
 

 
    /**
     * Returns a complete currency string
     *
     * @param int|float $value  the number
     * @param string    $locale OPTIONAL the locale for formatting the number
     * @param string    $script OPTIONAL the script of the numbers
     * @return string
     */ 
    public function toCurrency($value, $locale = false, $script = false) 
    {
        //TODO finish this method
    } 
    
    
    /**
     * sets the formating options of the outputed numbers
     * if no parameters passed, the currency's locale will be used
     *
     * @param string $locale    OPTIONAL locale name
     * @param string $script    OPTIONAL the script of the numbers
     * @return object Zend_Currency
     */
    public function setFormat($locale = false, $script = false)
    {
        //TODO finish this method
    }
    
 
    /**
     * sets the options of the outputed sign
     * if no parameters, the currency's locale will be used and the use of
     * currency Symbols and Names will be enabled
     *
     * @param string $locale    OPTIONAL the locale of the currency's name
     * @param string $side      OPTIONAL the place of the sign [right|left]
     * @param const  $useSymbol OPTIONAL use currency Symbols or not
     * @param const  $useName   OPTIONAL use currency Names or not
     * @return object Zend_Currency
     */
    public function setSign ($locale = false, $side = false, $useSymbol = false, $useName = false)
    {
        //TODO finish this method
    }
    
    
    /**
     * Returns the currency symbol, 
     * when no symbol is avaiable it returns the currency shortname (f.e. FIM for Finnian Mark)
     *
     * @param string $currency   Currency name
     * @param string $locale     The locale which the currency belongs to
     * @return string
     */ 
    public static function getCurrencySign($currency, $locale) 
    {
        //TODO finish this method
    } 

 
    /**
     * Returns the name of the currency
     *
     * @param string $currency   Currency's short name
     * @param string $locale     OPTIONAL the locale
     * @return string
     */ 
    public static function getCurrencyName($currency, $locale) 
    {
        //TODO finish this method
    } 
 
 
    /**
     * Returns a list of regions where this currency is or was known
     *
     * @param string $currency Currency's short name
     * @return array           List of regions
     */ 
    public static function getRegionForCurrency($currency) 
    {
        //TODO finish this method
    } 
    
 
    /**
     * Returns a list of currencies which are used in this region
     * a region name should be 2 charachters only (f.e. EG, DE, US)
     *
     * @param string $region Currency Type
     * @return array         List of currencys
     */ 
    public static function getCurrencyForRegion($region) 
    {
        //TODO finish this method
    } 
     
     
    /**
     * Returns the currency name 
     *
     * @return string
     */ 
    public function toString()
    {
        //TODO finish this method
    } 


    /**
     * Returns the currency name 
     *
     * @return string
     */ 
    public function __toString()
    {
        //TODO finish this method
    }
 
    
    /**
     * sets the locale of the currency
     * 
     * @param string|Zend_Locale $locale    the locale
     * @return void
     * @throws Zend_Currency_Exception
     */
    private function _setCurrencyLocale($locale)
    {
        if (is_object($locale) && $locale instanceof Zend_Locale){
            $this->_CurrencyLocale = $locale ;
        }elseif (is_string($locale)) {
            $this->_CurrencyLocale = new Zend_Locale($locale);
        }else{
            throw new Zend_Currency_Exception('invalid locale');
        }
    }
    
    
    /**
     * sets the short name of the currency
     * 
     * @param string $currency    currency short name
     * @return void
     * @throws Zend_Currency_Exception
     */
    private function _setCurrencyShortName($currency)
    {
        if (is_string($currency) && strlen($currency) == 3) {
            $this->_CurrencyShortName = strtoupper($currency);
        }else{
            throw new Zend_Currency_Exception('invalid currency short name');
        }
    }
    
    
    /**
     * sets the script name which used for formatting the outputed numbers
     * 
     * @param string $script    script name
     * @return void
     * @throws Zend_Currency_Exception
     */
    private function _setNumberScript($script)
    {
        if (is_string($script) && strlen($script) == 4) {
            $this->_NumberScript = $script ;
        }else{
            throw new Zend_Currency_Exception('invalid script name');
        }
    }

} 