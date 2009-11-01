<?php

class Zend_Entity_LazyLoad_GeneratorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_Entity_LazyLoad_Generator
     */
    private $generator = null;

    public function setUp()
    {
        $this->generator = new Zend_Entity_LazyLoad_DynamicGenerator();
    }

    public function testUseEntityNameForProxyClass()
    {
        $def = Zend_Entity_Fixture_SimpleFixtureDefs::createClassADefinition();

        $c = $this->generator->generateLazyLoadProxyClass($def);

        $this->assertEquals("Zend_TestEntity1Proxy", $c->getName());
    }

    public function testProxyExtendsEntityClass()
    {
        $def = Zend_Entity_Fixture_SimpleFixtureDefs::createClassADefinition();

        $c = $this->generator->generateLazyLoadProxyClass($def);

        $this->assertEquals("Zend_TestEntity1", $c->getExtendedClass());
    }

    public function testProxyDocblookContainsGeneratedWarning()
    {
        $def = Zend_Entity_Fixture_SimpleFixtureDefs::createClassADefinition();

        $c = $this->generator->generateLazyLoadProxyClass($def);

        $this->assertEquals(
            "THIS CODE WAS AUTOMATICALLY CREATED AND MIGHT BE AUTOMATICALLY REGENERATED\nCHANGES TO THIS CODE CAN BE LOST!",
            $c->getDocblock()->getShortDescription()
        );
    }

    public function testProxyHasOwnConstructor()
    {
        $def = Zend_Entity_Fixture_SimpleFixtureDefs::createClassADefinition();

        $c = $this->generator->generateLazyLoadProxyClass($def);

        $this->assertTrue($c->hasMethod('__construct'));
    }

    public function testProxyConstructorRequiresEntityManagerEntityNameAndId()
    {
        $def = Zend_Entity_Fixture_SimpleFixtureDefs::createClassADefinition();

        $c = $this->generator->generateLazyLoadProxyClass($def);

        $constructorMethod = $c->getMethod('__construct');

        $params = $constructorMethod->getParameters();
        
        $this->assertEquals(3, count($params));
        $this->assertTrue(isset($params["entityManager"]));
        $this->assertEquals("Zend_Entity_Manager_Interface", $params["entityManager"]->getType());
        $this->assertTrue(isset($params['entityName']));
        $this->assertTrue(isset($params['id']));
    }

    public function testProxyConstructorIsPublic()
    {
        $def = Zend_Entity_Fixture_SimpleFixtureDefs::createClassADefinition();

        $c = $this->generator->generateLazyLoadProxyClass($def);

        $constructorMethod = $c->getMethod('__construct');
        $this->assertEquals(Zend_CodeGenerator_Php_Member_Abstract::VISIBILITY_PUBLIC, $constructorMethod->getVisibility());
    }

    public function testProxyHasEntityManagerProperty()
    {
        $def = Zend_Entity_Fixture_SimpleFixtureDefs::createClassADefinition();

        $c = $this->generator->generateLazyLoadProxyClass($def);

        $this->assertTrue($c->hasProperty('_entityManager'));
    }

    public function testConstructorBody_SavesEntityManagerIntoProperty()
    {
        $expectedBody = <<<COM
\$this->_entityManager = \$entityManager;
\$entityManager->getIdentityMap()->addObject(\$entityName, \$id, \$this);
COM;

        $def = Zend_Entity_Fixture_SimpleFixtureDefs::createClassADefinition();

        $c = $this->generator->generateLazyLoadProxyClass($def);

        $this->assertEquals($expectedBody, $c->getMethod('__construct')->getBody());
    }

    public function testConstructorBody_CallParentConstructorIfExists()
    {
        $simpleBody = <<<COM
\$this->_entityManager = \$entityManager;
\$entityManager->getIdentityMap()->addObject(\$entityName, \$id, \$this);
COM;

        $expectedBody = <<<COM
\$this->_entityManager = \$entityManager;
\$entityManager->getIdentityMap()->addObject(\$entityName, \$id, \$this);
parent::__construct();
COM;

        $def = Zend_Entity_Fixture_ManyToOneDefs::createClassBDefinition();

        $c = $this->generator->generateLazyLoadProxyClass($def);

        $this->assertNotEquals($simpleBody, $c->getMethod('__construct')->getBody());
        $this->assertEquals($expectedBody, $c->getMethod('__construct')->getBody());
    }

    public function testProxyHasPrivateLazyLoadMethod()
    {
        $def = Zend_Entity_Fixture_SimpleFixtureDefs::createClassADefinition();

        $c = $this->generator->generateLazyLoadProxyClass($def);

        $this->assertTrue($c->hasMethod('__lazyLoad'));
        $this->assertEquals(Zend_CodeGenerator_Php_Member_Abstract::VISIBILITY_PRIVATE, $c->getMethod('__lazyLoad')->getVisibility());
    }

    public function testLazyLoadMethod_RefreshesEntity_IfNotYetDone()
    {
        $expectedBody = <<<LLM
if(\$this->_entityManager !== null) {
    \$this->_entityManager->refresh(\$this);
    \$this->_entityManager = null;
}
LLM;

        $def = Zend_Entity_Fixture_SimpleFixtureDefs::createClassADefinition();

        $c = $this->generator->generateLazyLoadProxyClass($def);

        $this->assertEquals($expectedBody, $c->getMethod('__lazyLoad')->getBody());
    }

    public function testAllPublicMethodsOfGivenEntityAreOverwritten()
    {
        $def = Zend_Entity_Fixture_SimpleFixtureDefs::createClassADefinition();

        $c = $this->generator->generateLazyLoadProxyClass($def);

        $overwrittenMethods = array('__set', '__get', 'setState', 'getState', '__call');
        foreach($overwrittenMethods AS $methodName) {
            if(strtolower($methodName) == "setstate") {
                continue;
            }

            $this->assertTrue($c->hasMethod($methodName), "Method '".$methodName."' was not overwritten by Proxy!");
            $this->assertContains('$this->__lazyLoad();', $c->getMethod($methodName)->getBody());
        }
    }

    public function testEntityHasFinalConstructor_ThrowsException()
    {
        require_once dirname(__FILE__)."/_files/constructorfinal.php";

        $this->setExpectedException(
            "Zend_Entity_LazyLoad_GenerateProxyException"
        );

        $def = new Zend_Entity_Definition_Entity("Zend_ConstructorFinalEntity");
        $def->addPrimaryKey("id");

        $this->generator->generateLazyLoadProxyClass($def);
    }

    public function testEntityClassNotExists_ThrowsException()
    {
        $this->setExpectedException(
            "Zend_Entity_LazyLoad_GenerateProxyException"
        );

        $def = new Zend_Entity_Definition_Entity("MyZend_UnknownEntity");
        $def->addPrimaryKey("id");

        $this->generator->generateLazyLoadProxyClass($def);
    }

    public function testEntityHasFinalMethod_ThrowsException()
    {
        $def = Zend_Entity_Fixture_SimpleFixtureDefs::createClassADefinition();
        $def->setClass("Zend_TestEntityHasFinal");

        $this->setExpectedException(
            "Zend_Entity_LazyLoad_GenerateProxyException",
            "Method 'doSomething' is ".
            "not allowed to be final! No valid proxy can be generated!"
        );

        $c = $this->generator->generateLazyLoadProxyClass($def);
    }

    public function testAllGeneratedMethodsAreFinal()
    {
        $def = Zend_Entity_Fixture_SimpleFixtureDefs::createClassADefinition();

        $c = $this->generator->generateLazyLoadProxyClass($def);

        $methods = $c->getMethods();
        $this->assertTrue(count($methods)>0, "No methods were generated!");
        foreach($methods AS $method) {
            $this->assertTrue($this->readAttribute($method, '_isFinal'), "Method ".$method->getName()." is not final!");
        }
    }

    public function testLazyLoadProxyImplementsProxyInterface()
    {
        $def = Zend_Entity_Fixture_SimpleFixtureDefs::createClassADefinition();

        $c = $this->generator->generateLazyLoadProxyClass($def);

        $this->assertEquals(array('Zend_Entity_LazyLoad_Proxy'), $c->getImplementedInterfaces());
    }

    public function testStaticPublicMethodsAreNotProxied()
    {
        $def = Zend_Entity_Fixture_SimpleFixtureDefs::createClassADefinition();

        $c = $this->generator->generateLazyLoadProxyClass($def);

        $this->assertFalse($c->hasMethod('create'));
    }

    public function testLazyLoadProxy_HasInterfaceEntityWasLoadedMethod()
    {
        $expectedBody = <<<EWL
return (\$this->_entityManager===null);
EWL;

        $def = Zend_Entity_Fixture_SimpleFixtureDefs::createClassADefinition();

        $c = $this->generator->generateLazyLoadProxyClass($def);

        $methodName = 'entityWasLoaded';
        $this->assertTrue($c->hasMethod($methodName));
        $this->assertEquals(0, count($c->getMethod($methodName)->getParameters()));
        $this->assertEquals($expectedBody, $c->getMethod($methodName)->getBody());
    }

    public function testGeneratedZendTestEntity1ProxyPhpCode()
    {
        $def = Zend_Entity_Fixture_SimpleFixtureDefs::createClassADefinition();

        $c = $this->generator->generateLazyLoadProxyClass($def);
        $proxyCode = $c->generate();

        $expectedProxyCode = <<< EPC
/**
 * THIS CODE WAS AUTOMATICALLY CREATED AND MIGHT BE AUTOMATICALLY REGENERATED
 * CHANGES TO THIS CODE CAN BE LOST!
 */
class Zend_TestEntity1Proxy extends Zend_TestEntity1 implements Zend_Entity_LazyLoad_Proxy
{

    private \$_entityManager = null;

    final public function __construct(Zend_Entity_Manager_Interface \$entityManager, \$entityName, \$id)
    {
        \$this->_entityManager = \$entityManager;
        \$entityManager->getIdentityMap()->addObject(\$entityName, \$id, \$this);
    }

    final private function __lazyLoad()
    {
        if(\$this->_entityManager !== null) {
            \$this->_entityManager->refresh(\$this);
            \$this->_entityManager = null;
        }
    }

    final public function entityWasLoaded()
    {
        return (\$this->_entityManager===null);
    }

    final public function __set(\$name, \$value)
    {
        \$this->__lazyLoad();
        return parent::__set(\$name, \$value);
    }

    final public function __get(\$name)
    {
        \$this->__lazyLoad();
        return parent::__get(\$name);
    }

    final public function getState()
    {
        \$this->__lazyLoad();
        return parent::getState();
    }

    final public function __call(\$method, \$args)
    {
        \$this->__lazyLoad();
        return parent::__call(\$method, \$args);
    }


}

EPC;
        $this->assertEquals($expectedProxyCode, $proxyCode);
    }
}