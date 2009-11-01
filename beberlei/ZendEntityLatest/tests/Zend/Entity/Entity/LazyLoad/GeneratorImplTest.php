<?php

class Zend_Entity_LazyLoad_GeneratorImplTest extends PHPUnit_Framework_TestCase
{
    public function testCreateFactoryDynamicFromArray()
    {
        $options = array('type' => 'dyNamiC');
        $proxyGenerator = Zend_Entity_LazyLoad_GeneratorAbstract::create($options);

        $this->assertType('Zend_Entity_LazyLoad_DynamicGenerator', $proxyGenerator);
    }

    public function testCreateFactoryDynamicFromZendConfig()
    {
        $options = array('type' => 'dyNamiC');
        $proxyGenerator = Zend_Entity_LazyLoad_GeneratorAbstract::create(new Zend_Config($options));

        $this->assertType('Zend_Entity_LazyLoad_DynamicGenerator', $proxyGenerator);
    }

    public function testCreateFactoryCacheFileFromArray()
    {
        $options = array('type' => 'CaCheFile', 'generatorOptions' => array('proxyTempFile' => '/tmp/Foo.php'));
        $proxyGenerator = Zend_Entity_LazyLoad_GeneratorAbstract::create($options);

        $this->assertType('Zend_Entity_LazyLoad_CacheFileGenerator', $proxyGenerator);
        $this->assertEquals('/tmp/Foo.php', $proxyGenerator->getProxyTempFile());
    }

    public function testCreateFactoryCacheFileFromZendConfig()
    {
        $options = array('type' => 'CaCheFile', 'generatorOptions' => array('proxyTempFile' => '/tmp/Foo.php'));
        $proxyGenerator = Zend_Entity_LazyLoad_GeneratorAbstract::create(new Zend_Config($options));

        $this->assertType('Zend_Entity_LazyLoad_CacheFileGenerator', $proxyGenerator);
        $this->assertEquals('/tmp/Foo.php', $proxyGenerator->getProxyTempFile());
    }

    public function testCreateFactoryFromClass()
    {
        $mock = $this->getMock('Zend_Entity_LazyLoad_GeneratorAbstract');

        $classes = array(
            'Zend_Entity_LazyLoad_CacheFileGenerator',
            'Zend_Entity_LazyLoad_DynamicGenerator',
            get_class($mock),
        );

        foreach($classes AS $class) {
            $options = array('class' => $class);

            $proxyGenerator = Zend_Entity_LazyLoad_GeneratorAbstract::create(new Zend_Config($options));
            $this->assertType($class, $proxyGenerator);
        }
    }

    public function testCreateWithInvalidType_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_LazyLoad_GenerateProxyException");

        Zend_Entity_LazyLoad_GeneratorAbstract::create(array('type' => 'Foo'));
    }

    public function testCreateWithoutTypeOrClassOption_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_LazyLoad_GenerateProxyException");

        Zend_Entity_LazyLoad_GeneratorAbstract::create(array());
    }

    public function testCreateWithUnknownClass_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_LazyLoad_GenerateProxyException");

        Zend_Entity_LazyLoad_GeneratorAbstract::create(array('class' => 'MyFooProxyGenerator'));
    }

    public function testCreateWithClassNotImplementingAbstractGenerator_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_LazyLoad_GenerateProxyException");

        Zend_Entity_LazyLoad_GeneratorAbstract::create(array('class' => 'stdClass'));
    }

    public function testCreateDynamicProxies()
    {
        require_once dirname(__FILE__)."/_files/dynamic.php";

        $proxyGenerator = Zend_Entity_LazyLoad_GeneratorAbstract::create(array('type' => 'dynamic'));

        $this->assertFalse(class_exists('MyFooZend_CacheEntity1Proxy'), "Proxy for Entity 1 should not exist before generation!");
        $this->assertFalse(class_exists('MyFooZend_CacheEntity2Proxy'), "Proxy for Entity 2 should not exist before generation!");

        $metadata = new Zend_Entity_LazyLoad_DynamicTestMetadata();
        $metadata->visit($proxyGenerator);

        $proxyGenerator->generate();

        $this->assertTrue(class_exists('MyFooZend_CacheEntity1Proxy'), "Proxy for Entity 1 should exist after generation!");
        $this->assertTrue(class_exists('MyFooZend_CacheEntity2Proxy'), "Proxy for Entity 2 should exist after generation!");

        $proxyGenerator->generate();
    }

    public function testCreateCacheFileProxies_NoTemporaryFile_ThrowsException()
    {
        require_once dirname(__FILE__)."/_files/dynamic.php";

        $this->setExpectedException("Zend_Entity_LazyLoad_GenerateProxyException");

        $proxyGenerator = Zend_Entity_LazyLoad_GeneratorAbstract::create(array('type' => 'cachefile'));

        $metadata = new Zend_Entity_LazyLoad_DynamicTestMetadata();
        $metadata->visit($proxyGenerator);

        $proxyGenerator->generate();
    }

    public function testCreateCacheFileProxies_TemporaryFileNotWriteable_ThrowsException()
    {
        require_once dirname(__FILE__)."/_files/dynamic.php";

        $this->setExpectedException("Zend_Entity_LazyLoad_GenerateProxyException");

        $proxyGenerator = Zend_Entity_LazyLoad_GeneratorAbstract::create(array('type' => 'cachefile', array(
                'generatorOptions' => array(
                    'proxyTempFile' => '/tmp/Foo/Foo.php'
                    )
                )
            )
        );

        $metadata = new Zend_Entity_LazyLoad_DynamicTestMetadata();
        $metadata->visit($proxyGenerator);

        $proxyGenerator->generate();
    }

    public function testCreateCacheFileProxies()
    {
        require_once dirname(__FILE__)."/_files/cachefile.php";

        $tempFile = sys_get_temp_dir()."/MyZendFooCacheFile.php";
        $hashFile = $tempFile.".hash";

        if(file_exists($tempFile)) {
            unlink($tempFile);
        }
        if(file_exists($hashFile)) {
            unlink($hashFile);
        }

        $proxyGenerator = Zend_Entity_LazyLoad_GeneratorAbstract::create(array(
            'type' => 'cachefile',
            'generatorOptions' => array(
                'proxyTempFile' => $tempFile
                )
            )
        );

        $this->assertEquals($tempFile, $proxyGenerator->getProxyTempFile());


        $this->assertFalse(class_exists('MyFooZend_CacheFileEntity1Proxy'), "Proxy for Entity 1 should not exist before generation!");
        $this->assertFalse(class_exists('MyFooZend_CacheFileEntity2Proxy'), "Proxy for Entity 2 should not exist before generation!");

        $metadata = new Zend_Entity_LazyLoad_CacheFileTestMetadata();
        $metadata->visit($proxyGenerator);

        $proxyGenerator->generate();

        $this->assertTrue(class_exists('MyFooZend_CacheFileEntity1Proxy'), "Proxy for Entity 1 should exist after generation!");
        $this->assertTrue(class_exists('MyFooZend_CacheFileEntity2Proxy'), "Proxy for Entity 2 should exist after generation!");
    }

    public function testInstantiate()
    {
        $this->markTestIncomplete();
    }
}