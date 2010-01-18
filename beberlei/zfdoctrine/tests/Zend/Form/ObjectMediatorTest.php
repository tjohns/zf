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
 * @package    Form
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once APPLICATION_ROOT."/library/Zend/Form/ObjectMediator.php";

class Zend_Form_ObjectMediatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_Form
     */
    public $form;

    /**
     * @var Zend_Form_ObjectMediator
     */
    public $mediator;

    public function setUp()
    {
        $this->form = new Zend_Form();
        $this->mediator = new Zend_Form_ObjectMediator($this->form, 'Zend_Form_ObjectMediatorEntity');
    }

    public function testSetGetInstance()
    {
        $entity = new Zend_Form_ObjectMediatorEntity();
        $this->mediator->setInstance($entity);
        $this->assertSame($entity, $this->mediator->getInstance());
    }

    public function testSetInvalidInstanceThrowsException()
    {
        $this->setExpectedException('Zend_Form_Exception');

        $entity = new stdClass();
        $this->mediator->setInstance($entity);
    }

    public function testGetDefaultInstance()
    {
        $this->assertType('Zend_Form_ObjectMediatorEntity', $this->mediator->getInstance());
    }

    public function testPopulateForm_WithSimpleDataStructure()
    {
        $this->form->addElement('text', 'foo');
        $this->form->addElement('text', 'bar');

        $this->mediator->addField('foo')->addField('bar');

        $this->assertEquals('', $this->form->getElement('foo')->getValue());
        $this->assertEquals('', $this->form->getElement('bar')->getValue());

        $entity = new Zend_Form_ObjectMediatorEntity();
        $this->mediator->setInstance($entity)->populate();

        $this->assertEquals('foo', $this->form->getElement('foo')->getValue());
        $this->assertEquals('bar', $this->form->getElement('bar')->getValue());
    }

    public function testPopulateForm_FilterValue()
    {
        $filter = new Zend_Filter_StripTags();

        $this->form->addElement('text', 'foo');
        $this->mediator->addField('foo', array('populateFilters' => array($filter)));

        $entity = new Zend_Form_ObjectMediatorEntity();
        $entity->foo = '<p>foo</p>';
        $this->mediator->setInstance($entity)->populate();

        $this->assertEquals('foo', $this->form->getElement('foo')->getValue());
    }

    public function testPopulateMethodDoesNotExist_ThrowsException()
    {
        $this->setExpectedException('Zend_Form_Exception');

        $this->form->addElement('text', 'foo');
        $this->mediator->addField('foo', array('getMethod' => 'invalidGetFoo'));

        $entity = new Zend_Form_ObjectMediatorEntity();
        $this->mediator->setInstance($entity)->populate();
    }

    public function testIsValid_CallEntityValidationMethod()
    {
        $data = array('foo' => 1234);

        $this->form->addElement('text', 'foo');
        $this->mediator->addField('foo', array('validatorMethod' => 'validateFoo', 'validatorErrorMessage' => 'foofailure'));

        $entity = new Zend_Form_ObjectMediatorEntity();
        $this->assertFalse($this->mediator->setInstance($entity)->isValid($data));
        $this->assertEquals(array('foofailure'), $this->form->getElement('foo')->getErrorMessages());
    }

    public function testTransferValues()
    {
        $data = array('foo' => 1234, 'bar' => 'baz');

        $this->form->addElement('text', 'foo');
        $this->form->addElement('text', 'bar');

        $this->mediator->addField('foo')->addField('bar');

        $entity = new Zend_Form_ObjectMediatorEntity();
        $this->assertTrue($this->mediator->setInstance($entity)->isValid($data));

        $this->mediator->transferValues();

        $this->assertEquals(1234, $entity->foo);
        $this->assertEquals('baz', $entity->bar);
    }

    public function testTransferValues_WithFilterMethod()
    {
        $data = array('foo' => 1234);

        $this->form->addElement('text', 'foo');
        $this->mediator->addField('foo', array('filterMethod' => 'filterFoo'));

        $entity = new Zend_Form_ObjectMediatorEntity();
        $this->assertTrue($this->mediator->setInstance($entity)->isValid($data));

        $this->mediator->transferValues();

        $this->assertEquals(4321, $entity->foo);
    }
}

class Zend_Form_ObjectMediatorEntity
{
    public $foo = "foo";
    public $bar = "bar";
    public $baz = null;

    public function __construct()
    {
        $this->baz = new stdClass();
        $this->baz->id = 1;
    }

    public function getFoo() {
        return $this->foo;
    }

    public function validateFoo($foo) {
        return false;
    }

    public function setFoo($foo) {
        $this->foo = $foo;
    }

    public function filterFoo($foo) {
        return strrev($foo);
    }

    public function getBar() {
        return $this->bar;
    }

    public function setBar($bar) {
        $this->bar = $bar;
    }

    public function getBazId() {
        return $this->baz->id;
    }

    public function setBaz(stdClass $baz) {
        $this->baz = $baz;
    }
}