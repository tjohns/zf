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
 * @package    Zend
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * Utility class for proxying math function to bcmath functions, if present,
 * otherwise to PHP builtin math operators, with limited detection of overflow conditions.
 *
 * @category   Zend
 * @package    Zend
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

class Zend_Locale_Math
{
    // support unit testing without using bcmath functions 
    static private $_bcmathDisabled = false;

    static public $add  = 'bcadd';
    static public $sub  = 'bcsub';
    static public $pow  = 'bcpow';
    static public $mul  = 'bcmul';
    static public $div  = 'bcdiv';
    static public $comp = 'bccomp';
    static public $sqrt = 'bcsqrt';
    static public $mod  = 'bcmod';

    static public function init($disable = false)
    {
        if ($disable || !extension_loaded('bcmath')) {
            self::$_bcmathDisabled = true;
            self::$add  = 'Zend_Locale_Math_Add';
            self::$sub  = 'Zend_Locale_Math_Sub';
            self::$pow  = 'Zend_Locale_Math_Pow';
            self::$mul  = 'Zend_Locale_Math_Mul';
            self::$div  = 'Zend_Locale_Math_Div';
            self::$comp = 'Zend_Locale_Math_Comp';
            self::$sqrt = 'Zend_Locale_Math_Sqrt';
            self::$mod  = 'Zend_Locale_Math_Mod';
        }
    }

    static public function isBcmathDisabled()
    {
        return self::$_bcmathDisabled;
    }
}

function Zend_Locale_Math_Add($op1, $op2)
{
    $result = $op1 + $op2;
    if ($result - $op2 != $op1) {
        throw Exception("addition overflow: $op1 + $op2 != $result");
    }
    return $result;
}

function Zend_Locale_Math_Sub($op1, $op2)
{
    $result = $op1 - $op2;
    if ($result + $op2 != $op1) {
        throw Exception("subtraction overflow: $op1 - $op2 != $result");
    }
    return $result;
}

function Zend_Locale_Math_Pow($base, $exp)
{
    $result = pow($base, $exp);
    if ($result === false) {
        throw Exception("power overflow: $op1 ^ $op2");
    }
    return $result;
} 

function Zend_Locale_Math_Mul($op1, $op2)
{
    $result = $op1 * $op2;
    if ($result / $op2 != $op1) {
        throw Exception("multiplication overflow: $op1 * $op2 != $result");
    }
    return $result;
}

function Zend_Locale_Math_Div($op1, $op2)
{
    $result = $op1 / $op2;
    if ($op2 == 0) {
        throw Exception("can not divide by zero");
    }
    if ($result * $op2 != $op1) {
        throw Exception("division overflow: $op1 / $op2 != $result");
    }
    return $result;
}

function Zend_Locale_Math_Comp($op1, $op2)
{
    $result = $op1 - $op2;
    if ($result + $op2 != $op1) {
        throw Exception("compare overflow: comp($op1, $op2)");
    }
    return $result;
}

function Zend_Locale_Math_Sqrt($op1, $op2 = null)
{
    if (((float)$op1) != $op1) {
        throw Exception("sqrt operand overflow: $op1");
    }
    $result = sqrt($op1);
    return $result;
}

function Zend_Locale_Math_Mod($op1, $op2)
{
    $result = $op1 / $op2;
    if ($op2 == 0) {
        throw Exception("can not modulo by zero");
    }
    if ($result * $op2 != $op1) {
        throw Exception("modulo overflow: $op1 % $op2");
    }
    $result = $op1 % $op2;
    return $result;
}

Zend_Locale_Math::init();

?>
