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
 * @package    Zend_Locale
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * Utility class for proxying math function to bcmath functions, if present,
 * otherwise to PHP builtin math operators, with limited detection of overflow conditions.
 * Sampling of PHP environments and platforms suggests that at least 80% to 90% support bcmath.
 * Thus, this file should be as light as possible.
 *
 * @category   Zend
 * @package    Zend_Locale
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

class Zend_Locale_Math
{
    // support unit testing without using bcmath functions 
    protected static $_bcmathDisabled = false;

    public static $add   = 'bcadd';
    public static $sub   = 'bcsub';
    public static $pow   = 'bcpow';
    public static $mul   = 'bcmul';
    public static $div   = 'bcdiv';
    public static $comp  = 'bccomp';
    public static $sqrt  = 'bcsqrt';
    public static $mod   = 'bcmod';
    public static $scale = 'bcscale';

    public static function isBcmathDisabled()
    {
        return self::$_bcmathDisabled;
    }

    public static function round($op1, $op2 = 0) {
        $value = call_user_func(Zend_Locale_Math::$sub, $op1, '0', $op2);
        $value = call_user_func(Zend_Locale_Math::$sub, $op1, $value, $op2);
        return   call_user_func(Zend_Locale_Math::$sub, $op1, $value, $op2);
    }
}

if ((defined('TESTS_ZEND_LOCALE_BCMATH_ENABLED') && !TESTS_ZEND_LOCALE_BCMATH_ENABLED)
    || !extension_loaded('bcmath')) {
    require_once 'Zend/Locale/Math/PhpMath.php';
}

?>
