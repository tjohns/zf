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
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Ccnum.php 8064 2008-02-16 10:58:39Z thomas $
 */

/**
 * @see Zend_Validate_Abstract
 */
require_once 'Zend/Validate/Abstract.php';

/**
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Validate_Ccnum extends Zend_Validate_Abstract
{
    const CHECKSUM = 'ccnumChecksum';
    const LENGTH   = 'ccnumLength';

    const AMERICAN_EXPRESS = 'AmericanExpress';
    const BANKCARD = 'Bankcard';
    const DINERS_CLUB = 'DinersClub';
    const DISCOVER_CARD = 'DiscoverCard';
    const GENERIC = 'Generic';
    const JCB = 'Jcb';
    const LASER = 'Laser';
    const MAESTRO = 'Maestro';
    const MASTERCARD = 'Mastercard';
    const SOLO = 'Solo';
    const SWITCH_CARD = 'SwitchCard';
    const VISA = 'Visa';

    protected static $_data = array(
        'AmericanExpress' => array('prefix' => '34,37', 'length' => '15'),
        'Bankcard' => array('prefix' => '5610,560221-560225', 'length' => '16'),
        'DinersClub' => array('prefix' => '300-305,36', 'length' => '14'),
        'DinersClub2' => array('prefix' => '54,55', 'length' => '16'),
        'DiscoverCard' => array('prefix' => '6011,622126-622925,644-649,65', 'length' => '16'),
        'Generic' => array('prefix' => '', 'length' => '12,13,14,15,16,17,18,19'),
        'Jcb' => array('prefix' => '3528-3589', 'length' => '16'),
        'Laser' => array('prefix' => '6304,6706,6771,6709', 'length' => '16,17,18,19'),
        'Maestro' => array('prefix' => '5018,5020,5038,6304,6759,6761', 'length' => '12,13,14,15,16,17,18,19'),
        'Mastercard' => array('prefix' => '51-55', 'length' => '16'),
        'Solo' => array('prefix' => '6334,6767', 'length' => '16,18,19'),
        'SwitchCard' => array('prefix' => '4903,4905,4911,4936,564182,633110,6333,6759', 'length' => '16,18,19'),
        'Visa' => array('prefix' => '4', 'length' => '13,16')
    );

    protected $_creditcard = 'Generic';

    /**
     * Digits filter for input
     *
     * @var Zend_Filter_Digits
     */
    protected static $_filter = null;

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::LENGTH   => "'%value%' must contain between 13 and 19 digits",
        self::CHECKSUM => "Luhn algorithm (mod-10 checksum) failed on '%value%'"
    );

    /**
     * Constructor
     *
     * @param string $options Credit Card Institute to validate against
     */
    public function __construct($options = 'Generic')
    {
        $this->setCreditCard($options);
    }

    /**
     * Sets a credit card institute to validate against
     *
     * @param string $creditcard Credit Card Institute to validate against
     */
    public function setCreditCard($creditcard)
    {
        if (!is_string($creditcard)) {
            $creditcard = self::GENERIC;
        }

        if (!array_key_exists($creditcard, self::$_data)) {
            $creditcard = self::GENERIC;
        }

        $this->_creditcard = $creditcard;
    }

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if $value follows the Luhn algorithm (mod-10 checksum)
     *
     * @param  string $value
     * @return boolean
     */
    public function isValid($value)
    {
        $this->_setValue($value);

        if (null === self::$_filter) {
            require_once 'Zend/Filter/Digits.php';
            self::$_filter = new Zend_Filter_Digits();
        }

        $valueFiltered = self::$_filter->filter($value);
        $length        = strlen($valueFiltered);
        $allowedlength = explode(',', self::$_data[$this->_creditcard]['length']);
        if (!in_array($length, $allowedlength)) {
            $this->_error(self::LENGTH);
            return false;
        }

        $prefix = explode(',', self::$_data[$this->_creditcard]['prefix']);
        foreach ($prefix as $pref) {
            if (strpos($pref, '-')) {
                $pref = explode('-', $pref);
            }

            if (is_array($pref)) {
                $length = strlen($pref[0]);
            } else {

            }
            // check prefix
        }

        $sum    = 0;
        $weight = 2;

        for ($i = $length - 2; $i >= 0; $i--) {
            $digit = $weight * $valueFiltered[$i];
            $sum += floor($digit / 10) + $digit % 10;
            $weight = $weight % 2 + 1;
        }

        if ((10 - $sum % 10) % 10 != $valueFiltered[$length - 1]) {
            $this->_error(self::CHECKSUM, $valueFiltered);
            return false;
        }

        return true;
    }

}
