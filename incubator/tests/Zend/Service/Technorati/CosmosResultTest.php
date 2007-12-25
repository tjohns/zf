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
 * @package    Zend_Service_Technorati
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id: TagsResultTest.php 7253 2007-12-24 13:34:35Z weppos $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Test helper
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR .'TechnoratiTestHelper.php';

/**
 * @see Zend_Service_Technorati_CosmosResult
 */
require_once 'Zend/Service/Technorati/CosmosResult.php';


/**
 * @category   Zend
 * @package    Zend_Service_Technorati
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Technorati_CosmosResultTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->domElements = Zend_Service_Technorati_TechnoratiTestHelper::getTestFileElementsAsDom('TestCosmosResultSet.xml');
    }

    public function testConstruct()
    {
        try {
            $object = new Zend_Service_Technorati_CosmosResult($this->domElements->item(0));
            $this->assertType('Zend_Service_Technorati_CosmosResult', $object);
        } catch (Exception $e) {
            $this->fail("Exception" . $e->getMessage() . " thrown");
        }
    }

    public function testConstructThrowsExceptionWithInvalidDom() 
    {
        try {
            $object = new Zend_Service_Technorati_CosmosResult('foo');
            $this->fail('Expected Zend_Service_Technorati_Exception not thrown');
        } catch (Exception $e) {
            $this->assertContains("DOMElement", $e->getMessage());
        }
    }

    public function testSearchResultSiteLink()
    {
        $domElements = Zend_Service_Technorati_TechnoratiTestHelper::getTestFileElementsAsDom('TestCosmosResultSetSiteLink.xml');
        $object = new Zend_Service_Technorati_CosmosResult($domElements->item(0));
        
        $this->assertType('Zend_Service_Technorati_Weblog', $object->getWeblog());
        $this->assertContains('Gioxx', $object->getWeblog()->getName());
        
        $this->assertType('string', $object->getNearestPermalink());
        $this->assertEquals('http://gioxx.org/2007/11/05/il-passaggio-a-mac-le-11-risposte/', $object->getNearestPermalink());
        
        $this->assertType('string', $object->getExcerpt());
        $this->assertContains('Ho intenzione di prendere il modello bianco', $object->getExcerpt());
        
        $this->assertType('string', $object->getLinkCreated());
        $this->assertEquals('2007-11-11 20:07:11 GMT', $object->getLinkCreated());
        
        $this->assertType('string', $object->getLinkUrl());
        $this->assertEquals('http://www.simonecarletti.com/blog/2007/04/parallels-desktop-overview.php', $object->getLinkUrl());
        
        // test an other element to prevent cached values
        $object = new Zend_Service_Technorati_CosmosResult($domElements->item(1));
        $this->assertContains('Progetto-Seo', $object->getWeblog()->getName());
        $this->assertEquals('http://www.progetto-seo.com/motori-di-ricerca/links-interni', $object->getNearestPermalink());
        $this->assertContains('soprattutto Google', $object->getExcerpt());
        $this->assertEquals('2007-11-10 08:57:22 GMT', $object->getLinkCreated());
        $this->assertEquals('http://www.simonecarletti.com/blog/2007/04/google-yahoo-ask-nofollow.php', $object->getLinkUrl());
    }

    public function testSearchResultSiteLinkNearestPermalinkIsNull()
    {
        $domElements = Zend_Service_Technorati_TechnoratiTestHelper::getTestFileElementsAsDom('TestCosmosResultSetSiteLink.xml');
        $object = new Zend_Service_Technorati_CosmosResult($domElements->item(2));
        $this->assertContains('Controrete', $object->getWeblog()->getName());
        $this->assertEquals(null, $object->getNearestPermalink());
    }
    
    public function testSearchResultSiteWeblog()
    {
        $domElements = Zend_Service_Technorati_TechnoratiTestHelper::getTestFileElementsAsDom('TestCosmosResultSetSiteWeblog.xml');
        $object = new Zend_Service_Technorati_CosmosResult($domElements->item(0));
        
        $this->assertType('Zend_Service_Technorati_Weblog', $object->getWeblog());
        $this->assertContains('Simone Carletti', $object->getWeblog()->getName());
        
        $this->assertType('string', $object->getLinkUrl());
        $this->assertEquals('http://www.simonecarletti.com', $object->getLinkUrl());
        
        // test an other element to prevent cached values
        $object = new Zend_Service_Technorati_CosmosResult($domElements->item(1));
        $this->assertContains('Gioxx', $object->getWeblog()->getName());
        $this->assertEquals('http://www.simonecarletti.com', $object->getLinkUrl());
    }
    
    public function testSearchResultBlogLink()
    {
        // same as testSearchResultSiteLink
    }
        
    public function testSearchResultBlogWeblog()
    {
        // same as testSearchResultSiteWeblog
    }
}
