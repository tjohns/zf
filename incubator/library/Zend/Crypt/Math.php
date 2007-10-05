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
 * @package    Zend_Crypt
 * @subpackage Math
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id: Math.php 127 2007-09-17 13:48:20Z padraic $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Crypt_Math_BigInteger */
require_once 'Zend/Crypt/Math/BigInteger.php';

/**
 * Zend_Crypt helper class for negotiating Arbitrary Precision Big Integer
 * math extensions available for PHP and offering math functions required
 * by cryptographic algorithms.
 *
 * @package    Zend_Crypt
 * @subpackage Hmac
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Crypt_Math extends Zend_Crypt_Math_BigInteger
{

    /**
     * Generate a pseudorandom number within the given range.
     * Will attempt to read from a *nix kernal's random number
     * generator if it exists.
     *
     * @param string|int $min
     * @param string|int $max
     * @return string
     * @todo Even more pseudorandomness would be nice...
     */
    public function rand($minimum, $maximum)
    {
        if (file_exists('/dev/urandom')) {
            $frandom = fopen('/dev/urandom', 'r');
            if ($frandom !== false) {
                return fread($frandom, strlen($maximum) - 1);
            }
        }
        if (strlen($maximum) < 4) {
            return mt_rand($minimum, $maximum - 1);
        }
        $rand = '';
        $i2 = strlen($maximum) - 1;
        for ($i = 1;$i < $i2;$i++) {
            $rand .= mt_rand(0,9);
        }
        $rand .= mt_rand(0,9);
        return $rand;
    }

    /**
     * Get the big endian two's complement of a given big integer in
     * binary notation.
     *
     * @param string $long
     * @return string
     */
    public function btwoc($long) {
        if (ord($long[0]) > 127) {
            return "\x00" . $long;
        }
        return $long;
    }

    /**
     * Translate a binary form into a big integer string
     *
     * @param string $binary
     * @return string
     */
    public function fromBinary($binary) {
        if (!$this instanceof Zend_Math_BigInteger_Gmp) {
            $big = 0;
            $length = strlen($binary);
            for ($i = 0; $i < $length; $i++) {
                $big = $this->_math->multiply($big, 256);
                $big = $this->_math->add($big, ord($binary[$i]));
            }
            return $big;
        } else {
            return $this->_math->init(bin2hex($binary), 16); // gmp shortcut
        }
    }

    /**
     * Translate a big integer string into a binary form.
     *
     * @param string $big
     * @return string
     */
    public function toBinary($big)
    {
        $compare = $this->_math->compare($big, 0);
        if ($compare == 0) {
            return (chr(0));
        } else if ($compare < 0) {
            return false;
        }
        $binary = '';
        while ($this->_math->compare($big, 0) > 0) {
            $binary = chr($this->_math->modulus($big, 256)) . $binary;
            $big = $this->_math->divide($big, 256);
        }
        return $binary;
    }
}