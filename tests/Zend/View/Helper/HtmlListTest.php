<?php
require_once 'Zend/View/Helper/HtmlList.php';
require_once 'PHPUnit/Framework/TestCase.php';

class Zend_View_Helper_HtmlListTest extends PHPUnit_Framework_TestCase 
{
    /**
     * @var Zend_View_Helper_HtmlList
     */
    protected $_helper;

    public function setUp()
    {
        $this->_helper = new Zend_View_Helper_HtmlList();
    }

    public function tearDown()
    {
        unset($this->_helper);
    }

    public function testMakeUnorderedList()
    {
        $items = array('one', 'two', 'three');

        $list = $this->_helper->htmlList($items);

        $this->assertContains('<ul>', $list);
        $this->assertContains('</ul>', $list);
        foreach ($items as $item) {
            $this->assertContains('<li>' . $item . '</li>', $list);
        }
    } 

    public function testMakeOrderedList()
    {
        $items = array('one', 'two', 'three');

        $list = $this->_helper->htmlList($items, true);

        $this->assertContains('<ol>', $list);
        $this->assertContains('</ol>', $list);
        foreach ($items as $item) {
            $this->assertContains('<li>' . $item . '</li>', $list);
        }
    } 

    public function testMakeUnorderedListWithAttribs()
    {
        $items = array('one', 'two', 'three');
        $attribs = array('class' => 'selected', 'name' => 'list');

        $list = $this->_helper->htmlList($items, false, $attribs);

        $this->assertContains('<ul', $list);
        $this->assertContains('class="selected"', $list);
        $this->assertContains('name="list"', $list);
        $this->assertContains('</ul>', $list);
        foreach ($items as $item) {
            $this->assertContains('<li>' . $item . '</li>', $list);
        }
    } 

    public function testMakeOrderedListWithAttribs()
    {
        $items = array('one', 'two', 'three');
        $attribs = array('class' => 'selected', 'name' => 'list');

        $list = $this->_helper->htmlList($items, true, $attribs);

        $this->assertContains('<ol', $list);
        $this->assertContains('class="selected"', $list);
        $this->assertContains('name="list"', $list);
        $this->assertContains('</ol>', $list);
        foreach ($items as $item) {
            $this->assertContains('<li>' . $item . '</li>', $list);
        }
    } 

    public function testMakeNestedUnorderedList()
    {
        $items = array('one', array('four', 'five', 'six'), 'two', 'three');

        $list = $this->_helper->htmlList($items);

        $this->assertContains('<ul>', $list);
        $this->assertContains('</ul>', $list);
        $this->assertContains('one<ul><li>four', $list);
        $this->assertContains('<li>six</li></ul></li><li>two', $list);
    } 

}
