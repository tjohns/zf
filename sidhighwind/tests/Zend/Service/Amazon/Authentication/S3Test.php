<?php

require_once dirname(__FILE__) . '/../../../../TestHelper.php';

require_once 'Zend/Service/Amazon/Authentication/S3.php';

require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Zend_Service_Amazon_Authentication_S3 test case.
 */
class Zend_Service_Amazon_Authentication_S3Test extends PHPUnit_Framework_TestCase
{
    
    /**
     * @var Zend_Service_Amazon_Authentication_S3
     */
    private $Zend_Service_Amazon_Authentication_S3;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        
        // TODO Auto-generated Zend_Service_Amazon_Authentication_S3Test::setUp()
        

        $this->Zend_Service_Amazon_Authentication_S3 = new Zend_Service_Amazon_Authentication_S3('0PN5J17HBGZHT7JJ3X82', 'uV3F3YluFJax1cknvbcGwgjvx4QpvB+leU8dUj2o', '2006-03-01');
    
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        // TODO Auto-generated Zend_Service_Amazon_Authentication_S3Test::tearDown()
        

        $this->Zend_Service_Amazon_Authentication_S3 = null;
        
        parent::tearDown();
    }

    
    public function testGetGeneratesCorrectSignature()
    {
        $headers = array();
        $headers['Date'] = "Tue, 27 Mar 2007 19:36:42 +0000";
        
        $ret = $this->Zend_Service_Amazon_Authentication_S3->generateSignature('GET', 'http://s3.amazonaws.com/johnsmith/photos/puppy.jpg', $headers);

        $this->assertEquals('AWS 0PN5J17HBGZHT7JJ3X82:xXjDGYUmKxnwqr5KXNPGldn5LbA=', $headers['Authorization']);
        $this->assertEquals($ret, "GET


Tue, 27 Mar 2007 19:36:42 +0000
/johnsmith/photos/puppy.jpg");
    }
    
    public function testPutGeneratesCorrectSignature()
    {
        $headers = array();
        $headers['Date'] = "Tue, 27 Mar 2007 21:15:45 +0000";
        $headers['Content-Type'] = "image/jpeg";
        $headers['Content-Length'] = 94328;
        
        $ret = $this->Zend_Service_Amazon_Authentication_S3->generateSignature('PUT', 'http://s3.amazonaws.com/johnsmith/photos/puppy.jpg', $headers);

        $this->assertEquals('AWS 0PN5J17HBGZHT7JJ3X82:hcicpDDvL9SsO6AkvxqmIWkmOuQ=', $headers['Authorization']);
        $this->assertEquals($ret, "PUT

image/jpeg
Tue, 27 Mar 2007 21:15:45 +0000
/johnsmith/photos/puppy.jpg");
    }
    
    public function testListGeneratesCorrectSignature()
    {
        $headers = array();
        $headers['Date'] = "Tue, 27 Mar 2007 19:42:41 +0000";
        
        $ret = $this->Zend_Service_Amazon_Authentication_S3->generateSignature('GET', 'http://s3.amazonaws.com/johnsmith/?prefix=photos&max-keys=50&marker=puppy', $headers);

        $this->assertEquals('AWS 0PN5J17HBGZHT7JJ3X82:jsRt/rhG+Vtp88HrYL706QhE4w4=', $headers['Authorization']);
        $this->assertEquals($ret, "GET


Tue, 27 Mar 2007 19:42:41 +0000
/johnsmith/");
    }
    
    public function testFetchGeneratesCorrectSignature()
    {
        $headers = array();
        $headers['Date'] = "Tue, 27 Mar 2007 19:44:46 +0000";
        
        $ret = $this->Zend_Service_Amazon_Authentication_S3->generateSignature('GET', 'http://s3.amazonaws.com/johnsmith/?acl', $headers);

        $this->assertEquals('AWS 0PN5J17HBGZHT7JJ3X82:thdUi9VAkzhkniLj96JIrOPGi0g=', $headers['Authorization']);
        $this->assertEquals($ret, "GET


Tue, 27 Mar 2007 19:44:46 +0000
/johnsmith/?acl");
    }
    
    public function testDeleteGeneratesCorrectSignature()
    {
        
        $headers = array();
        $headers['x-amz-date'] = "Tue, 27 Mar 2007 21:20:26 +0000";
        
        $ret = $this->Zend_Service_Amazon_Authentication_S3->generateSignature('DELETE', 'http://s3.amazonaws.com/johnsmith/photos/puppy.jpg', $headers);
        
        $this->assertEquals($headers['Authorization'], 'AWS 0PN5J17HBGZHT7JJ3X82:k3nL7gH3+PadhTEVn5Ip83xlYzk=');
        $this->assertEquals($ret, "DELETE



x-amz-date:Tue, 27 Mar 2007 21:20:26 +0000
/johnsmith/photos/puppy.jpg");
    }
    
    public function testUploadGeneratesCorrectSignature()
    {
        $headers = array();
        $headers['Date'] = "Tue, 27 Mar 2007 21:06:08 +0000";
        $headers['x-amz-acl'] = "public-read";
        $headers['content-type'] = "application/x-download";
        $headers['Content-MD5'] = "4gJE4saaMU4BqNR0kLY+lw==";
        $headers['X-Amz-Meta-ReviewedBy'][] = "joe@johnsmith.net";
        $headers['X-Amz-Meta-ReviewedBy'][] = "jane@johnsmith.net";
        $headers['X-Amz-Meta-FileChecksum'] = "0x02661779";
        $headers['X-Amz-Meta-ChecksumAlgorithm'] = "crc32";
        $headers['Content-Disposition'] = "attachment; filename=database.dat";
        $headers['Content-Encoding'] = "gzip";
        $headers['Content-Length'] = "5913339";
        
        
        $ret = $this->Zend_Service_Amazon_Authentication_S3->generateSignature('PUT', 'http://s3.amazonaws.com/static.johnsmith.net/db-backup.dat.gz', $headers);
        
        $this->assertEquals($headers['Authorization'], 'AWS 0PN5J17HBGZHT7JJ3X82:C0FlOtU8Ylb9KDTpZqYkZPX91iI=');
        $this->assertEquals($ret, "PUT
4gJE4saaMU4BqNR0kLY+lw==
application/x-download
Tue, 27 Mar 2007 21:06:08 +0000
x-amz-acl:public-read
x-amz-meta-checksumalgorithm:crc32
x-amz-meta-filechecksum:0x02661779
x-amz-meta-reviewedby:joe@johnsmith.net,jane@johnsmith.net
/static.johnsmith.net/db-backup.dat.gz");
    }

}

