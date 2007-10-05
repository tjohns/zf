<?php

require_once 'Zend/Crypt/Math/BigInteger/Bcmath.php';
require_once 'PHPUnit/Framework/TestCase.php';

class Zend_Crypt_Math_BigInteger_BcmathTest extends PHPUnit_Framework_TestCase 
{

    private $_math = null;

    public function setUp()
    {
        $this->_math = new Zend_Crypt_Math_BigInteger_Bcmath;
    }

    public function testAdd()
    {
        $this->assertEquals('2', $this->_math->add(1,1));
    }

    public function testSubtract()
    {
        $this->assertEquals('-2', $this->_math->subtract(2,4));
    }

    public function testCompare()
    {
        $this->assertEquals('0', $this->_math->compare(2,2));
        $this->assertEquals('-1', $this->_math->compare(2,4));
        $this->assertEquals('1', $this->_math->compare(4,2));
    }

    public function testDivide()
    {
        $this->assertEquals('2', $this->_math->divide(4,2));
        $this->assertEquals('2', $this->_math->divide(9,4));
    }

    public function testModulus()
    {
        $this->assertEquals('1', $this->_math->modulus(3,2));
    }

    public function testMultiply()
    {
        $this->assertEquals('4', $this->_math->multiply(2,2));
    }

    public function testPow()
    {
        $this->assertEquals('4', $this->_math->pow(2,2));
    }

    public function testPowMod()
    {
        $this->assertEquals('1', $this->_math->powmod(2,2,3));
    }

    public function testSqrt()
    {
        $this->assertEquals('2', $this->_math->sqrt(4));
    }

}