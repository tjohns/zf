<?php

class Zend_Entity_MetadataFactory_CacheTest extends PHPUnit_Framework_TestCase
{
    public function testGetDefinitionByEntityName_CacheMiss_Delegates()
    {
        $fixtureReturnValue = "foo";
        $fixtureEntityName = 'ZendTestEntity';

        $delegateMetadataFactory = $this->createDelegateMetdataFactory('getDefinitionByEntityName', $fixtureReturnValue, 1);
        $cacheMock = $this->createCacheMock(false, $fixtureEntityName, $fixtureReturnValue);

        $cacheMetadataFactory = new Zend_Entity_MetadataFactory_Cache($delegateMetadataFactory, $cacheMock);

        $ret = $cacheMetadataFactory->getDefinitionByEntityName($fixtureEntityName);

        $this->assertEquals($fixtureReturnValue, $ret);
    }

    public function testGetDefinitionByEntityName_WithPrefix_CacheMiss_Delegates()
    {
        $fixtureReturnValue = "foo";
        $fixtureEntityName = 'ZendTestEntity';

        $delegateMetadataFactory = $this->createDelegateMetdataFactory('getDefinitionByEntityName', $fixtureReturnValue, 1);
        $cacheMock = $this->createCacheMock(false, "prefix_".$fixtureEntityName, $fixtureReturnValue);

        $cacheMetadataFactory = new Zend_Entity_MetadataFactory_Cache($delegateMetadataFactory, $cacheMock, "prefix_");

        $ret = $cacheMetadataFactory->getDefinitionByEntityName($fixtureEntityName);

        $this->assertEquals($fixtureReturnValue, $ret);
    }


    public function testGetDefinitionByEntityName_CacheHit_DoesNotDelegate()
    {
        $fixtureReturnValue = "foo";
        $fixtureEntityName = 'ZendTestEntity';

        $delegateMetadataFactory = $this->createDelegateMetdataFactory('getDefinitionByEntityName', $fixtureReturnValue, 0);
        $cacheMock = $this->createCacheMock(true, $fixtureEntityName, $fixtureReturnValue);

        $cacheMetadataFactory = new Zend_Entity_MetadataFactory_Cache($delegateMetadataFactory, $cacheMock);

        $ret = $cacheMetadataFactory->getDefinitionByEntityName($fixtureEntityName);

        $this->assertEquals($fixtureReturnValue, $ret);
    }

    public function testGetDefinitionEntityNames_CacheMiss_Delegates()
    {
        $fixtureReturnValue = "foo";

        $delegateMetadataFactory = $this->createDelegateMetdataFactory('getDefinitionEntityNames', $fixtureReturnValue, 1);
        $cacheMock = $this->createCacheMock(false, "ze__entityDefinitionNames", $fixtureReturnValue);

        $cacheMetadataFactory = new Zend_Entity_MetadataFactory_Cache($delegateMetadataFactory, $cacheMock);

        $ret = $cacheMetadataFactory->getDefinitionEntityNames();

        $this->assertEquals($fixtureReturnValue, $ret);
    }

    public function testGetDefinitionEntityNames_WithPrefix_CacheMiss_Delegates()
    {
        $fixtureReturnValue = "foo";

        $delegateMetadataFactory = $this->createDelegateMetdataFactory('getDefinitionEntityNames', $fixtureReturnValue, 1);
        $cacheMock = $this->createCacheMock(false, "prefix_ze__entityDefinitionNames", $fixtureReturnValue);

        $cacheMetadataFactory = new Zend_Entity_MetadataFactory_Cache($delegateMetadataFactory, $cacheMock, "prefix_");

        $ret = $cacheMetadataFactory->getDefinitionEntityNames();

        $this->assertEquals($fixtureReturnValue, $ret);
    }

    public function testGetDefinitionEntityNames_CacheHit_DoesNotDelegate()
    {
        $fixtureReturnValue = "foo";

        $delegateMetadataFactory = $this->createDelegateMetdataFactory('getDefinitionEntityNames', $fixtureReturnValue, 0);
        $cacheMock = $this->createCacheMock(true, "ze__entityDefinitionNames", $fixtureReturnValue);

        $cacheMetadataFactory = new Zend_Entity_MetadataFactory_Cache($delegateMetadataFactory, $cacheMock);

        $ret = $cacheMetadataFactory->getDefinitionEntityNames();

        $this->assertEquals($fixtureReturnValue, $ret);
    }

    public function createDelegateMetdataFactory($functionName, $fixtureReturnValue, $expectedCount=1)
    {
        $delegateMetadataFactory = $this->getMock('Zend_Entity_MetadataFactory_Interface');
        $delegateMetadataFactory->expects($this->exactly($expectedCount))
                                ->method($functionName)
                                ->will($this->returnValue($fixtureReturnValue));
        return $delegateMetadataFactory;
    }

    public function createCacheMock($cacheHit, $expectedCacheHash, $expectedCacheSaveValue)
    {
        $cacheMock = $this->getMock('Zend_Cache_Core', array(), array(), '', false);
        $cacheMock->expects($this->at(0))
                  ->method('load')
                  ->with($this->equalTo($expectedCacheHash))
                  ->will($this->returnValue($cacheHit));
        if($cacheHit == false) {
            $cacheMock->expects($this->at(1))
                      ->method('save')
                      ->with($this->equalTo($expectedCacheSaveValue), $this->equalTo($expectedCacheHash));
        }
        return $cacheMock;
    }
}