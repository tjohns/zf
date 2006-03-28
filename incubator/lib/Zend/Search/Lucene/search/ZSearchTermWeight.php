<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to version 1.0 of the Zend Framework
 * license, that is bundled with this package in the file LICENSE, and
 * is available through the world-wide-web at the following URL:
 * http://www.zend.com/license/framework/1_0.txt. If you did not receive
 * a copy of the Zend Framework license and are unable to obtain it
 * through the world-wide-web, please send a note to license@zend.com
 * so we can mail you a copy immediately.
 *
 * @package    ZSearch
 * @subpackage search
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */


/** ZSearchWeight */
require_once 'Zend/Search/Lucene/search/ZSearchWeight.php';


/**
 * @package    ZSearch
 * @subpackage search
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
class ZSearchTermWeight extends ZSearchWeight
{
    /**
     * IndexReader.
     *
     * @var ZSearch
     */
    private $_reader;

    /**
     * Term
     *
     * @var Zend_Search_Lucene_Index_Term
     */
    private $_term;

    /**
     * The query that this concerns.
     *
     * @var ZSearchQuery
     */
    private $_query;

    /**
     * Weight value
     *
     * @var float
     */
    private $_value;

    /**
     * Score factor
     *
     * @var float
     */
    private $_idf;

    /**
     * Normalization factor
     *
     * @var float
     */
    private $_queryNorm;


    /**
     * Query weight
     *
     * @var float
     */
    private $_queryWeight;


    /**
     * ZSearchTermWeight constructor
     * reader - index reader
     *
     * @param ZSearch $reader
     */
    public function __construct($term, $query, $reader)
    {
        $this->_term   = $term;
        $this->_query  = $query;
        $this->_reader = $reader;
    }


    /**
     * The weight for this query
     *
     * @return float
     */
    public function getValue()
    {
        return $this->_value;
    }


    /**
     * The sum of squared weights of contained query clauses.
     *
     * @return float
     */
    public function sumOfSquaredWeights()
    {
        // compute idf
        $this->_idf = $this->_reader->getSimilarity()->idf($this->_term, $this->_reader);

        // compute query weight
        $this->_queryWeight = $this->_idf * $this->_query->getBoost();

        // square it
        return $this->_queryWeight * $this->_queryWeight;
    }


    /**
     * Assigns the query normalization factor to this.
     *
     * @param float $queryNorm
     */
    public function normalize($queryNorm)
    {
        $this->_queryNorm = $queryNorm;

        // normalize query weight
        $this->_queryWeight *= $queryNorm;

        // idf for documents
        $this->_value = $this->_queryWeight * $this->_idf;
    }
}

