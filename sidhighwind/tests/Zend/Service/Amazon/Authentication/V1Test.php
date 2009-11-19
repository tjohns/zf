<?php

require_once dirname(__FILE__) . '/../../../../TestHelper.php';

require_once 'Zend/Service/Amazon/Authentication/V1.php';

require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Zend_Service_Amazon_Authentication_V2 test case.
 */
class V1Test extends PHPUnit_Framework_TestCase
{

    /**
     * @var Zend_Service_Amazon_Authentication_V2
     */
    private $Zend_Service_Amazon_Authentication_V2;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->Zend_Service_Amazon_Authentication_V1 = new Zend_Service_Amazon_Authentication_V1('0PN5J17HBGZHT7JJ3X82', 'uV3F3YluFJax1cknvbcGwgjvx4QpvB+leU8dUj2o', '2007-12-01');
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->Zend_Service_Amazon_Authentication_V1 = null;

        parent::tearDown();
    }

    /**
     * Tests Zend_Service_Amazon_Authentication_V2::generateSignature()
     */
    public function testGenerateDevPaySignature()
    {
        $url = "https://ls.amazonaws.com/";
        $params = array();
        $params['Action'] = "ActivateHostedProduct";
        $params['Timestamp'] = "2009-11-11T13:52:38Z";

        $ret = $this->Zend_Service_Amazon_Authentication_V1->generateSignature($url, $params);

        $this->assertEquals('31Q2YlgABM5X3GkYQpGErcL10Xc=', $params['Signature']);
        $this->assertEquals("ActionActivateHostedProductAWSAccessKeyId0PN5J17HBGZHT7JJ3X82SignatureVersion1Timestamp2009-11-11T13:52:38ZVersion2007-12-01", $ret);
    }

}

