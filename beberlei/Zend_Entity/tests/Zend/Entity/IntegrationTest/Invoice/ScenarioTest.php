<?php

require_once dirname(__FILE__)."/../load_invoice.php";

abstract class Zend_Entity_ScenarioData_Invoice_ScenarioTest extends Zend_Test_PHPUnit_DatabaseTestCase
{
    /**
     *
     * @var Zend_Entity_Manager
     */
    protected $_entityManager = null;

    public function setUp()
    {
        parent::setUp();

        $path = dirname(__FILE__)."/Definition/";
        $dbAdapter = $this->getAdapter();
        $this->_entityManager = new Zend_Entity_Manager($dbAdapter, array('mappingsPath' => $path));
    }

    protected function getDataSet()
    {
        return $this->createFlatXMLDataSet(dirname(__FILE__).'/Fixtures/Example1.xml');
    }

    public function testFindCustomerById()
    {
        $customer = $this->_entityManager->load('Customer', 1);
        $this->assertEquals( "John Doe", $customer->getName() );
    }

    public function testCalculateTotalInvoiceAmount()
    {
        $invoiceId = 1;
        $invoice = $this->_entityManager->load('Invoice', $invoiceId);

        $customer = $invoice->getCustomer();
        $this->assertEquals("John Doe", $customer->getName());
        $this->assertEquals(2000, $invoice->calculateTotal());
    }
}