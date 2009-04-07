<?php
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'Zend/Navigation/Page.php';
require_once 'Zend/Config.php';

/**
 * Tests the class Zend_Navigation_Page
 *
 * @author    Robin Skoglund
 * @category  Zend_Tests
 * @package   Zend_Navigation
 * @license   http://www.zym-project.com/license    New BSD License
 * @copyright Copyright (c) 2008 Zend. (http://www.zym-project.com/)
 */
class Zend_Navigation_PageTest extends PHPUnit_Framework_TestCase
{
    /**
     * Prepares the environment before running a test.
     *
     */
    protected function setUp()
    {

    }

    /**
     * Tear down the environment after running a test
     *
     */
    protected function tearDown()
    {
        // setConfig, setOptions
    }

    public function testSetAndGetLabel()
    {
        $page = Zend_Navigation_Page::factory(array(
            'label' => 'foo',
            'uri' => '#'
        ));

        $this->assertEquals('foo', $page->getLabel());
        $page->setLabel('bar');
        $this->assertEquals('bar', $page->getLabel());

        $invalids = array(42, (object) null);
        foreach ($invalids as $invalid) {
            try {
                $page->setLabel($invalid);
                $msg = $invalid . ' is invalid, but no ';
                $msg .= 'Zend_Navigation_Exception was thrown';
                $this->fail($msg);
            } catch (Zend_Navigation_Exception $e) {

            }
        }
    }

    public function testSetAndGetId()
    {
        $page = Zend_Navigation_Page::factory(array(
            'label' => 'foo',
            'uri' => '#'
        ));

        $this->assertEquals(null, $page->getId());

        $page->setId('bar');
        $this->assertEquals('bar', $page->getId());

        $invalids = array(true, (object) null);
        foreach ($invalids as $invalid) {
            try {
                $page->setId($invalid);
                $msg = $invalid . ' is invalid, but no ';
                $msg .= 'Zend_Navigation_Exception was thrown';
                $this->fail($msg);
            } catch (Zend_Navigation_Exception $e) {

            }
        }
    }

    public function testIdCouldBeAnInteger()
    {
        $page = Zend_Navigation_Page::factory(array(
            'label' => 'foo',
            'uri' => '#',
            'id' => 10
        ));

        $this->assertEquals(10, $page->getId());
    }

    public function testSetAndGetClass()
    {
        $page = Zend_Navigation_Page::factory(array(
            'label' => 'foo',
            'uri' => '#'
        ));

        $this->assertEquals(null, $page->getClass());
        $page->setClass('bar');
        $this->assertEquals('bar', $page->getClass());

        $invalids = array(42, true, (object) null);
        foreach ($invalids as $invalid) {
            try {
                $page->setClass($invalid);
                $msg = $invalid . ' is invalid, but no ';
                $msg .= 'Zend_Navigation_Exception was thrown';
                $this->fail($msg);
            } catch (Zend_Navigation_Exception $e) {

            }
        }
    }

    public function testSetAndGetTitle()
    {
        $page = Zend_Navigation_Page::factory(array(
            'label' => 'foo',
            'uri' => '#'
        ));

        $this->assertEquals(null, $page->getTitle());
        $page->setTitle('bar');
        $this->assertEquals('bar', $page->getTitle());

        $invalids = array(42, true, (object) null);
        foreach ($invalids as $invalid) {
            try {
                $page->setTitle($invalid);
                $msg = $invalid . ' is invalid, but no ';
                $msg .= 'Zend_Navigation_Exception was thrown';
                $this->fail($msg);
            } catch (Zend_Navigation_Exception $e) {

            }
        }
    }

    public function testSetAndGetTarget()
    {
        $page = Zend_Navigation_Page::factory(array(
            'label' => 'foo',
            'uri' => '#'
        ));

        $this->assertEquals(null, $page->getTarget());
        $page->setTarget('bar');
        $this->assertEquals('bar', $page->getTarget());

        $invalids = array(42, true, (object) null);
        foreach ($invalids as $invalid) {
            try {
                $page->setTarget($invalid);
                $msg = $invalid . ' is invalid, but no ';
                $msg .= 'Zend_Navigation_Exception was thrown';
                $this->fail($msg);
            } catch (Zend_Navigation_Exception $e) {

            }
        }
    }

    public function testConstructingWithRelationsInArray()
    {
        $page = Zend_Navigation_Page::factory(array(
            'label' => 'bar',
            'uri'   => '#',
            'rel'   => array(
                'prev' => 'foo',
                'next' => 'baz'
            ),
            'rev'   => array(
                'alternate' => 'bat'
            )
        ));

        $expected = array(
            'rel'   => array(
                'prev' => 'foo',
                'next' => 'baz'
            ),
            'rev'   => array(
                'alternate' => 'bat'
            )
        );

        $actual = array(
            'rel' => $page->getRel(),
            'rev' => $page->getRev()
        );

        $this->assertEquals($expected, $actual);
    }

    public function testConstructingWithRelationsInConfig()
    {
        $page = Zend_Navigation_Page::factory(new Zend_Config(array(
            'label' => 'bar',
            'uri'   => '#',
            'rel'   => array(
                'prev' => 'foo',
                'next' => 'baz'
            ),
            'rev'   => array(
                'alternate' => 'bat'
            )
        )));

        $expected = array(
            'rel'   => array(
                'prev' => 'foo',
                'next' => 'baz'
            ),
            'rev'   => array(
                'alternate' => 'bat'
            )
        );

        $actual = array(
            'rel' => $page->getRel(),
            'rev' => $page->getRev()
        );

        $this->assertEquals($expected, $actual);
    }

    public function testGettingSpecificRelations()
    {
        $page = Zend_Navigation_Page::factory(array(
            'label' => 'bar',
            'uri'   => '#',
            'rel'   => array(
                'prev' => 'foo',
                'next' => 'baz'
            ),
            'rev'   => array(
                'next' => 'foo'
            )
        ));

        $expected = array(
            'foo', 'foo'
        );

        $actual = array(
            $page->getRel('prev'),
            $page->getRev('next')
        );

        $this->assertEquals($expected, $actual);
    }

    public function testSetAndGetOrder()
    {
        $page = Zend_Navigation_Page::factory(array(
            'label' => 'foo',
            'uri' => '#'
        ));

        $this->assertEquals(null, $page->getOrder());
        $page->setOrder('1');
        $this->assertEquals(1, $page->getOrder());
        $page->setOrder(1337);
        $this->assertEquals(1337, $page->getOrder());
        $page->setOrder('-25');
        $this->assertEquals(-25, $page->getOrder());

        $invalids = array(3.14, 'e', "\n", '0,4', true, (object) null);
        foreach ($invalids as $invalid) {
            try {
                $page->setOrder($invalid);
                $msg = $invalid . ' is invalid, but no ';
                $msg .= 'Zend_Navigation_Exception was thrown';
                $this->fail($msg);
            } catch (Zend_Navigation_Exception $e) {

            }
        }
    }

    public function testSetAndGetActive()
    {
        $page = Zend_Navigation_Page::factory(array(
            'label' => 'foo',
            'uri' => '#'
        ));

        $valids = array(true, 1, '1', 3.14, 'true', 'yes');
        foreach ($valids as $valid) {
            $page->setActive($valid);
            $this->assertEquals(true, $page->isActive());
        }

        $invalids = array(false, 0, '0', 0.0, array());
        foreach ($invalids as $invalid) {
            $page->setActive($invalid);
            $this->assertEquals(false, $page->isActive());
        }
    }

    public function testSetAndGetVisible()
    {
        $page = Zend_Navigation_Page::factory(array(
            'label' => 'foo',
            'uri' => '#'
        ));

        $valids = array(true, 1, '1', 3.14, 'true', 'yes');
        foreach ($valids as $valid) {
            $page->setVisible($valid);
            $this->assertEquals(true, $page->isVisible());
        }

        $invalids = array(false, 0, '0', 0.0, array());
        foreach ($invalids as $invalid) {
            $page->setVisible($invalid);
            $this->assertEquals(false, $page->isVisible());
        }
    }

    public function testMagicOverLoadsShouldSetAndGetNativeProperties()
    {
        $page = Zend_Navigation_Page::factory(array(
            'label' => 'foo',
            'uri' => 'foo'
        ));

        $this->assertSame('foo', $page->getUri());
        $this->assertSame('foo', $page->uri);

        $page->uri = 'bar';
        $this->assertSame('bar', $page->getUri());
        $this->assertSame('bar', $page->uri);
    }

    public function testMagicOverLoadsShouldCheckNativeProperties()
    {
        $page = Zend_Navigation_Page::factory(array(
            'label' => 'foo',
            'uri' => 'foo'
        ));

        $this->assertTrue(isset($page->uri));

        try {
            unset($page->uri);
            $this->fail('Should not be possible to unset native properties');
        } catch (Zend_Navigation_Exception $e) {

        }
    }

    public function testMagicOverLoadsShouldHandleCustomProperties()
    {
        $page = Zend_Navigation_Page::factory(array(
            'label' => 'foo',
            'uri' => 'foo'
        ));

        $this->assertFalse(isset($page->category));

        $page->category = 'music';
        $this->assertTrue(isset($page->category));
        $this->assertSame('music', $page->category);

        unset($page->category);
        $this->assertFalse(isset($page->category));
    }

    public function testMagicToStringMethodShouldReturnLabel()
    {
        $page = Zend_Navigation_Page::factory(array(
            'label' => 'foo',
            'uri' => '#'
        ));

        $this->assertEquals('foo', (string) $page);
    }

    public function testSetOptionsShouldTranslateToAccessor()
    {
        $page = Zend_Navigation_Page::factory(array(
            'label' => 'foo',
            'action' => 'index',
            'controller' => 'index'
        ));

        $options = array(
            'label' => 'bar',
            'action' => 'baz',
            'controller' => 'bat',
            'module' => 'test',
            'reset_params' => false,
            'id' => 'foo-test'
        );

        $page->setOptions($options);

        $expected = array(
            'label'       => 'bar',
            'action'      => 'baz',
            'controller'  => 'bat',
            'module'      => 'test',
            'resetParams' => false,
            'id'          => 'foo-test'
        );

        $actual = array(
            'label'       => $page->getLabel(),
            'action'      => $page->getAction(),
            'controller'  => $page->getController(),
            'module'      => $page->getModule(),
            'resetParams' => $page->getResetParams(),
            'id'          => $page->getId()
        );

        $this->assertEquals($expected, $actual);
    }

    public function testSetConfig()
    {
        $page = Zend_Navigation_Page::factory(array(
            'label' => 'foo',
            'action' => 'index',
            'controller' => 'index'
        ));

        $options = array(
            'label' => 'bar',
            'action' => 'baz',
            'controller' => 'bat',
            'module' => 'test',
            'reset_params' => false,
            'id' => 'foo-test'
        );

        $page->setConfig(new Zend_Config($options));

        $expected = array(
            'label'       => 'bar',
            'action'      => 'baz',
            'controller'  => 'bat',
            'module'      => 'test',
            'resetParams' => false,
            'id'          => 'foo-test'
        );

        $actual = array(
            'label'       => $page->getLabel(),
            'action'      => $page->getAction(),
            'controller'  => $page->getController(),
            'module'      => $page->getModule(),
            'resetParams' => $page->getResetParams(),
            'id'          => $page->getId()
        );

        $this->assertEquals($expected, $actual);
    }

    public function testSetOptionsShouldSetCustomProperties()
    {
        $page = Zend_Navigation_Page::factory(array(
            'label' => 'foo',
            'uri' => '#'
        ));

        $options = array(
            'test' => 'test',
            'meaning' => 42
        );

        $page->setOptions($options);

        $actual = array(
            'test' => $page->test,
            'meaning' => $page->meaning
        );

        $this->assertEquals($options, $actual);
    }

    public function testAddingRelations()
    {
        $page = Zend_Navigation_Page::factory(array(
            'label' => 'page',
            'uri'   => '#'
        ));

        $page->addRel('alternate', 'foo');
        $page->addRev('alternate', 'bar');

        $expected = array(
            'rel' => array('alternate' => 'foo'),
            'rev' => array('alternate' => 'bar')
        );

        $actual = array(
            'rel' => $page->getRel(),
            'rev' => $page->getRev()
        );

        $this->assertEquals($expected, $actual);
    }

    public function testRemovingRelations()
    {
        $page = Zend_Navigation_Page::factory(array(
            'label' => 'page',
            'uri'   => '#'
        ));

        $page->addRel('alternate', 'foo');
        $page->addRev('alternate', 'bar');
        $page->removeRel('alternate');
        $page->removeRev('alternate');

        $expected = array(
            'rel' => array(),
            'rev' => array()
        );

        $actual = array(
            'rel' => $page->getRel(),
            'rev' => $page->getRev()
        );

        $this->assertEquals($expected, $actual);
    }

    public function testGetCustomProperties()
    {
        $page = Zend_Navigation_Page::factory(array(
            'label' => 'foo',
            'uri' => '#',
            'baz' => 'bat'
        ));

        $options = array(
            'test' => 'test',
            'meaning' => 42
        );

        $page->setOptions($options);

        $expected = array(
            'baz' => 'bat',
            'test' => 'test',
            'meaning' => 42
        );

        $this->assertEquals($expected, $page->getCustomProperties());
    }

    public function testToArrayMethod()
    {
        $options = array(
            'label'    => 'foo',
            'uri'      => '#',
            'id'       => 'my-id',
            'class'    => 'my-class',
            'title'    => 'my-title',
            'target'   => 'my-target',
            'rel'      => array(),
            'rev'      => array(),
            'order'    => 100,
            'active'   => true,
            'visible'  => false,

            'resource' => 'joker',
            'privilege' => null,

            'foo'      => 'bar',
            'meaning'  => 42,

            'pages'    => array(
                array(
                    'label' => 'foo.bar',
                    'uri'   => '#'
                ),
                array(
                    'label' => 'foo.baz',
                    'uri'   => '#'
                )
            )
        );

        $page = Zend_Navigation_Page::factory($options);
        $toArray = $page->toArray();

        // tweak options to what we expect toArray() to contain
        $options['type'] = 'Zend_Navigation_Page_Uri';

        // calculate diff between toArray() and $options
        $diff = array_diff_assoc($toArray, $options);

        // should be no diff
        $this->assertEquals(array(), $diff);

        // $toArray should have 2 sub pages
        $this->assertEquals(2, count($toArray['pages']));

        // tweak options to what we expect sub page 1 to be
        $options['label'] = 'foo.bar';
        $options['order'] = null;
        $options['id'] = null;
        $options['class'] = null;
        $options['title'] = null;
        $options['target'] = null;
        $options['resource'] = null;
        $options['active'] = false;
        $options['visible'] = true;
        unset($options['foo']);
        unset($options['meaning']);

        // assert that there is no diff from what we expect
        $subPageOneDiff = array_diff_assoc($toArray['pages'][0], $options);
        $this->assertEquals(array(), $subPageOneDiff);

        // tweak options to what we expect sub page 2 to be
        $options['label'] = 'foo.baz';

        // assert that there is no diff from what we expect
        $subPageTwoDiff = array_diff_assoc($toArray['pages'][1], $options);
        $this->assertEquals(array(), $subPageTwoDiff);
    }
}