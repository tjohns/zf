<?php
/**
 * @package    Zend_Feed
 * @subpackage UnitTests
 */


/**
 * Zend_Feed_EntryAtom
 */
require_once 'Zend/Feed/EntryAtom.php';

/**
 * Zend_Http_Client_File
 */
require_once 'Zend/Http/Client/File.php';


/**
 * @package    Zend_Feed
 * @subpackage UnitTests
 */
class Zend_Feed_AtomPublishingTest extends PHPUnit_Framework_TestCase {

    private $uri;

    public function setUp()
    {
        $this->uri = 'http://fubar.com/myFeed';
    }

    public function tearDown()
    {
        Zend_Feed::setHttpClient(new Zend_Http_Client());
    }

    public function testPost()
    {
        Zend_Feed::setHttpClient(new TestClient());

        $entry = new Zend_Feed_EntryAtom();

        /* Give the entry its initial values. */
        $entry->title = 'Entry 1';
        $entry->content = '1.1';
        $entry->content['type'] = 'text';

        /* Do the initial post. The base feed URI is the same as the
         * POST URI, so just supply save() with that. */
        $entry->save($this->uri);

        /* $entry will be filled in with any elements returned by the
         * server (id, updated, link rel="edit", etc). */
        $this->assertEquals('1', $entry->id(), 'Expected id to be 1');
        $this->assertEquals('Entry 1', $entry->title(), 'Expected title to be "Entry 1"');
        $this->assertEquals('1.1', $entry->content(), 'Expected content to be "1.1"');
        $this->assertEquals('text', $entry->content['type'], 'Expected content/type to be "text"');
        $this->assertEquals('2005-05-23T16:26:00-08:00', $entry->updated(), 'Expected updated date of 2005-05-23T16:26:00-08:00');
        $this->assertEquals('http://fubar.com/myFeed/1/1/', $entry->link('edit'), 'Expected edit URI of http://fubar.com/myFeed/1/1/');
    }

    public function testEdit()
    {
        Zend_Feed::setHttpClient(new TestClient());
        $contents = file_get_contents(dirname(__FILE__) .  '/_files/AtomPublishingTest-before-update.xml');

        /* The base feed URI is the same as the POST URI, so just supply the
         * Zend_Feed_EntryAtom object with that. */
        $entry = new Zend_Feed_EntryAtom($this->uri, $contents);

        /* Initial state. */
        $this->assertEquals('2005-05-23T16:26:00-08:00', $entry->updated(), 'Initial state of updated timestamp does not match');
        $this->assertEquals('http://fubar.com/myFeed/1/1/', $entry->link('edit'), 'Initial state of edit link does not match');

        /* Just change the entry's properties directly. */
        $entry->content = '1.2';

        /* Then save the changes. */
        $entry->save();

        /* New state. */
        $this->assertEquals('1.2', $entry->content(), 'Content change did not stick');
        $this->assertEquals('2005-05-23T16:27:00-08:00', $entry->updated(), 'New updated link is not correct');
        $this->assertEquals('http://fubar.com/myFeed/1/2/', $entry->link('edit'), 'New edit link is not correct');
    }

}

class TestClient extends Zend_Http_Client_File {

    public function post($data)
    {
        $this->responseCode = 201;
        $this->responseBody = file_get_contents(dirname(__FILE__) . '/_files/AtomPublishingTest-created-entry.xml');
    }

    public function put($data)
    {
        $doc1 = new DOMDocument();
        $doc1->load(dirname(__FILE__) . '/_files/AtomPublishingTest-expected-update.xml');
        $doc2 = new DOMDocument();
        $doc2->loadXML($data);
        if ($doc1->saveXML() != $doc2->saveXML()) {
            $this->responseCode = 400;
            $this->responseBody = false;
            return null;
        }
        $this->responseCode = 200;
        $this->responseBody = file_get_contents(dirname(__FILE__) . '/_files/AtomPublishingTest-updated-entry.xml');
    }

}
