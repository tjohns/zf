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
class ZSearchMultiTermWeight extends ZSearchWeight
{
    /**
     * IndexReader.
     *
     * @var ZSearch
     */
    private $_reader;

    /**
     * The query that this concerns.
     *
     * @var ZSearchMultiTermQuery
     */
    private $_query;

    /**
     * Query terms weights
     * Array of ZSearchTermWeight
     *
     * @var array
     */
    private $_weights;


    /**
     * ZSearchMultiTermWeight constructor
     * query - the query that this concerns.
     * reader - index reader
     *
     * @param ZSearchMultiTermQuery $query
     * @param ZSearch $reader
     */
    public function __construct($query, $reader)
    {
        $this->_query   = $query;
        $this->_reader  = $reader;
        $this->_weights = array();

        $signs = $query->getSigns();

        foreach ($query->getTerms() as $num => $term) {
            if ($signs === null || $signs[$num] === null || $signs[$num]) {
                $this->_weights[$num] = new ZSearchTermWeight($term, $query, $reader);
                $query->setWeight($num, $this->_weights[$num]);
            }
        }
    }


    /**
     * The weight for this query
     *
     * @return float
     */
    public function getValue()
    {
        return $this->_query->getBoost();
    }


    /**
     * The sum of squared weights of contained query clauses.
     *
     * @return float
     */
    public function sumOfSquaredWeights()
    {
        $sum = 0;
        foreach ($this->_weights as $weight) {
            // sum sub weights
            $sum += $weight->sumOfSquaredWeights();
        }

        // boost each sub-weight
        $sum *= $this->_query->getBoost() * $this->_query->getBoost();

        // check for empty query (like '-something -another')
        if ($sum == 0) {
            $sum = 1.0;
        }
        return $sum;
    }


    /**
     * Assigns the query normalization factor to this.
     *
     * @param float $queryNorm
     */
    public function normalize($queryNorm)
    {
        // incorporate boost
        $queryNorm *= $this->_query->getBoost();

        foreach ($this->_weights as $weight) {
            $weight->normalize($queryNorm);
        }
    }
}


