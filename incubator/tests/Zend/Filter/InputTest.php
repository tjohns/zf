<?php

require_once 'Zend/Filter/Input.php';
require_once 'PHPUnit/Framework/TestCase.php';

class Zend_Filter_InputTest extends PHPUnit_Framework_TestCase
{

    public function testFilterScalar()
    {
        $data = array('month' => '6abc ');
        $filters = array('month' => 'digits');
        $validators = array('month' => array());
        $input = new Zend_Filter_Input($filters, $validators, $data);
        $month = $input->month;
        $this->assertEquals('6', $month);
    }

    public function testFilterArray()
    {
        // to be written
    }

    public function testFilterObject()
    {
        // to be written
    }

    public function testValidatorScalar()
    {
        $data = array('month' => '6');
        $filters = array('month' => array());
        $validators = array('month' => 'digits');
        $input = new Zend_Filter_Input($filters, $validators, $data);
        $month = $input->month;
        $this->assertEquals('6', $month);
    }

    public function testValidatorScalarInvalid()
    {
        $data = array('month' => '6abc ');
        $filters = array('month' => array());
        $validators = array('month' => 'digits');
        $input = new Zend_Filter_Input($filters, $validators, $data);
        $invalid = $input->getInvalid();
        $msg = $invalid['month'][0];
        $this->assertEquals("'6abc ' contains not only digit characters", $msg);
    }

    public function testValidatorArray()
    {
        // to be written
    }

    public function testValidatorArrayInvalid()
    {
        // to be written
    }

    public function testValidatorObject()
    {
        // to be written
    }

    public function testValidatorObjectInvalid()
    {
        // to be written
    }

    public function testValidatorHasMissing()
    {
        // to be written
    }

    public function testValidatorGetMissing()
    {
        // to be written
    }

    public function testValidatorHasUnknown()
    {
        // to be written
    }

    public function testValidatorGetUnknown()
    {
        // to be written
    }

    public function testSetOptionNamespace()
    {
        // to be written
    }

    public function testSetOptionDefaultEscapeFilter()
    {
        // to be written
    }

    public function testAddNamespace()
    {
        // to be written
    }

    public function testGetEscaped()
    {
    }

    public function testMagicGetEscaped()
    {
        // to be written
    }

    public function testGetUnescaped()
    {
        // to be written
    }

}
