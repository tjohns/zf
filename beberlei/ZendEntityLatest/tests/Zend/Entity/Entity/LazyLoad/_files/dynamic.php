<?php

class Zend_Entity_LazyLoad_DynamicTestMetadata extends Zend_Entity_MetadataFactory_Testing
{
    public function __construct()
    {
        $entityDef = new Zend_Entity_Definition_Entity("MyFooZend_CacheEntity1", array(
                'proxyClass' => 'MyFooZend_CacheEntity1Proxy'
            ));
        $entityDef->addPrimaryKey("id");
        $this->addDefinition($entityDef);

        $entityDef = new Zend_Entity_Definition_Entity("MyFooZend_CacheEntity2", array(
                'proxyClass' => 'MyFooZend_CacheEntity2Proxy'
            ));
        $entityDef->addPrimaryKey("id");
        $this->addDefinition($entityDef);
    }
}

class MyFooZend_CacheEntity1
{
    protected $id;
}

class MyFooZend_CacheEntity2
{
    protected $id;
}