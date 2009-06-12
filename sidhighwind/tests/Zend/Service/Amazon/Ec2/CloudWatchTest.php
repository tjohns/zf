<?php

require_once 'library/Zend/Service/Amazon/Ec2/CloudWatch.php';

require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Zend_Service_Amazon_Ec2_CloudWatch test case.
 */
class Zend_Service_Amazon_Ec2_CloudWatchTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Zend_Service_Amazon_Ec2_CloudWatch
     */
    private $Zend_Service_Amazon_Ec2_CloudWatch;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();

        // TODO Auto-generated Zend_Service_Amazon_Ec2_CloudWatchTest::setUp()


        $this->Zend_Service_Amazon_Ec2_CloudWatch = new Zend_Service_Amazon_Ec2_CloudWatch('access_key', 'secret_access_key');

    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        // TODO Auto-generated Zend_Service_Amazon_Ec2_CloudWatchTest::tearDown()


        $this->Zend_Service_Amazon_Ec2_CloudWatch = null;

        parent::tearDown();
    }

    /**
     * Constructs the test case.
     */
    public function __construct()
    {
        // TODO Auto-generated constructor
    }

    /**
     * Tests Zend_Service_Amazon_Ec2_CloudWatch->getMetricStatistics()
     */
    public function testGetMetricStatistics()
    {
        // TODO Auto-generated Zend_Service_Amazon_Ec2_CloudWatchTest->testGetMetricStatistics()
        $this->markTestIncomplete("getMetricStatistics test not implemented");

        $this->Zend_Service_Amazon_Ec2_CloudWatch->getMetricStatistics(/* parameters */);

    }

    /**
     * Tests Zend_Service_Amazon_Ec2_CloudWatch->listMetrics()
     */
    public function testListMetrics()
    {
        // TODO Auto-generated Zend_Service_Amazon_Ec2_CloudWatchTest->testListMetrics()
        $this->markTestIncomplete("listMetrics test not implemented");

        $this->Zend_Service_Amazon_Ec2_CloudWatch->listMetrics(/* parameters */);

    }

}

