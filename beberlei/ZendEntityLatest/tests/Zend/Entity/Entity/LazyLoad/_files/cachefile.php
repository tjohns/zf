<?php

class Zend_Entity_LazyLoad_CacheFileTestMetadata extends Zend_Entity_MetadataFactory_Testing
{
    public function __construct()
    {
        $entityDef = new Zend_Entity_Definition_Entity("MyFooZend_CacheFileEntity1", array(
                'proxyClass' => 'MyFooZend_CacheFileEntity1Proxy'
            ));
        $entityDef->addPrimaryKey("id");
        $this->addDefinition($entityDef);

        $entityDef = new Zend_Entity_Definition_Entity("MyFooZend_CacheFileEntity2", array(
                'proxyClass' => 'MyFooZend_CacheFileEntity2Proxy'
            ));
        $entityDef->addPrimaryKey("id");
        $this->addDefinition($entityDef);
    }
}

class MyFooZend_CacheFileEntity1
{
    protected $id;
}

class MyFooZend_CacheFileEntity2
{
    protected $id;
}