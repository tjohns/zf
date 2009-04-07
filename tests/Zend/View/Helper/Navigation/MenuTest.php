<?php
require_once dirname(__FILE__) . '/TestAbstract.php';
require_once 'Zend/View/Helper/Navigation/Menu.php';

/**
 * Tests Zend_View_Helper_Navigation_Menu
 *
 */
class Zend_View_Helper_Navigation_MenuTest
    extends Zend_View_Helper_Navigation_TestAbstract
{
    /**
     * Class name for view helper to test
     *
     * @var string
     */
    protected $_helperName = 'Zend_View_Helper_Navigation_Menu';

    /**
     * View helper
     *
     * @var Zend_View_Helper_Navigation_Menu
     */
    protected $_helper;

    public function testNullingOutContainerInHelper()
    {
        $this->_helper->setContainer();
        $this->assertEquals(0, count($this->_helper->getContainer()));
    }

    public function testAutoloadingContainerFromRegistry()
    {
        $oldReg = null;
        if (Zend_Registry::isRegistered(self::REGISTRY_KEY)) {
            $oldReg = Zend_Registry::get(self::REGISTRY_KEY);
        }
        Zend_Registry::set(self::REGISTRY_KEY, $this->_nav1);

        $this->_helper->setContainer(null);

        $expected = $this->_getExpected('menu/default1.html');
        $actual = $this->_helper->render();

        Zend_Registry::set(self::REGISTRY_KEY, $oldReg);

        $this->assertEquals($expected, $actual);
    }

    public function testSetIndentAndOverrideInRenderMenu()
    {
        $this->_helper->setIndent(8);

        $expected = array(
            'indent4' => $this->_getExpected('menu/indent4.html'),
            'indent8' => $this->_getExpected('menu/indent8.html')
        );

        $renderOptions = array(
            'indent' => 4
        );

        $actual = array(
            'indent4' => rtrim($this->_helper->renderMenu(null, $renderOptions), PHP_EOL),
            'indent8' => rtrim($this->_helper->renderMenu(), PHP_EOL)
        );

        $this->assertEquals($expected, $actual);
    }

    public function testRenderSuppliedContainerWithoutInterfering()
    {
        $rendered1 = $this->_getExpected('menu/default1.html');
        $rendered2 = $this->_getExpected('menu/default2.html');
        $expected = array(
            'registered'       => $rendered1,
            'supplied'         => $rendered2,
            'registered_again' => $rendered1
        );

        $actual = array(
            'registered'       => $this->_helper->render(),
            'supplied'         => $this->_helper->render($this->_nav2),
            'registered_again' => $this->_helper->render()
        );

        $this->assertEquals($expected, $actual);
    }

    public function testUseAclRoleAsString()
    {
        $acl = $this->_getAcl();
        $this->_helper->setAcl($acl['acl']);
        $this->_helper->setRole('member');

        $expected = $this->_getExpected('menu/acl_string.html');
        $this->assertEquals($expected, $this->_helper->render());
    }

    public function testFilterOutPagesBasedOnAcl()
    {
        $acl = $this->_getAcl();
        $this->_helper->setAcl($acl['acl']);
        $this->_helper->setRole($acl['role']);

        $expected = $this->_getExpected('menu/acl.html');
        $actual = $this->_helper->render();

        $this->assertEquals($expected, $actual);
    }

    public function testDisablingAcl()
    {
        $acl = $this->_getAcl();
        $this->_helper->setAcl($acl['acl']);
        $this->_helper->setRole($acl['role']);
        $this->_helper->setUseAcl(false);

        $expected = $this->_getExpected('menu/default1.html');
        $actual = $this->_helper->render();

        $this->assertEquals($expected, $actual);
    }

    public function testUseAnAclRoleInstanceFromAclObject()
    {
        $acl = $this->_getAcl();
        $this->_helper->setAcl($acl['acl']);
        $this->_helper->setRole($acl['acl']->getRole('member'));

        $expected = $this->_getExpected('menu/acl_role_interface.html');
        $this->assertEquals($expected, $this->_helper->render());
    }

    public function testUseConstructedAclRolesNotFromAclObject()
    {
        $acl = $this->_getAcl();
        $this->_helper->setAcl($acl['acl']);
        $this->_helper->setRole(new Zend_Acl_Role('member'));

        $expected = $this->_getExpected('menu/acl_role_interface.html');
        $this->assertEquals($expected, $this->_helper->render());
    }

    public function testSetUlCssClass()
    {
        $this->_helper->setUlClass('My_Nav');
        $expected = $this->_getExpected('menu/css.html');
        $this->assertEquals($expected, $this->_helper->render($this->_nav2));
    }

    public function testTranslationUsingZendTranslate()
    {
        $translator = $this->_getTranslator();
        $this->_helper->setTranslator($translator);

        $expected = $this->_getExpected('menu/translated.html');
        $this->assertEquals($expected, $this->_helper->render());
    }

    public function testTranslationUsingZendTranslateAdapter()
    {
        $translator = $this->_getTranslator();
        $this->_helper->setTranslator($translator->getAdapter());

        $expected = $this->_getExpected('menu/translated.html');
        $this->assertEquals($expected, $this->_helper->render());
    }

    public function testTranslationUsingTranslatorFromRegistry()
    {
        $oldReg = Zend_Registry::isRegistered('Zend_Translate')
                ? Zend_Registry::get('Zend_Translate')
                : null;

        $translator = $this->_getTranslator();
        Zend_Registry::set('Zend_Translate', $translator);

        $expected = $this->_getExpected('menu/translated.html');
        $actual = $this->_helper->render();

        Zend_Registry::set('Zend_Translate', $oldReg);

        $this->assertEquals($expected, $actual);

    }

    public function testDisablingTranslation()
    {
        $translator = $this->_getTranslator();
        $this->_helper->setTranslator($translator);
        $this->_helper->setUseTranslator(false);

        $expected = $this->_getExpected('menu/default1.html');
        $this->assertEquals($expected, $this->_helper->render());
    }

    public function testRenderingPartial()
    {
        $this->_helper->setPartial('menu.phtml');

        $expected = $this->_getExpected('menu/partial.html');
        $actual = $this->_helper->render();

        $this->assertEquals($expected, $actual);
    }

    public function testRenderingPartialBySpecifyingAnArrayAsPartial()
    {
        $this->_helper->setPartial(array('menu.phtml', 'default'));

        $expected = $this->_getExpected('menu/partial.html');
        $actual = $this->_helper->render();

        $this->assertEquals($expected, $actual);
    }

    public function testRenderingPartialShouldFailOnInvalidPartialArray()
    {
        $this->_helper->setPartial(array('menu.phtml'));

        try {
            $this->_helper->render();
            $this->fail('invalid $partial should throw Zend_View_Exception');
        } catch (Zend_View_Exception $e) {
        }
    }

    public function testSetMaxDepth()
    {
        $options = array(
            'maxDepth' => 1
        );

        $expected = $this->_getExpected('menu/maxdepth.html');
        $actual = $this->_helper->renderMenu(null, $options);

        $this->assertEquals($expected, $actual);
    }

    public function testSetMinDepth()
    {
        $options = array(
            'minDepth' => 1
        );

        $expected = $this->_getExpected('menu/mindepth.html');
        $actual = $this->_helper->renderMenu(null, $options);

        $this->assertEquals($expected, $actual);
    }

    public function testSetBothDepts()
    {
        $options = array(
            'minDepth' => 1,
            'maxDepth' => 2
        );

        $expected = $this->_getExpected('menu/bothdepts.html');
        $actual = $this->_helper->renderMenu(null, $options);

        $this->assertEquals($expected, $actual);
    }

    public function testOnlyActiveBranch()
    {
        $options = array(
            'onlyActiveBranch' => true
        );

        $expected = $this->_getExpected('menu/onlyactivebranch.html');
        $actual = $this->_helper->renderMenu(null, $options);

        $this->assertEquals($expected, $actual);
    }

    public function testOnlyActiveBranchNoParents()
    {
        $options = array(
            'onlyActiveBranch' => true,
            'renderParents' => false
        );

        $expected = $this->_getExpected('menu/onlyactivebranch_noparents.html');
        $actual = $this->_helper->renderMenu(null, $options);

        $this->assertEquals($expected, $actual);
    }

    public function testOnlyActiveBranchAndMinDepth()
    {
        $options = array(
            'minDepth' => 1,
            'onlyActiveBranch' => true
        );

        $expected = $this->_getExpected('menu/onlyactivebranch_mindepth.html');
        $actual = $this->_helper->renderMenu(null, $options);

        $this->assertEquals($expected, $actual);
    }

    public function testOnlyActiveBranchAndMaxDepth()
    {
        $options = array(
            'maxDepth' => 2,
            'onlyActiveBranch' => true
        );

        $expected = $this->_getExpected('menu/onlyactivebranch_maxdepth.html');
        $actual = $this->_helper->renderMenu(null, $options);

        $this->assertEquals($expected, $actual);
    }

    public function testOnlyActiveBranchAndBothDepthsSpecified()
    {
        $options = array(
            'minDepth' => 1,
            'maxDepth' => 2,
            'onlyActiveBranch' => true
        );

        $expected = $this->_getExpected('menu/onlyactivebranch_bothdepts.html');
        $actual = $this->_helper->renderMenu(null, $options);

        $this->assertEquals($expected, $actual);
    }

    public function testOnlyActiveBranchNoParentsAndBothDepthsSpecified()
    {
        $options = array(
            'minDepth' => 2,
            'maxDepth' => 2,
            'onlyActiveBranch' => true,
            'renderParents' => false
        );

        $expected = $this->_getExpected('menu/onlyactivebranch_np_bd.html');
        $actual = $this->_helper->renderMenu(null, $options);

        $this->assertEquals($expected, $actual);
    }
}