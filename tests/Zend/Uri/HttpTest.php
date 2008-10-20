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
 * @package    Zend_Uri
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Test helper
 */
require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

/**
 * @see Zend_Uri
 */
require_once 'Zend/Uri.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @category   Zend
 * @package    Zend_Uri
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Uri_HttpTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tests for proper URI decomposition
     */
    public function testSimple()
    {
        $this->_testValidUri('http://www.zend.com');
    }

    /**
     * Test that fromString() works proprerly for simple valid URLs
     *
     */
    public function testSimpleFromString()
    {
        $tests = array(
            'http://www.zend.com',
            'https://www.zend.com',
            'http://www.zend.com/path',
            'http://www.zend.com/path?query=value'
        );

        foreach ($tests as $uri) {
            $obj = Zend_Uri_Http::fromString($uri);
            $this->assertEquals($uri, $obj->getUri(), 
                "getUri() returned value that differs from input for $uri");
        }
    }
    
    /**
     * Make sure an exception is thrown when trying to use fromString() with a
     * non-HTTP scheme
     * 
     * @see http://framework.zend.com/issues/browse/ZF-4395
     * 
     * @expectedException Zend_Uri_Exception
     */
    public function testFromStringInvalidScheme()
    {
       Zend_Uri_Http::fromString('ftp://example.com/file');
    }

    public function testAllParts()
    {
        $this->_testValidUri('http://andi:password@www.zend.com:8080/path/to/file?a=1&b=2#top');
    }

    public function testUsernamePortPathQueryFragment()
    {
        $this->_testValidUri('http://andi@www.zend.com:8080/path/to/file?a=1&b=2#top');
    }

    public function testPortPathQueryFragment()
    {
        $this->_testValidUri('http://www.zend.com:8080/path/to/file?a=1&b=2#top');
    }

    public function testPathQueryFragment()
    {
        $this->_testValidUri('http://www.zend.com/path/to/file?a=1&b=2#top');
    }

    public function testQueryFragment()
    {
        $this->_testValidUri('http://www.zend.com/?a=1&b=2#top');
    }

    public function testFragment()
    {
        $this->_testValidUri('http://www.zend.com/#top');
    }

    public function testUsernamePassword()
    {
        $this->_testValidUri('http://andi:password@www.zend.com');
    }

    public function testUsernamePasswordColon()
    {
        $this->_testValidUri('http://an:di:password@www.zend.com');
    }

    public function testUsernamePasswordValidCharacters()
    {
        $this->_testValidUri('http://a_.!~*\'(-)n0123Di%25%26:pass;:&=+$,word@www.zend.com');
    }

    public function testUsernameInvalidCharacter()
    {
        $this->_testInvalidUri('http://an`di:password@www.zend.com');
    }

    public function testNoUsernamePassword()
    {
        $this->_testInvalidUri('http://:password@www.zend.com');
    }

    public function testPasswordInvalidCharacter()
    {
        $this->_testInvalidUri('http://andi:pass%word@www.zend.com');
    }

    public function testHostAsIP()
    {
        $this->_testValidUri('http://127.0.0.1');
    }

    public function testLocalhost()
    {
        $this->_testValidUri('http://localhost');
    }

    public function testLocalhostLocaldomain()
    {
        $this->_testValidUri('http://localhost.localdomain');
    }

    public function testSquareBrackets()
    {
        $this->_testValidUri('https://example.com/foo/?var[]=1&var[]=2&some[thing]=3');
    }

    /**
     * Ensures that successive slashes are considered valid
     *
     * @return void
     */
    public function testSuccessiveSlashes()
    {
        $this->_testValidUri('http://example.com//');
        $this->_testValidUri('http://example.com///');
        $this->_testValidUri('http://example.com/foo//');
        $this->_testValidUri('http://example.com/foo///');
        $this->_testValidUri('http://example.com/foo//bar');
        $this->_testValidUri('http://example.com/foo///bar');
        $this->_testValidUri('http://example.com/foo//bar/baz//fob/');
    }

    /**
     * Test that setQuery() can handle unencoded query parameters (as other
     * browsers do), ZF-1934
     *
     * @link   http://framework.zend.com/issues/browse/ZF-1934
     * @return void
     */
    public function testUnencodedQueryParameters()
    {
         $uri = Zend_Uri::factory('http://foo.com/bar');

         // First, make sure no exceptions are thrown
         try {
             $uri->setQuery('id=123&url=http://example.com/?bar=foo baz');
         } catch (Exception $e) {
             $this->fail('setQuery() was expected to handle unencoded parameters, but failed');
         }

         // Second, make sure the query string was properly encoded
         $parts = parse_url($uri->getUri());
         $this->assertEquals('id=123&url=http%3A%2F%2Fexample.com%2F%3Fbar%3Dfoo+baz', $parts['query']);
    }

    /**
     * Test that unwise characters in the query string are not valid
     *
     */
    public function testExceptionUnwiseQueryString()
    {
        $unwise = array(
            'http://example.com/?q={',
            'http://example.com/?q=}',
            'http://example.com/?q=|',
            'http://example.com/?q=\\',
            'http://example.com/?q=^',
            'http://example.com/?q=`',
        ); 
        
        foreach ($unwise as $uri) {
            $this->assertFalse(Zend_Uri::check($uri), "failed for URI $uri");
        }
    }
    
    /**
     * Test that after setting 'allow_unwise' to true unwise characters are
     * accepted
     *
     */
    public function testAllowUnwiseQueryString()
    {
        $unwise = array(
            'http://example.com/?q={',
            'http://example.com/?q=}',
            'http://example.com/?q=|',
            'http://example.com/?q=\\',
            'http://example.com/?q=^',
            'http://example.com/?q=`',
        ); 
        
        Zend_Uri::setConfig(array('allow_unwise' => true));
        
        foreach ($unwise as $uri) {
            $this->assertTrue(Zend_Uri::check($uri), "failed for URI $uri");
        }
        
        Zend_Uri::setConfig(array('allow_unwise' => false));
    }
    
    /**
     * Test that an extremely long URI does not break things up
     * 
     * @link http://framework.zend.com/issues/browse/ZF-3712
     */
    public function testVeryLongUriZF3712()
    {
        $uri = 'http://localhost:4444/selenium-server/driver/?cmd=type&1=ban' .
               'ner_code&2=if+%28+typeof%28+globalseed+%29+%3D%3D+%22undefin' .
               'ed%22%29+%7B%0A%09var+globalseed++%3D+Math.round%28Math.rand' .
               'om%28%29%2A65535%29%3B+%0A%7D%0Avar+haveFlash1234562404000+%' .
               '3D+false%3B%0Afunction+CAWBrowser%28%29%7B%0A%09var+ua+%3D+n' .
               'avigator.userAgent%3B%0A%09this.msie+%3D+%28ua+%26%26+%28+pa' .
               'rseFloat%28+navigator.appVersion+%29++%3E%3D4+%29+%26%26+%28' .
               '+ua.indexOf%28%22Opera%22%29+%3C+0+%29+%26%26+%28+ua.indexOf' .
               '%28%22MSIE+4%22%29+%3C+0+%29+%26%26+%28+ua.indexOf%28+%22MSI' . 
               'E%22+%29+%3E%3D0%29+%29%3B%0A%09this.win+%3D+%28ua+%26%26+%2' . 
               '8%28ua.indexOf%28+%22Windows+95%22+%29+%3E%3D0%29+%7C%7C+%28' . 
               'ua.indexOf%28%22Windows+NT%22%29+%3E%3D0+%29+%7C%7C+%28ua.in' . 
               'dexOf%28%22Windows+98%22%29+%3E%3D0%29+%29+%29%3B%0A%09this.' . 
               'mac+%3D+%28navigator.platform+%26%26+%28navigator.platform.i' . 
               'ndexOf%28%27Mac%27%29%21%3D-1%29%29%3B%0A%09this.opera7+%3D+' . 
               '%28%28ua.indexOf%28%27Opera%27%29+%21%3D+-1%29+%26%26+window' . 
               '.opera+%26%26+document.readyState%29+%3F+1+%3A+0%3B%0A%09thi' . 
               's.gecko+++%3D+%28ua.toLowerCase%28%29.indexOf%28%27gecko%27%' . 
               '29+%21%3D+-1%29+%26%26+%28ua.indexOf%28%27safari%27%29+%3D%3' . 
               'D+-1%29%3B%0A%09haveFlash1234562404000+%3D+%28navigator.mime' . 
               'Types+%26%26+navigator.mimeTypes%5B%22application%2Fx-shockw' . 
               'ave-flash%22%5D%29+%3F+navigator.mimeTypes%5B%22application%' . 
               '2Fx-shockwave-flash%22%5D.enabledPlugin+%3A+0%3B%0A%09if%28+' . 
               'haveFlash1234562404000+%29%7B%0A%09%09haveFlash1234562404000' . 
               '+%3D+%28parseInt%28haveFlash1234562404000.description.substr' . 
               'ing%28haveFlash1234562404000.description.indexOf%28%22.%22%2' . 
               '9-1%29%29%3E%3D6%29%3B%0A%09%7Delse+if+%28+this.msie+%26%26+' . 
               'this.win+%26%26+%21this.mac%29%7B%0A%09%09document.write%28%' . 
               '27%3CSCR%27+%2B+%27IPT+LANGUAGE%3DVBScript%5C%3E+%5Cn%27%0A%' . 
               '09%09%09%2B+%27+on+error+resume+next+%5Cn%27%0A%09%09%09%2B+' . 
               '%27+haveFlash1234562404000+%3D+%28IsObject%28CreateObject%28' . 
               '%22ShockwaveFlash.ShockwaveFlash.6%22%29%29%29%5Cn%27%0A%09%' . 
               '09%09%2B+%27%3C%2FSCR%27+%2B+%27IPT%5C%3E+%5Cn%27+%29%3B%0A%' . 
               '09%7D%0A%09%0A%09this.other+%3D+%21%28+%28this.gecko+%7C%7C+' . 
               'this.msie%29+%26%26+this.win+%26%26+%21this.mac%29%3B%0A%09t' . 
               'his.desc+%3D++this.msie+%3F+%22msie+%22+%3A+this.gecko+%3F+%' . 
               '22gecko+%22+%3A+this.opera7+%3F+%22opera7%28other%29+%22+%3A' . 
               '+this.other%3F+%22other%22+%3A+%22ukn%22%3B%0A%09this.flash+' . 
               '%3D+haveFlash1234562404000+%3F+6+%3A+0%3B%09%0A%7D%0Afunctio' . 
               'n+CAWCode1234562404000%28%29%7B%0A%09this.server+%3D+%22engi' . 
               'ne.awaps.net%22%3B+%0A%09this.section+%3D+123456%3B%0A%09thi' . 
               's.adw_w+%3D+240%3B%0A%09this.adw_h+%3D+400%3B%0A%09this.wh+%' . 
               '3D+%22240400%22%3B%0A%09this.subsection+%3D+0%3B%0A%09this.u' . 
               'niq+%3D+%221234562404000%22%3B%0A%09this.html_code+%3D+%22%2' . 
               '2%3B%0A%09this.adw_a+%3D+0%3B%0A%09this.adw_p+%3D+0%3B%0A%09' . 
               'this.i_type+%3D+0%3B%0A%09this.seed+%3D+Math.round%28Math.ra' . 
               'ndom%28%29%2A65535%29%3B%0A%09this.jsinfo_onload+%3D+functio' . 
               'n%28t%2C+a%2C+p%2C+cmd%29%7B%0A%09++++++++var+d%3Ddocument%3' . 
               'B%0A%09%09this.adw_a+%3D+a%3B%0A%09%09this.adw_p+%3D+p%3B%0A' . 
               '%0A%09%09if+%28+this.adw_a+%3D%3D+0+%7C%7C+cmd+%3D%3D+2+%29%' . 
               '7B%0A%09%09%09if+%28%21aw_br.other%29%7B%0A%09%09%09%09aw_tb' . 
               '+%3D+d.getElementById%28%27aw_tb%27+%2B+this.uniq%29%3B%09%0' . 
               '9%0A%09%09%09%09aw_tb.style.display+%3D+%27none%27%3B%0A%09%' . 
               '09%09%7D%0A++++++++%09%09return%3B%0A%09%09%7D%0A%0A%09%09if' . 
               '+%28+cmd+%3D%3D+1%29%7B%0A%09%09%09switch+%28+t+%29%7B%0A%09' . 
               '%09%09%09case+1%3A+this.writeGIF%28%29%3B+break%3B%0A%09%09%' . 
               '09%09case+2%3A+this.writeHTM%28%29%3B+break%3B%0A%09%09%09%0' .
               '9case+3%3A+this.writeSWF%28%29%3B+break%3B%0A%09%09%09%09cas' .
               'e+4%3A%09this.writeJS%28%29%3B+break%3B%0A%09%09%09%09case+2' .
               '4%3A+this.writeJS%28%29%3B+break%3B%0A%09%09%09%09case+5%3A%' .
               '09this.writeCustom%28%29%3B+return%3B+break%3B%0A%09%09%09%0' .
               '9default%3A+this.writeDef%28+97+%29%3B+return%3B+break%3B%0A' .
               '%09%09%09%7D%0A%09%0A%09%09%09if%28+aw_br.other+%29%7B%0A%09' .
               '%09%09%09d.write%28+this.html_code+%29%3B%09%09%0A%09%09%09%' .
               '7Delse%7B%0A%09%09%09%09aw_td+%3D+d.getElementById%28%27aw_t' .
               'd%27+%2B+this.uniq%29%3B%09%09%09%0A%09%09%09%09aw_td.innerH' .
               'TML+%3D+this.html_code%3B%09%09%0A%09%09%09%7D%0A%09%09%7D%0' .
               'A%09%0A%09%7D%0A%09this.writeCustom+%3D+function%28%29%7B%0A' .
               '%09%09%2F%2F+emplement+custom+code%2C+like+site+default+code' .
               '%0A%09%7D%0A%09this.writeJS+%3D+function%28%29%7B%0A%09%09va' .
               'r+jssrc+%3D+%27http%3A%2F%2F%27+%2B+this.server+%2B+%27%2F0%' .
               '2F%27+%2B+this.section+%2B+%27%2F%27+%2B+this.wh+%2B+%27.htm' .
               '%3F0-0-%27+%2B+this.seed+%2B+%27-0-la%3A%27+%2B+this.adw_a+%' .
               '2B+%27p%3A%27+%2B+this.adw_p+%2B+%27%26subsection%3D%27+%2B+' .
               'this.subsection+%2B+%27%26c_uniq%3D%27+%2B+this.uniq%3B%0A%0' .
               '9%09if%28+aw_br.other+%29%7B%0A%09%09%09this.html_code+%3D+%' .
               '27%3Csc%27%2B%27ript+src%3D%22%27+%2B+jssrc+%2B%27%22%3E%3C%' .
               '2Fsc%27%2B%27ript%3E%27%3B%0A%09%09%7Delse%7B%0A%09%09%09thi' .
               's.html_code%3D%22%22%3B%0A%09%09%09var+h+%3D+document.getEle' .
               'mentsByTagName%28%27head%27%29%5B0%5D%3B%0A%09%09%09var+s+%3' .
               'D+document.createElement%28%27script%27%29%3B%0A%09%09%09s.t' .
               'ype+%3D+%27text%2Fjavascript%27%3B%0A%09%09%09s.src+%3D+jssr' .
               'c%3B%0A%09%09%09h.appendChild%28s%29%3B%0A%09%09%7D%0A%09%7D' .
               '%0A%09this.writeGIF+%3D+function%28%29%7B%0A%09%09this.html_' .
               'code+%3D++%27%3Ca+href%3Dhttp%3A%2F%2F%27+%2B+this.server+%2' .
               'B+%27%2F1%2F%27+%2B+this.section+%2B+%27%2F%27+%2B+this.wh+%' .
               '2B+%27.gif%3F0-0-%27+%2B+this.seed+%2B+%27-0-la%3A%27+%2B+th' .
               'is.adw_a+%2B+%27p%3A%27+%2B+this.adw_p+%2B+%27+target%3D_bla' .
               'nk+%3E%27%0A%09%09%09%2B+%27%3Cimg+src%3Dhttp%3A%2F%2F%27+%2' .
               'B+this.server+%2B+%27%2F0%2F%27+%2B+this.section+%2B+%27%2F%' .
               '27+%2B+this.wh+%2B+%27.gif%3F0-0-%27+%2B+this.seed+%2B+%27-0' .
               '-la%3A%27+%2B+this.adw_a+%2B+%27p%3A%27+%2B+this.adw_p+%2B+%' .
               '27%26%27+%2B+%27timestamp%3D%27+%2B+this.seed+%2B+%27%26subs' .
               'ection%3D%27+%2B+this.subsection%0A%09%09%09%2B+%27+width%3D' .
               '%27+%2B+this.adw_w+%2B+%27+height%3D%27+%2B+this.adw_h+%2B+%' .
               '27+border%3D0%3E%3C%2Fa%3E%27%3B%0A%09%7D%0A%09this.writeHTM' .
               '+%3D+function%28%29%7B%0A%09%09this.html_code+%3D++%27%3CIFR' .
               'AME++src%3Dhttp%3A%2F%2F%27+%2B+this.server+%2B+%27%2F0%2F%2' .
               '7+%2B+this.section+%2B+%27%2F%27+%2B+this.wh+%2B+%27.htm%3F0' .
               '-0-%27+%2B+this.seed+%2B+%27-0-la%3A%27+%2B+this.adw_a+%2B+%' .
               '27p%3A%27+%2B+this.adw_p+%2B+%27%26subsection%3D%27+%2B+this' .
               '.subsection%0A%09%09%09%2B+%27++width%3D%22%27+%2B+this.adw_' .
               'w+%2B+%27%22+height%3D%22%27+%2B+this.adw_h+%2B+%27%22+frame' .
               'border%3D0+marginwidth%3D%220%22+marginheight%3D%220%22+scro' .
               'lling%3D%22no%22%3E+%3C%2FIFRAME%3E%27%3B%0A%09%7D%0A%09this' .
               '.writeSWF+%3D+function%28%29%7B%0A%09%09var+c_linkNmbs+%3D+%' .
               '27%27%3B%0A%09%09for+%28var+n_linkNmb+%3D+0%3B+n_linkNmb+%3C' .
               '+6%3B+n_linkNmb%2B%2B%29+%7B%0A%09%09%09c_linkNmbs+%2B%3D+%2' .
               '8n_linkNmb+%3D%3D+0+%3F+%27%27+%3A+%27%26%27%29+%2B+%27link%' .
               '27+%2B+%28n_linkNmb+%2B+1%29+%2B+%27%3D%27+%2B+%27http%3A%2F' .
               '%2F%27+%2B+this.server+%2B+%27%2F1%2F%27+%2B+this.section+%2' .
               'B+%27%2F%27+%2B+this.wh+%2B+%27.swf%3F0-%27+%2B+n_linkNmb+%2' .
               'B+%27-%27+%2B+this.seed+%2B+%27-0-la%3A%27+%2B+this.adw_a+%2' .
               'B+%27p%3A%27+%2B+this.adw_p+%2B+%27%27%3B%0A%09%09%7D%0A%0A%' .
               '09%09var+swf_url+%3D+%27http%3A%2F%2F%27+%2B+this.server+%2B' .
               '+%27%2F0%2F%27+%2B+this.section+%2B+%27%2F%27%2B+this.wh+%2B' .
               '+%27.swf%3F0-0-%27+%2B+this.seed+%2B+%27-0-la%3A%27+%2B+this' .
               '.adw_a+%2B+%27p%3A%27+%2B+this.adw_p+%2B+%27-%26%27+%2B+%27s' .
               'ection%3D%27+%2B+this.section+%2B+%27%26subsection%3D%27+%2B' .
               '+this.subsection+%2B+%27%26%27+%2B+c_linkNmbs%3B%0A%0A%09%09' .
               'this.html_code+%3D+%27%3Cobject+classid%3Dclsid%3AD27CDB6E-A' .
               'E6D-11cf-96B8-444553540000+codebase%3Dhttp%3A%2F%2Fdownload.' .
               'macromedia.com%2Fpub%2Fshockwave%2Fcabs%2Fflash%2Fswflash.ca' .
               'b%23version%3D5%2C0%2C0%2C0+width%3D%27%0A%09%09%09%2B+this.' .
               'adw_w+%2B+%27+height%3D%27+%2B+this.adw_h+%2B+%27+%3E+%27+%2' .
               'B+%27%3Cparam+name%3Dmovie+value%3D%27+%2B+swf_url+%2B+%27+%' .
               '3E%3Cparam+name%3Dmenu+value%3Dfalse%3E%3Cparam+name%3Dquali' .
               'ty+value%3Dhigh%3E%27%0A%09%09%09%2B+%27%3CEM%27+%2B+%27BED+' .
               'src%3D%22%27+%2B+swf_url+%2B+%27%22+quality%3Dhigh+%27%0A%09' .
               '%09%09%2B+%27menu%3Dfalse+swLiveConnect%3DFALSE+WIDTH%3D%27%' .
               '2B+this.adw_w+%2B%27+HEIGHT%3D%27+%2B+this.adw_h+%0A%09%09%0' .
               '9%2B+%27+TYPE%3D%22application%2Fx-shockwave-flash%22+PLUGIN' .
               'SPAGE%3D%22http%3A%2F%2Fwww.macromedia.com%2Fshockwave%2Fdow' .
               'nload%2Findex.cgi%3FP1_Prod_Version%3DShockwaveFlash%22%3E%2' .
               '7%0A%09%09%09%2B+%27%3C%2FEMBED%3E%27%0A%09%09%09%2B+%27+%3C' .
               '%2Fobject%3E%27%3B%0A%09%7D%0A%09this.writeDef+%3D+function%' .
               '28num%29%7B%0A%09%09var+info+%3D+new+Image%28%29%3B%0A%09%09' .
               'info.src+%3D+%22http%3A%2F%2F%22+%2B+this.server+%2B+%22%2F%' .
               '22+%2B+num+%2B+%22%2F%22+%2B+this.section+%2B+%22%2F%22+%2B+' .
               'this.wh+%2B+%22.gif%3F0-0-%22+%2B+this.seed%3B%0A%09%7D%0A%0' .
               '9this.write+%3D+function%28%29%7B%09%09%0A%09%09var+jssrc+%3' .
               'D+%22http%3A%2F%2F%22+%2B+this.server+%2B+%22%2F3%2F%22+%2B+' .
               'this.section+%2B+%22%2F%22+%2B+this.wh+%2B+%22.%3F%22%0A%09%' .
               '09%09%2B+globalseed+%2B+%0A%09%09%09%22-0-%22+%2B+this.seed+' .
               '%2B+%22%26swfcode%3D6%26awcode%3D33%26subsection%3D%22+%2B+t' .
               'his.subsection+%2B+%22%26c_uniq%3D%22+%2B+this.uniq+%2B+%22%' .
               '26jsaction4%3D1%26flash%3D%22+%2B+aw_br.flash%3B%0A%09%09var' .
               '+jssrc_code+%3D+%27%3Csc%27%2B%27ript+src%3D%22%27+%2B+jssrc' .
               '+%2B%27%22%3E%3C%2Fsc%27%2B%27ript%3E%27%3B%0A%0A%09%09if+%2' .
               '8+%21aw_br.other%29%7B%0A%09%09%09document.write%28+%27%3Cta' .
               'ble+style%3D%22display%3Ainline%22+border%3D0+cellpadding%3D' .
               '0+cellspacing%3D0+id%3D%22aw_tb%27%2Bthis.uniq%2B%27%22+widt' .
               'h%3D%27+%2B+this.adw_w+%2B+%27+height%3D%27+%2B+this.adw_h+%' .
               '2B+%27%22%3E%3Ctr%3E%3Ctd+id%3D%22aw_td%27%2Bthis.uniq%2B%27' .
               '%22%3E%3C%2Ftd%3E%3C%2Ftr%3E%3C%2Ftable%3E%27+%29%3B%09%0A%0' .
               '9%09%09var+h+%3D+document.getElementsByTagName%28%27head%27%' .
               '29%5B0%5D%3B%0A%09%09%09var+s+%3D+document.createElement%28%' .
               '27scr%27%2B%27ipt%27%29%3B%0A%09%09%09s.type+%3D+%27text%2Fj' .
               'avasc%27%2B%27ript%27%3B%0A%09%09%09s.src+%3D+jssrc%3B%0A%09' .
               '%09%09h.appendChild%28s%29%3B%09%09%09%09%09%0A%09%09%7Delse' .
               '%7B+%0A%09%09%09document.write%28jssrc_code%29%3B%0A%09%09%7' .
               'D%0A%09%7D%0A%7D%0Afunction+aw_jsinfo_onload1234562404000%28' .
               't%2Ca%2Cp%2Ccmd%29%7B%0A%09code1234562404000.jsinfo_onload%2' .
               '8t%2Ca%2Cp%2Ccmd%29%3B%0A%7D%0Avar+aw_br+%3D+new+CAWBrowser%' .
               '28%29%3B%0Avar+code1234562404000+%3D+new+CAWCode123456240400' .
               '0%28%29%3B%0Acode1234562404000.write%28%29%3B&sessionId=1406' .
               '66';
        
        $this->_testValidUri($uri);
    }
    
    /**
     * Test a known valid URI
     *
     * @param string $uri
     */
    protected function _testValidUri($uri)
    {
        $obj = Zend_Uri::factory($uri);
        $this->assertEquals($uri, $obj->getUri(), 'getUri() returned value that differs from input');
    }

    /**
     * Test a known invalid URI
     *
     * @param string $uri
     */
    protected function _testInvalidUri($uri)
    {
        try {
            $obj = Zend_Uri::factory($uri);
            $this->fail('Zend_Uri_Exception was expected but not thrown');
        } catch (Zend_Uri_Exception $e) {
        }
    }
}
