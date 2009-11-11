<?php

require_once dirname(__FILE__) . '/../../../../TestHelper.php';

require_once 'Zend/Service/Amazon/Authentication/V2.php';

require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Zend_Service_Amazon_Authentication_V2 test case.
 */
class V2Test extends PHPUnit_Framework_TestCase
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

        $this->Zend_Service_Amazon_Authentication_V2 = new Zend_Service_Amazon_Authentication_V2('0PN5J17HBGZHT7JJ3X82', 'uV3F3YluFJax1cknvbcGwgjvx4QpvB+leU8dUj2o', '2009-07-15');
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->Zend_Service_Amazon_Authentication_V2 = null;

        parent::tearDown();
    }

    /**
     * Tests Zend_Service_Amazon_Authentication_V2::generateSignature()
     */
    public function testGenerateEc2Signature()
    {
        $url = "https://ec2.amazonaws.com/";
        $params = array();
        $params['Action'] = "DescribeImages";
        $params['ImageId.1'] = "ami-2bb65342";
        $params['Timestamp'] = "2009-11-11T13:52:38Z";

        $ret = $this->Zend_Service_Amazon_Authentication_V2->generateSignature($url, $params);

        $this->assertEquals('xx+pg7xVQwvRKO5trKC8GyUGQ1QmNJSLbxOQb9Kse6Q=', $params['Signature']);
        $this->assertEquals(file_get_contents(dirname(__FILE__) . '/_files/ec2_v2_return.txt'), $ret);
    }

    public function testGenerateSqsGetSignature()
    {
        $url = "https://queue.amazonaws.com/770098461991/queue2";
        $params = array();
        $params['Action'] = "SetQueueAttributes";
        $params['Attribute.Name'] = "VisibilityTimeout";
        $params['Attribute.Value'] = "90";
        $params['Timestamp'] = "2009-11-11T13:52:38Z";

        $ret = $this->Zend_Service_Amazon_Authentication_V2->generateSignature($url, $params);

        $this->assertEquals('YSw7HXDqokM/A6DhLz8kG+sd+oD5eMjqx3a02A0+GkE=', $params['Signature']);
        $this->assertEquals(file_get_contents(dirname(__FILE__) . '/_files/sqs_v2_get_return.txt'), $ret);
    }

}

