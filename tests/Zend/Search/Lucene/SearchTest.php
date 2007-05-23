<?php
/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage UnitTests
 */


/**
 * Zend_Search_Lucene
 */
require_once 'Zend/Search/Lucene.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage UnitTests
 */
class Zend_Search_Lucene_SearchTest extends PHPUnit_Framework_TestCase
{
    public function testQueryParser()
    {
        $queries = array('title:"The Right Way" AND text:go',
                         'title:"Do it right" AND right',
                         'title:Do it right',
                         //'te?t',
                         //'te?t~20^0.8',
                         //'test*',
                         //'te*t',
                         //'roam~',
                         //'roam~0.8',
                         '"jakarta apache"~10',
                         //'mod_date:[20020101 TO 20030101]',
                         //'title:{Aida TO Carmen}',
                         'jakarta apache',
                         'jakarta^4 apache',
                         '"jakarta apache"^4 "Apache Lucene"',
                         '"jakarta apache" jakarta',
                         '"jakarta apache" OR jakarta',
                         '"jakarta apache" || jakarta',
                         '"jakarta apache" AND "Apache Lucene"',
                         '"jakarta apache" && "Apache Lucene"',
                         '+jakarta apache',
                         '"jakarta apache" AND NOT "Apache Lucene"',
                         '"jakarta apache" && !"Apache Lucene"',
                         'NOT "jakarta apache"',
                         '!"jakarta apache"',
                         '"jakarta apache" -"Apache Lucene"',
                         '(jakarta OR apache) AND website',
                         '(jakarta || apache) && website',
                         'title:(+return +"pink panther")',
                         'title:(+re\\turn\\ value +"pink panther\\"" +body:cool)',
                         '+type:1 +id:5',
                         'type:1 AND id:5',
                         'f1:word1 f1:word2 and f1:word3',
                         'f1:word1 not f1:word2 and f1:word3'
                         );

        $rewritedQueries = array('+(title:"the right way") +(text:go)',
                                 '+(title:"do it right") +(path:right modified:right contents:right)',
                                 '(title:do) (path:it modified:it contents:it) (path:right modified:right contents:right)',
                                 // ....
                                 '((path:"jakarta apache"~10) (modified:"jakarta apache"~10) (contents:"jakarta apache"~10))',
                                 // ....
                                 '(path:jakarta modified:jakarta contents:jakarta) (path:apache modified:apache contents:apache)',
                                 '((path:jakarta modified:jakarta contents:jakarta)^4)^4 (path:apache modified:apache contents:apache)',
                                 '((path:"jakarta apache") (modified:"jakarta apache") (contents:"jakarta apache"))^4 ((path:"apache lucene") (modified:"apache lucene") (contents:"apache lucene"))',
                                 '((path:"jakarta apache") (modified:"jakarta apache") (contents:"jakarta apache")) (path:jakarta modified:jakarta contents:jakarta)',
                                 '((path:"jakarta apache") (modified:"jakarta apache") (contents:"jakarta apache")) (path:jakarta modified:jakarta contents:jakarta)',
                                 '((path:"jakarta apache") (modified:"jakarta apache") (contents:"jakarta apache")) (path:jakarta modified:jakarta contents:jakarta)',
                                 '+((path:"jakarta apache") (modified:"jakarta apache") (contents:"jakarta apache")) +((path:"apache lucene") (modified:"apache lucene") (contents:"apache lucene"))',
                                 '+((path:"jakarta apache") (modified:"jakarta apache") (contents:"jakarta apache")) +((path:"apache lucene") (modified:"apache lucene") (contents:"apache lucene"))',
                                 '+(path:jakarta modified:jakarta contents:jakarta) (path:apache modified:apache contents:apache)',
                                 '+((path:"jakarta apache") (modified:"jakarta apache") (contents:"jakarta apache")) -((path:"apache lucene") (modified:"apache lucene") (contents:"apache lucene"))',
                                 '+((path:"jakarta apache") (modified:"jakarta apache") (contents:"jakarta apache")) -((path:"apache lucene") (modified:"apache lucene") (contents:"apache lucene"))',
                                 '<EmptyQuery>',
                                 '<EmptyQuery>',
                                 '((path:"jakarta apache") (modified:"jakarta apache") (contents:"jakarta apache")) -((path:"apache lucene") (modified:"apache lucene") (contents:"apache lucene"))',
                                 '+((path:jakarta modified:jakarta contents:jakarta) (path:apache modified:apache contents:apache)) +(path:website modified:website contents:website)',
                                 '+((path:jakarta modified:jakarta contents:jakarta) (path:apache modified:apache contents:apache)) +(path:website modified:website contents:website)',
                                 '(+(title:return) +(title:"pink panther"))',
                                 '(+(+title:return +title:value) +(title:"pink panther") +(body:cool))',
                                 '+(<EmptyQuery>) +(<EmptyQuery>)',
                                 '+(<EmptyQuery>) +(<EmptyQuery>)',
                                 '(f1:word) (+(f1:word) +(f1:word))',
                                 '(f1:word) (-(f1:word) +(f1:word))');


        $index = Zend_Search_Lucene::open(dirname(__FILE__) . '/_files/_indexSample');

        foreach ($queries as $id => $queryString) {
            $query = Zend_Search_Lucene_Search_QueryParser::parse($queryString);

            $this->assertTrue($query instanceof Zend_Search_Lucene_Search_Query);

            $this->assertEquals($query->rewrite($index)->__toString(), $rewritedQueries[$id]);
        }
    }

    public function testEmptyQuery()
    {
        $index = Zend_Search_Lucene::open(dirname(__FILE__) . '/_files/_indexSample');

        $hits = $index->find('');

        $this->assertEquals(count($hits), 0);
    }

    public function testTermQuery()
    {
        $index = Zend_Search_Lucene::open(dirname(__FILE__) . '/_files/_indexSample');

        $hits = $index->find('submitting');

        $this->assertEquals(count($hits), 3);
        $expectedResultset = array(array(2, 0.114555, 'IndexSource/contributing.patches.html'),
                                   array(7, 0.112241, 'IndexSource/contributing.bugs.html'),
                                   array(8, 0.112241, 'IndexSource/contributing.html'));

        foreach ($hits as $resId => $hit) {
            $this->assertEquals($hit->id, $expectedResultset[$resId][0]);
            $this->assertTrue( abs($hit->score - $expectedResultset[$resId][1]) < 0.000001 );
            $this->assertEquals($hit->path, $expectedResultset[$resId][2]);
        }
    }

    public function testMultiTermQuery()
    {
        $index = Zend_Search_Lucene::open(dirname(__FILE__) . '/_files/_indexSample');

        $hits = $index->find('submitting AND wishlists');

        $this->assertEquals(count($hits), 1);

        $this->assertEquals($hits[0]->id, 8);
        $this->assertTrue( abs($hits[0]->score - 0.141633) < 0.000001 );
        $this->assertEquals($hits[0]->path, 'IndexSource/contributing.html');
    }

    public function testPraseQuery()
    {
        $index = Zend_Search_Lucene::open(dirname(__FILE__) . '/_files/_indexSample');

        $hits = $index->find('"reporting bugs"');

        $this->assertEquals(count($hits), 4);
        $expectedResultset = array(array(0, 0.247795, 'IndexSource/contributing.documentation.html'),
                                   array(7, 0.212395, 'IndexSource/contributing.bugs.html'),
                                   array(8, 0.212395, 'IndexSource/contributing.html'),
                                   array(2, 0.176996, 'IndexSource/contributing.patches.html'));

        foreach ($hits as $resId => $hit) {
            $this->assertEquals($hit->id, $expectedResultset[$resId][0]);
            $this->assertTrue( abs($hit->score - $expectedResultset[$resId][1]) < 0.000001 );
            $this->assertEquals($hit->path, $expectedResultset[$resId][2]);
        }
    }

    public function testBooleanQuery()
    {
        $index = Zend_Search_Lucene::open(dirname(__FILE__) . '/_files/_indexSample');

        $hits = $index->find('submitting AND (wishlists OR requirements)');

        $this->assertEquals(count($hits), 2);
        $expectedResultset = array(array(7, 0.095697, 'IndexSource/contributing.bugs.html'),
                                   array(8, 0.075573, 'IndexSource/contributing.html'));

        foreach ($hits as $resId => $hit) {
            $this->assertEquals($hit->id, $expectedResultset[$resId][0]);
            $this->assertTrue( abs($hit->score - $expectedResultset[$resId][1]) < 0.000001 );
            $this->assertEquals($hit->path, $expectedResultset[$resId][2]);
        }
    }

    public function testQueryHit()
    {
        $index = Zend_Search_Lucene::open(dirname(__FILE__) . '/_files/_indexSample');

        $hits = $index->find('submitting AND wishlists');
        $hit = $hits[0];

        $this->assertTrue($hit instanceof Zend_Search_Lucene_Search_QueryHit);
        $this->assertTrue($hit->getIndex() instanceof Zend_Search_Lucene_Interface);

        $doc = $hit->getDocument();
        $this->assertTrue($doc instanceof Zend_Search_Lucene_Document);

        $this->assertEquals($doc->path, 'IndexSource/contributing.html');
    }

    public function testSortingResult()
    {
        $index = Zend_Search_Lucene::open(dirname(__FILE__) . '/_files/_indexSample');

        $hits = $index->find('"reporting bugs"', 'path');

        $this->assertEquals(count($hits), 4);
        $expectedResultset = array(array(7, 0.212395, 'IndexSource/contributing.bugs.html'),
                                   array(0, 0.247795, 'IndexSource/contributing.documentation.html'),
                                   array(8, 0.212395, 'IndexSource/contributing.html'),
                                   array(2, 0.176996, 'IndexSource/contributing.patches.html'));

        foreach ($hits as $resId => $hit) {
            $this->assertEquals($hit->id, $expectedResultset[$resId][0]);
            $this->assertTrue( abs($hit->score - $expectedResultset[$resId][1]) < 0.000001 );
            $this->assertEquals($hit->path, $expectedResultset[$resId][2]);
        }
    }
}
