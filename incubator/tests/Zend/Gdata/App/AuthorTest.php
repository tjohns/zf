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
 * @category     Zend
 * @package      Zend_Gdata_App
 * @subpackage UnitTests
 * @copyright    Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com);
 * @license      http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once 'Zend/Gdata/App/Extension/Author.php';
require_once 'Zend/Gdata/App.php';

/**
 * @package Zend_Gdata_App
 * @subpackage UnitTests
 */
class Zend_Gdata_App_AuthorTest extends PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->author = new Zend_Gdata_App_Extension_Author();
    }
      
    public function testEmptyAuthorShouldHaveEmptyExtensionsList() {
        $this->assertTrue(is_array($this->author->extensionElements));
        $this->assertTrue(count($this->author->extensionElements) == 0);
    }
      
    public function testNormalAuthorShouldHaveNoExtensionElements() {
        $this->author->name = new Zend_Gdata_App_Extension_Name('Jeff Scudder');
        $this->assertEquals($this->author->name->text, 'Jeff Scudder');
        $this->assertEquals(count($this->author->extensionElements), 0);
        $newAuthor = new Zend_Gdata_App_Extension_Author(); 
        $newAuthor->transferFromXML($this->author->saveXML());
        $this->assertEquals(count($newAuthor->extensionElements), 0);
        $newAuthor->extensionElements = array(
                new Zend_Gdata_App_Extension_Element('foo', 'atom', null, 'bar'));
        $this->assertEquals(count($newAuthor->extensionElements), 1);
        $this->assertEquals($newAuthor->name->text, 'Jeff Scudder');

        /* try constructing using magic factory */
        $app = new Zend_Gdata_App();
        $newAuthor2 = $app->newAuthor();
        $newAuthor2->transferFromXML($newAuthor->saveXML());
        $this->assertEquals(count($newAuthor2->extensionElements), 1);
        $this->assertEquals($newAuthor2->name->text, 'Jeff Scudder');
    }
}

/*
    public function testEmptyAuthorToAndFromStringShouldMatch() {
      string_from_author = $this->author->ToString();
      new_author = atom.AuthorFromString(string_from_author);
      string_from_new_author = new_author->ToString();
      $this->assertTrue(string_from_author == string_from_new_author);
    }

    public function testAuthorWithNameToAndFromStringShouldMatch() {
      $this->author->name = atom.Name();
      $this->author->name.text = 'Jeff Scudder'
      string_from_author = $this->author->ToString();
      new_author = atom.AuthorFromString(string_from_author);
      string_from_new_author = new_author->ToString();
      $this->assertTrue(string_from_author == string_from_new_author);
      $this->assertTrue($this->author->name.text == new_author->name.text);
    }

    public function testExtensionElements() {
      $this->author->extension_attributes['foo1'] = 'bar'
      $this->author->extension_attributes['foo2'] = 'rab'
      $this->assertTrue($this->author->extension_attributes['foo1'] == 'bar');
      $this->assertTrue($this->author->extension_attributes['foo2'] == 'rab');
      new_author = atom.AuthorFromString($this->author->ToString());
      $this->assertTrue(new_author->extension_attributes['foo1'] == 'bar');
      $this->assertTrue(new_author->extension_attributes['foo2'] == 'rab');
    }

    public function testConvertFullAuthorToAndFromString() {
      author = atom.AuthorFromString(test_data.TEST_AUTHOR);
      $this->assertTrue(author->name.text == 'John Doe');
      $this->assertTrue(author->email.text == 'johndoes@someemailadress.com');
      $this->assertTrue(author->uri.text == 'http://www.google.com');

*/

