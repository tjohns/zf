<?php
require_once dirname(__FILE__) . '/TestAbstract.php';
require_once 'Zend/View/Helper/Navigation.php';

/**
 * Tests Zend_View_Helper_Navigation
 *
 */
class Zend_View_Helper_Navigation_NavigationTest
    extends Zend_View_Helper_Navigation_TestAbstract
{
    /**
     * Class name for view helper to test
     *
     * @var string
     */
    protected $_helperName = 'Zend_View_Helper_Navigation';

    /**
     * View helper
     *
     * @var Zend_View_Helper_Navigation
     */
    protected $_helper;

    public function testShouldProxyToMenuHelperByDeafult()
    {
        // setup
        $oldReg = null;
        if (Zend_Registry::isRegistered(self::REGISTRY_KEY)) {
            $oldReg = Zend_Registry::get(self::REGISTRY_KEY);
        }
        Zend_Registry::set(self::REGISTRY_KEY, $this->_nav1);
        $this->_helper->setContainer(null);

        // result
        $expected = $this->_getExpected('menu/default1.html');
        $actual = $this->_helper->render();

        // teardown
        Zend_Registry::set(self::REGISTRY_KEY, $oldReg);

        $this->assertEquals($expected, $actual);
    }

    public function testHasContainer()
    {
        $oldContainer = $this->_helper->getContainer();
        $this->_helper->setContainer(null);
        $this->assertFalse($this->_helper->hasContainer());
        $this->_helper->setContainer($oldContainer);
    }

    public function testInjectingContainer()
    {
        // setup
        $this->_helper->setContainer($this->_nav2);
        $expected = array(
            'menu' => $this->_getExpected('menu/default2.html'),
            'breadcrumbs' => $this->_getExpected('bc/default.html')
        );
        $actual = array();

        // result
        $actual['menu'] = $this->_helper->render();
        $this->_helper->setContainer($this->_nav1);
        $actual['breadcrumbs'] = $this->_helper->breadcrumbs()->render();

        $this->assertEquals($expected, $actual);
    }

    public function testDisablingContainerInjection()
    {
        // setup
        $this->_helper->setInjectContainer(false);
        $this->_helper->menu()->setContainer(null);
        $this->_helper->breadcrumbs()->setContainer(null);
        $this->_helper->setContainer($this->_nav2);

        // result
        $expected = array(
            'menu'        => '',
            'breadcrumbs' => ''
        );
        $actual = array(
            'menu'        => $this->_helper->render(),
            'breadcrumbs' => $this->_helper->breadcrumbs()->render()
        );

        $this->assertEquals($expected, $actual);
    }

    public function testInjectingAcl()
    {
        // setup
        $acl = $this->_getAcl();
        $this->_helper->setAcl($acl['acl']);
        $this->_helper->setRole($acl['role']);

        $expected = $this->_getExpected('menu/acl.html');
        $actual = $this->_helper->render();

        $this->assertEquals($expected, $actual);
    }

    public function testDisablingAclInjection()
    {
        // setup
        $acl = $this->_getAcl();
        $this->_helper->setAcl($acl['acl']);
        $this->_helper->setRole($acl['role']);
        $this->_helper->setInjectAcl(false);

        $expected = $this->_getExpected('menu/default1.html');
        $actual = $this->_helper->render();

        $this->assertEquals($expected, $actual);
    }

    public function testInjectingTranslator()
    {
        $this->_helper->setTranslator($this->_getTranslator());

        $expected = $this->_getExpected('menu/translated.html');
        $actual = $this->_helper->render();

        $this->assertEquals($expected, $actual);
    }

    public function testDisablingTranslatorInjection()
    {
        $this->_helper->setTranslator($this->_getTranslator());
        $this->_helper->setInjectTranslator(false);

        $expected = $this->_getExpected('menu/default1.html');
        $actual = $this->_helper->render();

        $this->assertEquals($expected, $actual);
    }

    public function testSpecifyingDefaultProxy()
    {
        $expected = array(
            'breadcrumbs' => $this->_getExpected('bc/default.html'),
            'menu' => $this->_getExpected('menu/default1.html')
        );
        $actual = array();

        // result
        $this->_helper->setDefaultProxy('breadcrumbs');
        $actual['breadcrumbs'] = $this->_helper->render($this->_nav1);
        $this->_helper->setDefaultProxy('menu');
        $actual['menu'] = $this->_helper->render($this->_nav1);

        $this->assertEquals($expected, $actual);
    }
}