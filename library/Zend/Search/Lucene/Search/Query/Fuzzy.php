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
 * @package    Zend_Search_Lucene
 * @subpackage Search
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/** Zend_Search_Lucene_Search_Query */
require_once 'Zend/Search/Lucene/Search/Query.php';

/** Zend_Search_Lucene_Search_Query_MultiTerm */
require_once 'Zend/Search/Lucene/Search/Query/MultiTerm.php';


/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage Search
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Search_Lucene_Search_Query_Fuzzy extends Zend_Search_Lucene_Search_Query
{
	const DEFAULT_MIN_SIMILARITY = 0.5;
    /**
     * Base searching term.
     *
     * @var Zend_Search_Lucene_Index_Term
     */
    private $_term;
    
    /**
     * A value between 0 and 1 to set the required similarity
     *  between the query term and the matching terms. For example, for a
     *  _minimumSimilarity of 0.5 a term of the same length
     *  as the query term is considered similar to the query term if the edit distance
     *  between both terms is less than length(term)*0.5
     * 
     * @var float
     */
    private $_minimumSimilarity;

    /**
     * The length of common (non-fuzzy) prefix
     * 
     * @var integer
     */
    private $_prefixLength;
    
    /**
     * Matched terms.
     *
     * Matched terms list.
     * It's filled during the search (rewrite operation) and may be used for search result
     * post-processing
     *
     * Array of Zend_Search_Lucene_Index_Term objects
     *
     * @var array
     */
    private $_matches = null;

    /**
     * Zend_Search_Lucene_Search_Query_Wildcard constructor.
     *
     * @param Zend_Search_Lucene_Index_Term $pattern
     */
    public function __construct(Zend_Search_Lucene_Index_Term $term, $minimumSimilarity = self::DEFAULT_MIN_SIMILARITY, $prefixLength = 0)
    {
        $this->_term              = $term;
        $this->_minimumSimilarity = $minimumSimilarity;
        $this->_prefixLength      = $prefixLength;
    }

    /**
     * Calculate distance between words
     *
     * @param string $word1
     * @param string $word2
     * @return integer
     */
    private static function _distance($word1, $word2)
    {
        // 

        return $word;
    }

    /**
     * Re-write query into primitive queries in the context of specified index
     *
     * @param Zend_Search_Lucene_Interface $index
     * @return Zend_Search_Lucene_Search_Query
     */
    public function rewrite(Zend_Search_Lucene_Interface $index)
    {
        $this->_matches = array();

        if ($this->_term->field === null) {
            // Search through all fields
            $fields = $index->getFieldNames(true /* indexed fields list */);
        } else {
            $fields = array($this->_term->field);
        }

        $prefix          = Zend_Search_Lucene_Index_Term::getPrefix($this->_term->text, $this->_prefixLength);
        $prefixLength    = strlen($prefix);

        foreach ($fields as $field) {
            $index->resetTermsStream();

            if ($prefix != '') {
                $index->skipTo(new Zend_Search_Lucene_Index_Term($prefix, $field));

                while ($index->currentTerm() !== null          &&
                       $index->currentTerm()->field == $field  &&
                       substr($index->currentTerm()->text, 0, $prefixLength) == $prefix) {
                    if (self::_distance($this->_term->text, $index->currentTerm()->text) < $this->_minimumSimilarity) {
                        $this->_matches[] = $index->currentTerm();
                    }

                    $index->nextTerm();
                }
            } else {
                $index->skipTo(new Zend_Search_Lucene_Index_Term('', $field));

                while ($index->currentTerm() !== null  &&  $index->currentTerm()->field == $field) {
                    if (self::_distance($this->_term->text, $index->currentTerm()->text) < $this->_minimumSimilarity) {
                        $this->_matches[] = $index->currentTerm();
                    }

                    $index->nextTerm();
                }
            }

            $index->closeTermsStream();
        }

        if (count($this->_matches) == 0) {
            return new Zend_Search_Lucene_Search_Query_Empty();
        } else if (count($this->_matches) == 1) {
            return new Zend_Search_Lucene_Search_Query_Term(reset($this->_matches));
        } else {
            $rewrittenQuery = new Zend_Search_Lucene_Search_Query_MultiTerm();

            foreach ($this->_matches as $matchedTerm) {
                $rewrittenQuery->addTerm($matchedTerm);
            }

            return $rewrittenQuery;
        }
    }

    /**
     * Optimize query in the context of specified index
     *
     * @param Zend_Search_Lucene_Interface $index
     * @return Zend_Search_Lucene_Search_Query
     */
    public function optimize(Zend_Search_Lucene_Interface $index)
    {
        throw new Zend_Search_Lucene_Exception('Wildcard query should not be directly used for search. Use $query->rewrite($index)');
    }


    /**
     * Returns query pattern
     *
     * @return Zend_Search_Lucene_Index_Term
     */
    public function getPattern()
    {
        return $this->_pattern;
    }


    /**
     * Return query terms
     *
     * @return array
     * @throws Zend_Search_Lucene_Exception
     */
    public function getQueryTerms()
    {
        if ($this->_matches === null) {
            throw new Zend_Search_Lucene_Exception('Search has to be performed first to get matched terms');
        }

        return $this->_matches;
    }

    /**
     * Constructs an appropriate Weight implementation for this query.
     *
     * @param Zend_Search_Lucene_Interface $reader
     * @return Zend_Search_Lucene_Search_Weight
     * @throws Zend_Search_Lucene_Exception
     */
    public function createWeight(Zend_Search_Lucene_Interface $reader)
    {
        throw new Zend_Search_Lucene_Exception('Wildcard query should not be directly used for search. Use $query->rewrite($index)');
    }


    /**
     * Execute query in context of index reader
     * It also initializes necessary internal structures
     *
     * @param Zend_Search_Lucene_Interface $reader
     * @throws Zend_Search_Lucene_Exception
     */
    public function execute(Zend_Search_Lucene_Interface $reader)
    {
        throw new Zend_Search_Lucene_Exception('Wildcard query should not be directly used for search. Use $query->rewrite($index)');
    }

    /**
     * Get document ids likely matching the query
     *
     * It's an array with document ids as keys (performance considerations)
     *
     * @return array
     * @throws Zend_Search_Lucene_Exception
     */
    public function matchedDocs()
    {
        throw new Zend_Search_Lucene_Exception('Wildcard query should not be directly used for search. Use $query->rewrite($index)');
    }

    /**
     * Score specified document
     *
     * @param integer $docId
     * @param Zend_Search_Lucene_Interface $reader
     * @return float
     * @throws Zend_Search_Lucene_Exception
     */
    public function score($docId, Zend_Search_Lucene_Interface $reader)
    {
        throw new Zend_Search_Lucene_Exception('Wildcard query should not be directly used for search. Use $query->rewrite($index)');
    }

    /**
     * Highlight query terms
     *
     * @param integer &$colorIndex
     * @param Zend_Search_Lucene_Document_Html $doc
     */
    public function highlightMatchesDOM(Zend_Search_Lucene_Document_Html $doc, &$colorIndex)
    {
        $words = array();

        $matchExpression = '/^' . str_replace(array('\\?', '\\*'), array('.', '.*') , preg_quote($this->_pattern->text, '/')) . '$/';
        if (@preg_match('/\pL/u', 'a') == 1) {
            // PCRE unicode support is turned on
            // add Unicode modifier to the match expression
            $matchExpression .= 'u';
        }

        $tokens = Zend_Search_Lucene_Analysis_Analyzer::getDefault()->tokenize($doc->getFieldUtf8Value('body'), 'UTF-8');
        foreach ($tokens as $token) {
            if (preg_match($matchExpression, $token->getTermText()) === 1) {
                $words[] = $token->getTermText();
            }
        }

        $doc->highlight($words, $this->_getHighlightColor($colorIndex));
    }

    /**
     * Print a query
     *
     * @return string
     */
    public function __toString()
    {
        // It's used only for query visualisation, so we don't care about characters escaping
        return (($this->_term->field === null)? '' : $this->_term->field . ':')
             . $this->_pattern->text . '~'
             . (($this->_minimumSimilarity != self::DEFAULT_MIN_SIMILARITY)? $this->_minimumSimilarity : '');
    }
}

