<?php

class Zend_Soap_Wsdl_ComplexTypeB
{
    /**
     * @var string
     */
    public $bar;
    /**
     * @var string
     */
    public $foo;
}


class Zend_Soap_Wsdl_ComplexTypeA
{
    /**
     * @var Zend_Soap_Wsdl_ComplexTypeB[]
     */
    public $baz = array();
}

class Zend_Soap_Wsdl_ComplexTest
{
    /**
     * @var int
     */
    public $var = 5;
}

class Zend_Soap_Wsdl_ComplexObjectStructure
{
    /**
     * @var boolean
     */
    public $boolean = true;

    /**
     * @var string
     */
    public $string = "Hello World";

    /**
     * @var int
     */
    public $int = 10;

    /**
     * @var array
     */
    public $array = array(1, 2, 3);
}

class Zend_Soap_Wsdl_ComplexObjectWithObjectStructure
{
    /**
     * @var Zend_Soap_Wsdl_ComplexTest
     */
    public $object;
}

class Zend_Soap_AutoDiscover_MyService
{
    /**
     *	@param string $foo
     *	@return Zend_Soap_AutoDiscover_MyResponse[]
     */
    public function foo($foo) {
    }
    /**
     *	@param string $bar
     *	@return Zend_Soap_AutoDiscover_MyResponse[]
     */
    public function bar($bar) {
    }

    /**
     *	@param string $baz
     *	@return Zend_Soap_AutoDiscover_MyResponse[]
     */
    public function baz($baz) {
    }
}

class Zend_Soap_AutoDiscover_MyServiceSequence
{
    /**
     *	@param string $foo
     *	@return string[]
     */
    public function foo($foo) {
    }
    /**
     *	@param string $bar
     *	@return string[]
     */
    public function bar($bar) {
    }

    /**
     *	@param string $baz
     *	@return string[]
     */
    public function baz($baz) {
    }

    /**
     *	@param string $baz
     *	@return string[][][]
     */
    public function bazNested($baz) {
    }
}

class Zend_Soap_AutoDiscover_MyResponse
{
    /**
     * @var string
     */
    public $p1;
}

class Zend_Soap_AutoDiscover_Recursion
{
    /**
     * @var Zend_Soap_AutoDiscover_Recursion
     */
    public $recursion;
}