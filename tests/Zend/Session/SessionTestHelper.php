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
 * @package    Zend_Session_TestHelper
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

// http://en.wikipedia.org/wiki/White_box_testing

require_once 'PathHelper.php';
require_once 'Zend/Session.php';

if ($argc < 2) {
    echo "usage: $argv[0] <test name>\n";
    exit;
}

error_reporting ( E_ALL | E_STRICT );

class Zend_Session_TestHelper extends Zend_Session_PathHelper
{

    /*
     * which test do we run (corresponds to test* function in this class)
     */
    private $test;

    public function __construct($argv)
    {
        //$test = empty($_GET['test']) ? '' : substr(preg_replace('/[^a-zA-Z0-9_]/', '', $_GET['test']),0,32);
        $test = empty($argv[1]) ? '' : substr(preg_replace('/[^a-zA-Z0-9_]/', '', $argv[1]),0,32);
        $this->test = '_ZF_'.$test;
        if (strlen($this->test) > 4) {
            if (method_exists($this, $this->test)) {
                #echo "Found: \$this->test={$this->test}\n";
                array_shift($argv);
                array_shift($argv);
                $this->run($argv);
                exit;
            }
        }
        echo "INVALID: test=", htmlspecialchars($test);
        exit;
    }

    public function run($argv)
    {
        #echo "run({$this->test});\n";
        $this->{$this->test}($argv);
    }

    public function _ZF_testing()
    {
        echo "PASS";
    }

    public function _ZF_expireAll($args)
    {
        Zend_Session::setOptions(array('remember_me_seconds' => 15, 'gc_probability' => 2));
        session_id($args[0]);
        if (isset($args[1]) && !empty($args[1])) {
            $s = new Zend_Session_Namespace($args[1]);
        }
        else {
            $s = new Zend_Session_Namespace();
        }
        $result = '';
        foreach ($s->getIterator() as $key => $val) {
            $result .= "$key === $val;";
        }
        Zend_Session::expireSessionCookie();
        Zend_Session::writeClose();
        echo $result;
    }

    public function _ZF_setArray($args)
    {
        $GLOBALS['fpc'] = 'set';
        session_id($args[0]);
        $s = new Zend_Session_Namespace($args[1]);
        array_shift($args);
        $s->astring = 'happy';

        // works, even for broken versions of PHP
        // $s->someArray = array( & $args ) ;
        // $args['OOOOOOOOOOOOOOOO'] = 'YYYYYYYYYYYYYYYYYYYYYYYYYYYYY';

        $s->someArray = $args;
        $s->someArray['bee'] = 'honey'; // Repeating this line twice "solves" the problem for some versions of PHP,
        $s->someArray['bee'] = 'honey'; // but PHP 5.2.1 has the real fix for ZF-800.
        $s->someArray['ant'] = 'sugar';
        $s->someArray['dog'] = 'cat';
        // file_put_contents('out.sessiontest.set', (str_replace(array("\n", ' '),array(';',''), print_r($_SESSION, true))) );
        $s->serializedArray = serialize($args);

        $result = '';
        foreach ($s->getIterator() as $key => $val) {
            $result .= "$key === ". (print_r($val,true)) .';';
        }

        Zend_Session::writeClose();
    }

    public function _ZF_getArray($args)
    {
        $GLOBALS['fpc'] = 'get';
        session_id($args[0]);
        if (isset($args[1]) && !empty($args[1])) {
            $s = new Zend_Session_Namespace($args[1]);
        }
        else {
            $s = new Zend_Session_Namespace();
        }
        $result = '';
        foreach ($s->getIterator() as $key => $val) {
            $result .= "$key === ". (str_replace(array("\n", ' '),array(';',''), print_r($val, true))) .';';
        }
        // file_put_contents('out.sesstiontest.get', print_r($s->someArray, true));
        Zend_Session::writeClose();
        echo $result;
    }
} 

$testHelper = new Zend_Session_TestHelper($argv);
