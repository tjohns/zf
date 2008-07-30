<?php
/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 */

/** Zend_Controller_Router_Route_Chain */
require_once 'Zend/Controller/Router/Route/Chain.php';

/** Zend_Controller_Router_Route */
require_once 'Zend/Controller/Router/Route.php';

/** Zend_Controller_Router_Route_Static */
require_once 'Zend/Controller/Router/Route/Static.php';

/** Zend_Controller_Router_Route_Static */
require_once 'Zend/Controller/Router/Route/Regex.php';

/** PHPUnit test case */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 */
class Zend_Controller_Router_Route_ChainTest extends PHPUnit_Framework_TestCase
{

    public function testChaining()
    {
        $request = new Zend_Controller_Router_RewriteTest_Request('http://localhost/foo/bar');

        $foo = new Zend_Controller_Router_Route('foo');
        $bar = new Zend_Controller_Router_Route('bar');

        $chain = $foo->chain($bar);

        $this->assertType('Zend_Controller_Router_Route_Chain', $chain);
    }

    public function testChainingMatch()
    {
        $chain = new Zend_Controller_Router_Route_Chain();

        $foo = new Zend_Controller_Router_Route('foo', array('foo' => 1));
        $bar = new Zend_Controller_Router_Route('bar', array('bar' => 2));

        $chain->chain($foo)->chain($bar);

        $res = $chain->match('foo/bar');

        $this->assertEquals(1, $res['foo']);
        $this->assertEquals(2, $res['bar']);
    }

    public function testChainingShortcutMatch()
    {
        $foo = new Zend_Controller_Router_Route('foo', array('foo' => 1));
        $bar = new Zend_Controller_Router_Route('bar', array('bar' => 2, 'controller' => 'foo', 'action' => 'bar'));

        $chain = $foo->chain($bar);

        $res = $chain->match('foo/bar');

        $this->assertEquals(1, $res['foo']);
        $this->assertEquals(2, $res['bar']);
    }

    public function testChainingMatchFailure()
    {
        $foo = new Zend_Controller_Router_Route('foo', array('foo' => 1));
        $bar = new Zend_Controller_Router_Route('bar', array('bar' => 2, 'controller' => 'foo', 'action' => 'bar'));

        $chain = $foo->chain($bar);

        $res = $chain->match('foo/all');

        $this->assertSame(false, $res);
    }

    public function testChainingVariableOverriding()
    {
        $foo = new Zend_Controller_Router_Route('foo', array('foo' => 1, 'controller' => 'foo', 'module' => 'foo'));
        $bar = new Zend_Controller_Router_Route('bar', array('bar' => 2, 'controller' => 'bar', 'action' => 'bar'));
        $baz = new Zend_Controller_Router_Route('baz', array('baz' => 3));

        $chain = $foo->chain($bar)->chain($baz);

        $res = $chain->match('foo/bar/baz');

        $this->assertEquals('foo', $res['module']);
        $this->assertEquals('bar', $res['controller']);
        $this->assertEquals('bar', $res['action']);
    }

    public function testChainingSeparatorOverriding()
    {
        $foo = new Zend_Controller_Router_Route('foo', array('foo' => 1));
        $bar = new Zend_Controller_Router_Route('bar', array('bar' => 2));
        $baz = new Zend_Controller_Router_Route('baz', array('baz' => 3));

        $chain = $foo->chain($bar, '.');

        $res = $chain->match('foo.bar');
        $this->assertType('array', $res);

        $res = $chain->match('foo/bar');
        $this->assertEquals(false, $res);

        $chain->chain($baz, ':');

        $res = $chain->match('foo.bar:baz');
        $this->assertType('array', $res);
    }

    public function testI18nChaining()
    {
        $lang = new Zend_Controller_Router_Route(':lang', array('lang' => 'en'));
        $profile = new Zend_Controller_Router_Route('user/:id', array('controller' => 'foo', 'action' => 'bar'));

        $chain = $lang->chain($profile);

        $res = $chain->match('en/user/1');

        $this->assertEquals('en', $res['lang']);
        $this->assertEquals('1', $res['id']);
    }

    public function testChainingAssemble()
    {
        $chain = new Zend_Controller_Router_Route_Chain();

        $foo = new Zend_Controller_Router_Route(':foo');
        $bar = new Zend_Controller_Router_Route(':bar');

        $chain->chain($foo)->chain($bar);

        $res = $chain->match('foo/bar');
        $this->assertEquals('foo/bar', $chain->assemble());
    }

    public function testChainingMatchAndAssembleStatic()
    {
        $chain = new Zend_Controller_Router_Route_Chain();

        $foo = new Zend_Controller_Router_Route_Static('foo', array('foo' => 1));
        $bar = new Zend_Controller_Router_Route_Static('bar', array('bar' => 2));

        $chain->chain($foo)->chain($bar);

        $res = $chain->match('foo/bar');

        $this->assertType('array', $res);
        $this->assertEquals('foo/bar', $chain->assemble());
    }

    public function testChainingMatchAndAssembleRegex()
    {
        $chain = new Zend_Controller_Router_Route_Chain();

        $foo = new Zend_Controller_Router_Route_Regex('foo', array('foo' => 1));
        $bar = new Zend_Controller_Router_Route_Regex('bar', array('bar' => 2));

        $chain->chain($foo)->chain($bar);

        $res = $chain->match('foo/bar');

        $this->assertType('array', $res);
        $this->assertEquals('foo/bar', $chain->assemble());
    }
    
    public function testChainingReuse()
    {
        $this->markTestIncomplete();

        $request = new Zend_Controller_Router_RewriteTest_Request('http://localhost/foo/bar');

        $lang = new Zend_Controller_Router_Route(':lang', array('lang' => 'en'));
        $profile = new Zend_Controller_Router_Route('user/:id', array('controller' => 'foo', 'action' => 'bar'));
        $article = new Zend_Controller_Router_Route('article/:id', array('controller' => 'foo', 'action' => 'bar'));

        $profileChain = $lang->chain($profile);
        $articleChain = $lang->chain($article);
    }

}