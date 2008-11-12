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
 * @package    Zend_Soap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */
require_once dirname(__FILE__)."/../../../TestHelper.php";

/** PHPUnit Test Case */
require_once 'PHPUnit/Framework/TestCase.php';

/** Zend_Soap_Wsdl */
require_once 'Zend/Soap/Wsdl.php';

require_once 'Zend/Soap/Wsdl/Strategy/ArrayOfTypeComplex.php';

class Zend_Soap_Wsdl_ArrayOfTypeComplexStrategyTest extends PHPUnit_Framework_TestCase
{
    private $wsdl;
    private $strategy;

    public function setUp()
    {
        $this->strategy = new Zend_Soap_Wsdl_Strategy_ArrayOfTypeComplex();
        $this->wsdl = new Zend_Soap_Wsdl('MyService', 'http://localhost/MyService.php', $this->strategy);
    }

    public function testNestingObjectsDeepMakesNoSenseThrowingException()
    {
        try {
            $this->wsdl->addComplexType('Zend_Soap_Wsdl_ComplexTest[][]');
            $this->fail();
        } catch(Zend_Soap_Wsdl_Exception $e) {
            
        }
    }

    public function testAddComplexTypeOfNonExistingClassThrowsException()
    {
        try {
            $this->wsdl->addComplexType('Zend_Soap_Wsdl_UnknownClass[]');
        } catch(Zend_Soap_Wsdl_Exception $e) {

        }
    }

    public function testArrayOfSimpleObject()
    {
        $return = $this->wsdl->addComplexType('Zend_Soap_Wsdl_ComplexTest[]');
        $this->assertEquals("tns:ArrayOfZend_Soap_Wsdl_ComplexTest", $return);

        $wsdl = $this->wsdl->toXML();

        $this->assertContains(
            '<xsd:complexType name="ArrayOfZend_Soap_Wsdl_ComplexTest"><xsd:complexContent><xsd:restriction base="soapenc:Array"><xsd:attribute ref="soapenc:arrayType" wsdl:arrayType="typens:Zend_Soap_Wsdl_ComplexTest[]"/></xsd:restriction></xsd:complexContent></xsd:complexType>',
            $wsdl
        );

        $this->assertContains(
            '<xsd:complexType name="Zend_Soap_Wsdl_ComplexTest"><xsd:all><xsd:element name="var" type="xsd:int"/></xsd:all></xsd:complexType>',
            $wsdl
        );
    }

    public function testThatOverridingStrategyIsReset()
    {
        $return = $this->wsdl->addComplexType('Zend_Soap_Wsdl_ComplexTest[]');
        $this->assertEquals("tns:ArrayOfZend_Soap_Wsdl_ComplexTest", $return);
        #$this->assertTrue($this->wsdl->getComplexTypeStrategy() instanceof Zend_Soap_Wsdl_Strategy_ArrayOfTypeComplexStrategy);

        $wsdl = $this->wsdl->toXML();
    }

    public function testArrayOfComplexObjects()
    {
        $return = $this->wsdl->addComplexType('ComplexObjectStructure[]');
        $this->assertEquals("tns:ArrayOfComplexObjectStructure", $return);

        $wsdl = $this->wsdl->toXML();

        $this->assertContains(
            '<xsd:complexType name="ArrayOfComplexObjectStructure"><xsd:complexContent><xsd:restriction base="soapenc:Array"><xsd:attribute ref="soapenc:arrayType" wsdl:arrayType="typens:ComplexObjectStructure[]"/></xsd:restriction></xsd:complexContent></xsd:complexType>',
            $wsdl
        );

        $this->assertContains(
            '<xsd:complexType name="ComplexObjectStructure"><xsd:all><xsd:element name="boolean" type="xsd:boolean"/><xsd:element name="string" type="xsd:string"/><xsd:element name="int" type="xsd:int"/><xsd:element name="array" type="soap-enc:Array"/></xsd:all></xsd:complexType>',
            $wsdl
        );
    }

    public function testArrayOfObjectWithObject()
    {
        $return = $this->wsdl->addComplexType('ComplexObjectWithObjectStructure[]');
        $this->assertEquals("tns:ArrayOfComplexObjectWithObjectStructure", $return);

        $wsdl = $this->wsdl->toXML();

        $this->assertContains(
            '<xsd:complexType name="ArrayOfComplexObjectWithObjectStructure"><xsd:complexContent><xsd:restriction base="soapenc:Array"><xsd:attribute ref="soapenc:arrayType" wsdl:arrayType="typens:ComplexObjectWithObjectStructure[]"/></xsd:restriction></xsd:complexContent></xsd:complexType>',
            $wsdl
        );

        $this->assertContains(
            '<xsd:complexType name="ComplexObjectWithObjectStructure"><xsd:all><xsd:element name="object" type="tns:Zend_Soap_Wsdl_ComplexTest"/></xsd:all></xsd:complexType>',
            $wsdl
        );

        $this->assertContains(
            '<xsd:complexType name="Zend_Soap_Wsdl_ComplexTest"><xsd:all><xsd:element name="var" type="xsd:int"/></xsd:all></xsd:complexType>',
            $wsdl
        );
    }
}

class Zend_Soap_Wsdl_ComplexTest
{
    /**
     * @var int
     */
    public $var = 5;
}

class ComplexObjectStructure
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

class ComplexObjectWithObjectStructure
{
    /**
     * @var Zend_Soap_Wsdl_ComplexTest
     */
    public $object;
}