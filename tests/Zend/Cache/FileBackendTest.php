<?php
/**
 * @package    Zend_Cache
 * @subpackage UnitTests
 */
 
 /**
 * Zend_Cache
 */
require_once 'Zend/Cache.php';
require_once 'Zend/Cache/Backend/File.php';

/**
 * PHPUnit2 test case
 */
require_once 'PHPUnit2/Framework/TestCase.php';

/**
 * @package    Zend_Cache
 * @subpackage UnitTests
 */
class Zend_Cache_fileBackendTest extends PHPUnit2_Framework_TestCase {
    
    private $_instance;
    private $_cacheDir;
    
    public function setUp()
    {        
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $this->_cacheDir = $this->_getTmpDirWindows() . DIRECTORY_SEPARATOR;
        } else {
            $this->_cacheDir = $this->_getTmpDirUnix() . DIRECTORY_SEPARATOR;
        }
        $this->_instance = new Zend_Cache_Backend_File(array(
            'cacheDir' => $this->_cacheDir
        ));
        $this->_instance->save('bar : data to cache', 'bar', array('tag3', 'tag4'));
    }
    
    private function _getTmpDirWindows()
    {
        if (isset($_ENV['TEMP'])) {
            return $_ENV['TEMP'];
        }
        if (isset($_ENV['TMP'])) {
            return $_ENV['TMP'];
        }
        if (isset($_ENV['windir'])) {
            return $_ENV['windir'] . '\\temp';
        }
        if (isset($_ENV['SystemRoot'])) {
            return $_ENV['SystemRoot'] . '\\temp';
        }
        if (isset($_SERVER['TEMP'])) {
            return $_SERVER['TEMP'];
        }
        if (isset($_SERVER['TMP'])) {
            return $_SERVER['TMP'];
        }
        if (isset($_SERVER['windir'])) {
            return $_SERVER['windir'] . '\\temp';
        }
        if (isset($_SERVER['SystemRoot'])) {
            return $_SERVER['SystemRoot'] . '\\temp';
        }
        return '\temp';
    }
    
    private function _getTmpDirUnix()
    {
        if (isset($_ENV['TMPDIR'])) {
	        return $_ENV['TMPDIR'];
	    }
	    if (isset($_SERVER['TMPDIR'])) {
	        return $_SERVER['TMPDIR'];
	    }
	    return '/tmp';
    }
    
    public function tearDown()
    {
        $this->_instance->clean('all');
        unset($this->_instance);
    }
    
    public function testConstructorCorrectCall()
    {
        $test = new Zend_Cache_Backend_File(array('readControl' => true, 'fileLocking' => true));    
    }
    
    public function testConstructorBadCall()
    {
        try {
            $test = new Zend_Cache_Backend_File('readControl');
        } catch (Zend_Cache_Exception $e) {
            return;
        }
        $this->fail('Zend_Cache_Exception was expected but not thrown');    
    }
       
    public function testConstructorBadOption()
    {
        try {
            $test = new Zend_Cache_Backend_File(array('foo' => 'bar', 'fileLocking' => true));
        } catch (Zend_Cache_Exception $e) {
            return;
        }
        $this->fail('Zend_Cache_Exception was expected but not thrown'); 
    }
    
    public function testSetDirectivesCorrectCall()
    {
        $this->_instance->setDirectives(array('lifeTime' => 3600, 'logging' => true));
    }
    
    public function testSetDirectivesBadCall()
    {
        try {
            $this->_instance->setDirectives('foo');
        } catch (Zend_Cache_Exception $e) {
            return;
        }
        $this->fail('Zend_Cache_Exception was expected but not thrown'); 
    }
    
    public function testSetDirectivesBadDirective()
    {
        // A bad directive (not known by a specific backend) is possible
        // => so no exception here
        $this->_instance->setDirectives(array('foo' => true, 'lifeTime' => 3600));
    }
    
    public function testSetDirectivesBadDirective2()
    {
        try {
            $this->_instance->setDirectives(array('foo' => true, 12 => 3600));
        } catch (Zend_Cache_Exception $e) {
            return;
        }
        $this->fail('Zend_Cache_Exception was expected but not thrown'); 
    }
    
    private function _testCacheFiles($id, $tags, $hashed = 0, $inverse = false)
    {
        if (!file_exists($this->_path("cache_$id", $hashed) . "cache_$id")) {
            if (!$inverse) {
                $this->fail('There is no cache file'); 
            }
        } else {
            if ($inverse) {
                 $this->fail('There is still a cache file'); 
            }
        }
        foreach ($tags as $tag) {
            if (!file_exists($this->_path("cache_internal_$id---$tag", $hashed) . "cache_internal_$id---$tag")) {
                if (!$inverse) {
                    $this->fail("There is no $tag file"); 
                }
            } else {
                if ($inverse) {
                    $this->fail("There is still a $tag file"); 
                }
            }
         }
    }
    
    private function _path($fileName, $hashed = 0) 
    {
        $root = $this->_cacheDir;
        if ($hashed > 0) {
            if (strpos($fileName, '---') > 0) {
                $fileName = preg_replace('~^cache_internal_(.*)---(.*)$~' ,'cache_$1', $fileName);
            }
            $hash = md5($fileName);
            for ($i=0 ; $i<$hashed ; $i++) {
                $root = $root . 'cache_' . substr($hash, 0, $i + 1) . '/';
            }             
        }
        return $root;
    }
    
    public function testSaveCorrectCallCrc32()
    {
        $this->_testSaveCorrectCall(array('readControl' => true, 'readControlType' => 'crc32'));
    }
    
    public function testSaveCorrectCallMd5()
    {
        $this->_testSaveCorrectCall(array('readControl' => true, 'readControlType' => 'md5'));
    }
    
    public function testSaveCorrectCallStrlen()
    {
        $this->_testSaveCorrectCall(array('readControl' => true, 'readControlType' => 'strlen'));
    }
    
    public function testSaveCorrectCallNoReadControl()
    {
        $this->_testSaveCorrectCall(array('readControl' => false));       
    }
    
    public function testSaveCorrectCallNoFileLocking()
    {
        $this->_testSaveCorrectCall(array('fileLocking' => false));       
    }
    
    public function testSaveCorrectCallHashedDirectoryLevel()
    {
        $this->_testSaveCorrectCall(array('hashedDirectoryLevel' => 3), 3);
    }
    
    private function _testSaveCorrectCall($options = array(), $hashed = 0)
    {
        while (list($name, $value) = each($options)) {
            $this->_instance->setOption($name, $value);
        }
        $result = $this->_instance->save('data to cache', 'foo', array('tag1', 'tag2'));
        $this->assertTrue($result);
        $this->_testCacheFiles('foo', array('tag1', 'tag2'), $hashed);
    }
    
    public function testTestCorrectCall1()
    {
        $res = $this->_instance->test('bar');
        if (!$res) {
            $this->fail('test() return false');
        }
        if (!($res > 999999)) {
            $this->fail('test() return an incorrect integer');
        }
        return;
    }
    
    public function testTestCorrectCall2() 
    {    
        $this->assertFalse($this->_instance->test('barbar'));       
    }
    
    public function testTestCorrectCall3()
    {
        $this->_instance->setDirectives(array('lifeTime' => null));
        $res = $this->_instance->test('bar');
        if (!$res) {
            $this->fail('test() return false');
        }
        if (!($res > 999999)) {
            $this->fail('test() return an incorrect integer');
        }
        return;
    }
    
    public function testRemoveCorrectCall1()
    {
        $this->assertTrue($this->_instance->remove('bar'));
        $this->assertFalse($this->_instance->test('bar'));
        $this->_testCacheFiles('bar', array('tag3', 'tag4'), 0, true);
    }
    
    public function testRemoveCorrectCall2()
    {
        $this->assertFalse($this->_instance->remove('barbar'));
    }
    
    public function testRemoveCorrectCall3()
    {
        $this->_instance->setOption('hashedDirectoryLevel', 3);
        $this->_instance->save('foobar', 'barbar', array('tag1', 'tag2'));
        $this->assertTrue($this->_instance->remove('barbar'));
        $this->assertFalse($this->_instance->test('barbar'));
        $this->_testCacheFiles('barbar', array('tag1', 'tag2'), 3, true);
    }
       
    public function testGetCorrectCall1()
    {
        // existing file
        $result = $this->_instance->get('bar');
        $this->assertEquals('bar : data to cache', $result);
    }
    
    public function testGetCorrectCall2()
    {
        // non existing file
        $result = $this->_instance->get('barbar');
        $this->assertFalse($result);
    }
    
    public function testGetCorrectCall3()
    {
        // existing file but too old 
        touch($this->_path('cache_bar') . 'cache_bar', strtotime('23 April 2005'));
        $result = $this->_instance->get('bar');
        $this->assertFalse($result);
    }
    
    public function testGetCorrectCall4()
    {
        // existing file but too old 
        touch($this->_path('cache_bar') . 'cache_bar', strtotime('23 April 2005'));
        $result = $this->_instance->get('bar', true);
        $this->assertEquals('bar : data to cache', $result);
    }
    
    public function testGetCorrectCall5()
    {
        // existing file but too old (and lifeTime = null)
        touch($this->_path('cache_bar') . 'cache_bar', strtotime('23 April 2005'));
        $this->_instance->setDirectives(array('lifeTime' => null));
        $result = $this->_instance->get('bar');
        $this->assertEquals('bar : data to cache', $result);
    }
    
    public function testGetCorrectCall6()
    {
        // non existing file but and lifeTime = null
        $this->_instance->setDirectives(array('lifeTime' => null));
        $result = $this->_instance->get('barbar');
        $this->assertFalse($result);
    }
    
    public function testGetCorrectCall7()
    {
        // no read control
        $this->_instance->setOption('readControl', false);
        $this->_instance->save('foo', 'barbar');
        $result = $this->_instance->get('barbar');
        $this->assertEquals('foo', $result);
    }
        
    public function testGetCorrectCall8()
    {
        // nothing in cache, read control = true
        $this->_instance->setOption('readControl', true);
        $this->_instance->save('', 'barbar');
        $result = $this->_instance->get('barbar');
        $this->assertEquals('', $result);
    }
    
    public function testGetCorrectCall9()
    {
        // nothing in cache, read control = false
        $this->_instance->setOption('readControl', false);
        $this->_instance->save('', 'barbar');
        $result = $this->_instance->get('barbar');
        $this->assertEquals('', $result);
    }
    
    public function testGetCorrectCallWithReadControlProblem()
    {
        // simulate a cache corruption
        $fp = fopen($this->_path('cache_bar') . 'cache_bar', 'a');
        fwrite($fp, 'foo');
        fclose($fp);
        $result = $this->_instance->get('bar');
        $this->assertFalse($result);
    }
    
    public function testCleanAllCorrectCall1()
    {
        $this->_instance->setOption('hashedDirectoryLevel', 0); 
        for ($i = 1 ; $i<10 ; $i++) {
            $this->_instance->save('data to cache', "foo_$i");
        }
        $this->assertTrue($this->_instance->clean('all'));
        for ($i = 1 ; $i<10 ; $i++) {
            $this->assertFalse($this->_instance->test("foo_$i"));
        }
    }
    
    public function testCleanAllCorrectCall2()
    {
        $this->_instance->setOption('hashedDirectoryLevel', 3); 
        for ($i = 1 ; $i<10 ; $i++) {
            $this->_instance->save('data to cache', "foo_$i");
        }
        $this->assertTrue($this->_instance->clean('all'));
        for ($i = 1 ; $i<10 ; $i++) {
            $this->assertFalse($this->_instance->test("foo_$i"));
        }
    }
    
    public function testCleanOldCorrectCall()
    {
        for ($i = 1 ; $i<10 ; $i++) {
            $this->_instance->save('data to cache', "foo_$i");
        }
        for ($i = 1 ; $i<5 ; $i++) {
            touch($this->_path("cache_foo_$i") . "cache_foo_$i", strtotime('23 April 2005'));
        }
        $this->assertTrue($this->_instance->clean('old'));
        for ($i = 1 ; $i<10 ; $i++) {
            if ($i<5) {
                $this->assertFalse(file_exists($this->_path("cache_foo_$i") . "cache_foo_$i"));
            } else {
                $res = $this->_instance->test("foo_$i");
                if (!$res) $this->fail("no cache for foo_$i");
            }
        }
    }
    
    public function testCleanMatchingTagCorrectCall()
    {
        for ($i = 1 ; $i<5 ; $i++) {
            $this->_instance->save('data to cache', "foo_$i", array('tag1', 'tag2', 'tag3'));
        }
        for ($i = 5 ; $i<10 ; $i++) {
            $this->_instance->save('data to cache', "foo_$i", array('tag2', 'tag3'));
        }
        for ($i = 10 ; $i<15 ; $i++) {
            $this->_instance->save('data to cache', "foo_$i", array('tag1', 'tag3'));
        }
        for ($i = 15 ; $i<20 ; $i++) {
            $this->_instance->save('data to cache', "foo_$i", array('tag3'));
        }
        $this->assertTrue($this->_instance->clean('matchingTag', array('tag1', 'tag2')));
        for ($i = 1 ; $i<20 ; $i++) {
            if ($i<5) {
                $this->assertFalse($this->_instance->test("foo_$i"));    
            } else {
                $res = $this->_instance->test("foo_$i");
                if (!$res) $this->fail("no cache for foo_$i");
            }
        }
    }
    
    public function testCleanNotMatchingTagCorrectCall()
    {
        for ($i = 1 ; $i<5 ; $i++) {
            $this->_instance->save('data to cache', "foo_$i", array('tag1', 'tag2', 'tag3'));
        }
        for ($i = 5 ; $i<10 ; $i++) {
            $this->_instance->save('data to cache', "foo_$i", array('tag2', 'tag3'));
        }
        for ($i = 10 ; $i<15 ; $i++) {
            $this->_instance->save('data to cache', "foo_$i", array('tag1', 'tag3'));
        }
        for ($i = 15 ; $i<20 ; $i++) {
            $this->_instance->save('data to cache', "foo_$i", array('tag3'));
        }
        $this->assertTrue($this->_instance->clean('notMatchingTag', array('tag1', 'tag2')));
        for ($i = 1 ; $i<20 ; $i++) {
            if ($i<15) {
                $res = $this->_instance->test("foo_$i");
                if (!$res) $this->fail("no cache for foo_$i");   
            } else {
                $res = $this->_instance->test("foo_$i");  
                $this->assertFalse($res);
            }
        }
    }
    
    
}

?>
