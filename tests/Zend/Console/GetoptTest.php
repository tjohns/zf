<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id$
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Zend_Console_Getopt
 */
require_once 'Zend/Console/Getopt.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @category   Zend
 * @package    Zend_Console_Getopt
 * @subpackage UnitTests
 */
class Zend_Console_GetoptTest extends PHPUnit_Framework_TestCase
{
    public function testShortOptionsGnuMode()
    {
        $opts = new Zend_Console_Getopt('abp:', array('-a', '-p', 'p_arg'));
        $this->assertEquals(true, $opts->a);
        $this->assertNull(@$opts->b);
        $this->assertEquals($opts->p, 'p_arg');
    }

    public function testLongOptionsZendMode()
    {
        $opts = new Zend_Console_Getopt(array(
                'apple|a' => 'Apple option',
                'banana|b' => 'Banana option',
                'pear|p=s' => 'Pear option'
            ),
            array('-a', '-p', 'p_arg'));
        $this->assertTrue($opts->apple);
        $this->assertNull(@$opts->banana);
        $this->assertEquals($opts->pear, 'p_arg');
    }

    public function testZendModeEqualsParam()
    {
        $opts = new Zend_Console_Getopt(array(
                'apple|a' => 'Apple option',
                'banana|b' => 'Banana option',
                'pear|p=s' => 'Pear option'
            ),
            array('--pear=pear.phpunit.de'));
        $this->assertEquals($opts->pear, 'pear.phpunit.de');
    }

    public function testToString()
    {
        $opts = new Zend_Console_Getopt('abp:', array('-a', '-p', 'p_arg'));
        $this->assertEquals($opts->__toString(), 'a=true p=p_arg');
    }

    public function testDumpString()
    {
        $opts = new Zend_Console_Getopt('abp:', array('-a', '-p', 'p_arg'));
        $this->assertEquals($opts->toString(), 'a=true p=p_arg');
    }

    public function testDumpArray()
    {
        $opts = new Zend_Console_Getopt('abp:', array('-a', '-p', 'p_arg'));
        $this->assertEquals(implode(',', $opts->toArray()), 'a,p,p_arg');
    }

    public function testDumpJson()
    {
        $opts = new Zend_Console_Getopt('abp:', array('-a', '-p', 'p_arg'));
        $this->assertEquals($opts->toJson(),
            '{"options":[{"option":{"flag":"a","parameter":true}},{"option":{"flag":"p","parameter":"p_arg"}}]}');

    }

    public function testDumpXml()
    {
        $opts = new Zend_Console_Getopt('abp:', array('-a', '-p', 'p_arg'));
        $this->assertEquals($opts->toXml(),
            "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<options><option flag=\"a\"/><option flag=\"p\" parameter=\"p_arg\"/></options>\n");
    }

    public function testExceptionForMissingFlag()
    {
        try {
            $opts = new Zend_Console_Getopt(array('|a'=>'Apple option'));
        } catch (Zend_Console_Getopt_Exception $e) {
            $this->assertEquals($e->getMessage(),
                'Blank flag not allowed in rule "|a".');
        }
    }

    public function testExceptionForDuplicateFlag()
    {
        try {
            $opts = new Zend_Console_Getopt(
                array('apple|apple'=>'apple-option'));
        } catch (Zend_Console_Getopt_Exception $e) {
            $this->assertEquals($e->getMessage(),
                'Option "--apple" is being defined more than once.');
        }

        try {
            $opts = new Zend_Console_Getopt(
                array('a'=>'Apple option', 'apple|a'=>'Apple option'));
        } catch (Zend_Console_Getopt_Exception $e) {
            $this->assertEquals($e->getMessage(),
                'Option "-a" is being defined more than once.');
        }
    }

    public function testAddRules()
    {
        $opts = new Zend_Console_Getopt(
            array(
                'apple|a' => 'Apple option',
                'banana|b' => 'Banana option'
            ),
            array('--pear', 'pear_param'));
        try {
            $opts->parse();
        } catch (Zend_Console_Getopt_Exception $e) {
            $this->assertEquals($e->getMessage(), 'Option "pear" is not recognized.');
        }
        $opts->addRules(array('pear|p=s' => 'Pear option'));
        $this->assertEquals($opts->pear, 'pear_param');
    }

    public function testMissingParameter()
    {
        $opts = new Zend_Console_Getopt(
            array(
                'apple|a=s' => 'Apple with required parameter',
                'banana|b' => 'Banana'
            ),
            array('--apple'));
        try {
            $opts->parse();
        } catch (Zend_Console_Getopt_Exception $e) {
            $this->assertEquals($e->getMessage(), 'Option "apple" requires a parameter.');
        }
    }

    public function testOptionalParameter()
    {
        $opts = new Zend_Console_Getopt(
            array(
                'apple|a-s' => 'Apple with optional parameter',
                'banana|b' => 'Banana'
            ),
            array('--apple', '--banana'));
        $this->assertTrue($opts->apple);
        $this->assertTrue($opts->banana);
    }

    public function testIgnoreCaseGnuMode()
    {
        $opts = new Zend_Console_Getopt('aB', array('-A', '-b'),
            array(Zend_Console_Getopt::CONFIG_IGNORECASE => true));
        $this->assertEquals(true, $opts->a);
        $this->assertEquals(true, $opts->B);
    }

    public function testIgnoreCaseZendMode()
    {
        $opts = new Zend_Console_Getopt(
            array(
                'apple|a' => 'Apple-option',
                'Banana|B' => 'Banana-option'
            ),
            array('--Apple', '--bAnaNa'),
            array(Zend_Console_Getopt::CONFIG_IGNORECASE => true));
        $this->assertEquals(true, $opts->apple);
        $this->assertEquals(true, $opts->BANANA);
    }

    public function testIsSet()
    {
        $opts = new Zend_Console_Getopt('ab', array('-a'));
        $this->assertTrue(isset($opts->a));
        $this->assertFalse(isset($opts->b));
    }

    public function testSet()
    {
        $opts = new Zend_Console_Getopt('ab', array('-a'));
        $this->assertFalse(isset($opts->b));
        $opts->b = true;
        $this->assertTrue(isset($opts->b));
    }

    public function testUnSet()
    {
        $opts = new Zend_Console_Getopt('ab', array('-a'));
        $this->assertTrue(isset($opts->a));
        unset($opts->a);
        $this->assertFalse(isset($opts->a));
    }

    public function testAddArguments()
    {
        $opts = new Zend_Console_Getopt('abp:', array('-a'));
        $this->assertNull(@$opts->p);
        $opts->addArguments(array('-p', 'p_arg'));
        $this->assertEquals($opts->p, 'p_arg');
    }

    public function testRemainingArgs()
    {
        $opts = new Zend_Console_Getopt('abp:', array('-a', '--', 'file1', 'file2'));
        $this->assertEquals(implode(',', $opts->getRemainingArgs()), 'file1,file2');
        $opts = new Zend_Console_Getopt('abp:', array('-a', 'file1', 'file2'));
        $this->assertEquals(implode(',', $opts->getRemainingArgs()), 'file1,file2');
    }

    public function testDashDashFalse()
    {
        try {
            $opts = new Zend_Console_Getopt('abp:', array('-a', '--', '--fakeflag'),
                array(Zend_Console_Getopt::CONFIG_DASHDASH => false));
        } catch (Zend_Console_Getopt_Exception $e) {
            $this->assertEquals($e->getMessage(), 'Option "fakeflag" is not recognized.');
        }
    }

    public function testGetOptions()
    {
        $opts = new Zend_Console_Getopt('abp:', array('-a', '-p', 'p_arg'));
        $this->assertEquals(implode(',', $opts->getOptions()), 'a,p');
    }

    public function testGetUsageMessage()
    {
        $opts = new Zend_Console_Getopt('abp:', array('-x'));
        $message = preg_replace('/Usage: .* \[ options \]/',
            'Usage: <progname> [ options ]',
            $opts->getUsageMessage());
        $message = preg_replace('/ /', '_', $message);
        $this->assertEquals($message,
            "Usage:_<progname>_[_options_]\n-a___________________\n-b___________________\n-p_<string>__________\n");
    }

    public function testUsageMessageFromException()
    {
        try {
            $opts = new Zend_Console_Getopt(array(
                'apple|a-s' => 'apple',
                'banana1|banana2|banana3|banana4' => 'banana',
                'pear=s' => 'pear'),
                array('-x'));
            $opts->parse();
        } catch (Zend_Console_Getopt_Exception $e) {
            $message = preg_replace('/Usage: .* \[ options \]/',
                'Usage: <progname> [ options ]',
                $e->getUsageMessage());
            $message = preg_replace('/ /', '_', $message);
            $this->assertEquals($message,
                "Usage:_<progname>_[_options_]\n--apple|-a_[_<string>_]_________________apple\n--banana1|--banana2|--banana3|--banana4_banana\n--pear_<string>_________________________pear\n");

        }
    }

    public function testSetAliases()
    {
        $opts = new Zend_Console_Getopt('abp:', array('--apple'));
        $opts->setAliases(array('a' => 'apple'));
        $this->assertTrue($opts->a);
    }

    public function testSetAliasesIgnoreCase()
    {
        $opts = new Zend_Console_Getopt('abp:', array('--apple'), 
            array(Zend_Console_Getopt::CONFIG_IGNORECASE => true));
        $opts->setAliases(array('a' => 'APPLE'));
        $this->assertTrue($opts->apple);
    }

    public function testSetAliasesWithNamingConflict()
    {
        $opts = new Zend_Console_Getopt('abp:', array('--apple'));
        $opts->setAliases(array('a' => 'apple'));
        try {
            $opts->setAliases(array('b' => 'apple'));
        } catch(Zend_Console_Getopt_Exception $e) {
            $this->assertEquals($e->getMessage(), 'Option "--apple" is being defined more than once.');
        }
    }

    public function testSetHelp()
    {
        $opts = new Zend_Console_Getopt('abp:', array('-a'));
        $opts->setHelp(array(
            'a' => 'apple',
            'b' => 'banana',
            'p' => 'pear'));
        $message = preg_replace('/Usage: .* \[ options \]/',
            'Usage: <progname> [ options ]',
            $opts->getUsageMessage());
        $message = preg_replace('/ /', '_', $message);
        $this->assertEquals($message, 
            "Usage:_<progname>_[_options_]\n-a___________________apple\n-b___________________banana\n-p_<string>__________pear\n");

    }

    public function testCheckParameterType()
    {
        $opts = new Zend_Console_Getopt(array(
            'apple|a=i' => 'apple with integer',
            'banana|b=w' => 'banana with word',
            'pear|p=s' => 'pear with string',
            'orange|o-i' => 'orange with optional integer',
            'lemon|l-w' => 'lemon with optional word',
            'kumquat|k-s' => 'kumquat with optional string'));

        $opts->setArguments(array('-a', 327));
        $opts->parse();
        $this->assertEquals(327, $opts->a);

        $opts->setArguments(array('-a', 'noninteger'));
        try {
            $opts->parse();
        } catch (Zend_Console_Getopt_Exception $e) {
            $this->assertEquals($e->getMessage(), 'Option "apple" requires an integer parameter, but was given "noninteger".');
        }

        $opts->setArguments(array('-b', 'word'));
        $this->assertEquals('word', $opts->b);

        $opts->setArguments(array('-b', 'two words'));
        try {
            $opts->parse();
        } catch (Zend_Console_Getopt_Exception $e) {
            $this->assertEquals($e->getMessage(), 'Option "banana" requires a single-word parameter, but was given "two words".');
        }

        $opts->setArguments(array('-p', 'string'));
        $this->assertEquals('string', $opts->p);

        $opts->setArguments(array('-o', 327));
        $this->assertEquals(327, $opts->o);

        $opts->setArguments(array('-o'));
        $this->assertTrue($opts->o);

        $opts->setArguments(array('-l', 'word'));
        $this->assertEquals('word', $opts->l);

        $opts->setArguments(array('-k', 'string'));
        $this->assertEquals('string', $opts->k);

    }

}
