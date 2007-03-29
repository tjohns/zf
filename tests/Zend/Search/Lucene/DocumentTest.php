<?php
/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage UnitTests
 */


/**
 * Zend_Search_Lucene_PriorityQueue
 */
require_once 'Zend/Search/Lucene/Field.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage UnitTests
 */
class Zend_Search_Lucene_DocumentTest extends PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $document =  new Zend_Search_Lucene_Document();

        $this->assertEquals($document->boost, 1);
    }

    public function testFields()
    {
        $document =  new Zend_Search_Lucene_Document();

        $document->addField(Zend_Search_Lucene_Field::Text('title',      'Title'));
        $document->addField(Zend_Search_Lucene_Field::Text('annotation', 'Annotation'));
        $document->addField(Zend_Search_Lucene_Field::Text('body',       'Document body, document body, document body...'));

        $fieldnamesDiffArray = array_diff($document->getFieldNames(), array('title', 'annotation', 'body'));
        $this->assertTrue(is_array($fieldnamesDiffArray));
        $this->assertEquals(count($fieldnamesDiffArray), 0);

        $this->assertEquals($document->title,      'Title');
        $this->assertEquals($document->annotation, 'Annotation');
        $this->assertEquals($document->body,       'Document body, document body, document body...');

        $this->assertEquals($document->getField('title')->value,      'Title');
        $this->assertEquals($document->getField('annotation')->value, 'Annotation');
        $this->assertEquals($document->getField('body')->value,       'Document body, document body, document body...');

        $this->assertEquals($document->getFieldValue('title'),      'Title');
        $this->assertEquals($document->getFieldValue('annotation'), 'Annotation');
        $this->assertEquals($document->getFieldValue('body'),       'Document body, document body, document body...');


        $document->addField(Zend_Search_Lucene_Field::Text('description', 'Words with umlauts: εγό...', 'ISO-8859-1'));
        $this->assertEquals($document->description, 'Words with umlauts: εγό...');
        $this->assertEquals($document->getFieldUtf8Value('description'), 'Words with umlauts: Γ₯Γ£ΓΌ...');
    }
}

