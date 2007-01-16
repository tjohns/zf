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
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id$
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * include needed classes
 */
require_once 'Zend/Locale.php';
require_once 'Zend/Currency/Exception.php';


/**
 * @category   Zend
 * @package    Zend_Currency
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Currency { 
 
    
    /**
     * constants for enabling and disabling the use of currency Symbols
     */
    const USE_SYMBOL = 0;
    const NO_SYMBOL  = 1;
    
    
    /**
     * constants for enabling and disabling the use of currency Names
     */
    const USE_NAME  = 2;
    const NO_NAME   = 4;

    const STANDARD = 8;
    const RIGHT    = 16;
    const LEFT     = 32;
    
    
    /**
     * the locale name of the region that uses the currency
     * 
     * @var Zend_Locale
     */
    private $_currencyLocale = null;
    
    
    /**
     * the short name of the currency
     * 
     * @var string
     */
    private $_shortName = null;
    
    
    /**
     * the full name of the currency
     * 
     * @var string
     */
    private $_fullName = null;
    
    /**
     * the symbol of the currency
     * 
     * @var string
     */
    private $_symbol = null;
    
    
    /**
     * the position of the symbol
     * 
     * @var const
     */
    private $_symbolPosition = null;
    
    
    /**
     * the locale of the symbol
     * 
     * @var Zend_Locale
     */
    private $_symbolLocale = null;
    
    
    /**
     * the script name which used to format the outputed numbers
     * 
     * @var string
     */
    private $_numberScript = null;
    
    
    /**
     * the locale for formating the output
     *
     * @var Zend_Locale
     */
    private $_formatLocale = null;
    
    
    /**
     * Constructor 
     *
     * @param  string              $currency  OPTIONAL currency short name
     * @param  string              $script    OPTIONAL script name
     * @param  string|Zend_Locale  $locale    OPTIONAL locale name
     * @return Zend_Currency
     * @throws Zend_Currency_Exception
     */ 
    public function __construct($currency = NULL, $script = NULL, $locale = NULL)
    {
        /*
         * supporting flexible parameters
         */
        $params = array(
                      1 => $currency,
                      2 => $locale,
                      3 => $script);
                        
         $currency = $locale = $script = false ;
                        
        foreach ($params as $num => $param){
            
            //get the locale
            if ( (is_object($param) && $param instanceof Zend_Locale) || 
                 (is_string($param) && preg_match('/^[a-z]{2}_[A-Z]{2}$/',$param)) ) {
                
                if (empty($this->_CurrencyLocale)){
                    $this->_setCurrencyLocale($param);
                } else {
                    throw new Zend_Currency_Exception('many locales passed');
                }
            //get the currency short name   
            } else if (is_string($param) && strlen($param) == 3) {
                
                if(empty($this->_CurrencyShortName)) {
                    $this->_setCurrencyShortName($param);
                } else {
                    throw new Zend_Currency_Exception('many currency names passed');
                }
            //get the script name
            } else if (is_string($param) && strlen($param) == 4) {
                
                if (empty($this->_NumberScript)) {
                    $this->_setNumberScript($param);
                } else {
                    throw new Zend_Currency_Exception('many number script names passed');
                }
            //unknown data passed in this param  
            } else if ($param !== false){
                throw new Zend_Currency_Exception('unknown value passed at param #' . $num);
            }
            
        }
        
        
        //make sure that the locale is passed
        if (empty($this->_CurrencyLocale)) {
            throw new Zend_Currency_Exception('you should pass the locale of the currency');
        }
        
        
        //getting the data related to this currency
        $this->_updateShortName()
             ->_updateFullName()
             ->_updateSymbol()
             ->_updateFormat();
    }

    
    
    /**
     * gets the short name of the currency from the LDML files
     * 
     * @return Zend_Currency
     * @throws Zend_Currency_Exception
     */
    protected function _updateShortName()
    {
        //getting the short name of the currency
        $data = Zend_Locale_Data::getContent('','currencyforregion',$this->_currencyLocale->getRegion());
        
        $this->_shortName = $data['currency'];
        
        return $this;
    }
    
    
    /**
     * gets the full name of the currency from the LDML files
     * 
     * @return Zend_Currency
     * @throws Zend_Currency_Exception
     */
    protected function _updateFullName()
    {
        //getting the full name of the currency
        $names = Zend_Locale_Data::getContent('','currencynames',$this->_currencyLocale->getRegion());
        
        $this->_fullName = $names[$this->_shortName];
        
        return $this;
    }
    
    
    /**
     * gets the symbol of the currency from the LDML files
     * 
     * @return Zend_Currency
     * @throws Zend_Currency_Exception
     */
    protected function _updateSymbol()
    {
        $formatLocale = $this->symbolLocale;
        
        if (!$formatLocale instanceof Zend_Locale) {
            $formatLocale = $this->_currencyLocale;
        }
        
        //getting the symbol of the currency
        $symbols = Zend_Locale_Data::getContent($formatLocale, 'currencysymbols');
        
        $this->_symbol = $symbols[$this->_shortName];
        
        return $this;
    }
    
    
	/**
     * gets the information required for formating the currency from the LDML files
     * 
     * @return Zend_Currency
     * @throws Zend_Currency_Exception
     */
    protected function _updateFormat()
    {
        $formatLocale = $this->formatLocale;
        
        if (!$formatLocale instanceof Zend_Locale) {
            $formatLocale = $this->_currencyLocale;
        }
        
        //getting the format information of the currency
        $format = Zend_Locale_Data::getContent($formatLocale, 'currencyformat');
        $format = $format['default'];
        
        iconv_set_encoding('internal_encoding', 'UTF-8');
        
        if (iconv_strpos($format, ';')) {
            $format = iconv_substr($format, 0, iconv_strpos($format, ';'));
        }
        
        
        //knowing the symbol positioning information
        if (iconv_strpos($format, '¤') == 0) {
            $this->_symbolPosition = self::RIGHT;
        } else if (iconv_strpos($format, '¤') == iconv_strlen($format)-1) {
            $this->_symbolPosition = self::LEFT;
        }
        
        return $this;
    }
    
    
    /**
     * Returns a localized currency string
     *
     * @param  int|float           $value   Currency value
     * @param  string              $script  OPTIONAL Number script to use for output
     * @param  string|Zend_Locale  $locale  OPTIONAL Locale for output formatting
     * @return string
     */ 
    public function toCurrency($value, $script = NULL, $locale = NULL) 
    {
        //TODO finish this method
    } 
    
    
    /**
     * Sets the formating options of the localized currency string
     * If no parameter is passed, the standard setting of the
     * actual set locale will be used
     *
     * @param  const|string        $rules   OPTIONAL formating rules for currency
     *                  - SYMBOL|NOSYMBOL : display currency symbol
     *                  - NAME|NONAME     : display currency name
     *                  - DEFAULT|RIGHT|LEFT : where to display currency symbol/name
     *                  - string: gives the currency string/name/sign to set
     * @param  string              $script  OPTIONAL Number script to use for output
     * @param  string|Zend_Locale  $locale  OPTIONAL Locale for output formatting
     * @return Zend_Currency
     */
    public function setFormat($rules = NULL, $script = NULL, $locale = NULL)
    {
        //TODO finish this method
    }
    
    
    /**
     * Returns the actual or details of other currency symbols, 
     * when no symbol is avaiable it returns the currency shortname (f.e. FIM for Finnian Mark)
     *
     * @param  string              $currency   OPTIONAL Currency name
     * @param  string|Zend_Locale  $locale     OPTIONAL Locale to display informations
     * @return string
     */ 
    public static function getSign($currency, $locale) 
    {
        //TODO finish this method
    } 

 
    /**
     * Returns the actual or details of other currency names
     *
     * @param  string              $currency   OPTIONAL Currency's short name
     * @param  string|Zend_Locale  $locale     OPTIONAL the locale
     * @return string
     */ 
    public static function getName($currency, $locale) 
    {
        //TODO finish this method
    } 


    /**
     * Returns a list of regions where this currency is or was known
     *
     * @param  string  $currency  Currency's short name
     * @return array              List of regions
     */ 
    public static function getRegionList($currency) 
    {
        //TODO finish this method
    } 
    
 
    /**
     * Returns a list of currencies which are used in this region
     * a region name should be 2 charachters only (f.e. EG, DE, US)
     *
     * @param  string  $region  Currency Type
     * @return array            List of currencys
     */ 
    public static function getCurrencyList($region) 
    {
        //TODO finish this method
    }


    /**
     * Returns the actual currency name 
     *
     * @return string
     */ 
    public function toString()
    {
        if (!empty($this->_fullName)) {
            return $this->_fullName;
        } else {
            return $this->shortName;
        }
    }


    /**
     * Returns the currency name 
     *
     * @return string
     */ 
    public function __toString()
    {
        return $this->toString();
    }
 
    
    /**
     * sets the locale of the currency
     * 
     * @param string|Zend_Locale  $locale  the locale
     * @return void
     * @throws Zend_Currency_Exception
     */
    private function _setCurrencyLocale($locale)
    {
        if (is_object($locale) && $locale instanceof Zend_Locale){
            $this->_CurrencyLocale = $locale ;
        } else if (is_string($locale)) {
            $this->_currencyLocale = new Zend_Locale($locale);
        } else {
            throw new Zend_Currency_Exception('invalid locale');
        }
    }

    
    /**
     * sets the short name of the currency
     * 
     * @param string  $currency  currency short name
     * @return void
     * @throws Zend_Currency_Exception
     */
    private function _setCurrencyShortName($currency)
    {
        if (is_string($currency) && strlen($currency) == 3) {
            $this->_currencyShortName = strtoupper($currency);
        } else {
            throw new Zend_Currency_Exception('invalid currency short name');
        }
    }
    
    
    /**
     * sets the script name which used for formatting the outputed numbers
     * 
     * @param string $script	script name
     * @return void
     * @throws Zend_Currency_Exception
     */
    private function _setNumberScript($script)
    {
        if (is_string($script) && strlen($script) == 4) {
            $this->_numberScript = $script ;
        } else {
            throw new Zend_Currency_Exception('invalid script name');
        }
    }
}

