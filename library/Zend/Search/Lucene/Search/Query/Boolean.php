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
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/** Zend_Search_Lucene_Search_Query */
require_once 'Zend/Search/Lucene/Search/Query.php';

/** Zend_Search_Lucene_Search_Weight_Boolean */
require_once 'Zend/Search/Lucene/Search/Weight/Boolean.php';


/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage Search
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Search_Lucene_Search_Query_Boolean extends Zend_Search_Lucene_Search_Query
{

    /**
     * Subqueries
     * Array of Zend_Search_Lucene_Query
     *
     * @var array
     */
    private $_subqueries = array();

    /**
     * Subqueries signs.
     * If true then subquery is required.
     * If false then subquery is prohibited.
     * If null then subquery is neither prohibited, nor required
     *
     * If array is null then all subqueries are required
     *
     * @var array
     */

    private $_signs = array();

    /**
     * A score factor based on the fraction of all query subqueries
     * that a document contains.
     * float for conjunction queries
     * array of float for non conjunction queries
     *
     * @var mixed
     */
    private $_coord = null;


    /**
     * Class constructor.  Create a new Boolean query object.
     *
     * @param array $subqueries    Array of Zend_Search_Search_Query objects
     * @param array $signs    Array of signs.  Sign is boolean|null.
     * @return void
     */
    public function __construct($subqueries = null, $signs = null)
    {
        if (is_array($subqueries)) {
            $this->_subqueries = $subqueries;

            $this->_signs = null;
            // Check if all subqueries are required
            if (is_array($signs)) {
                foreach ($signs as $sign ) {
                    if ($sign !== true) {
                        $this->_signs = $signs;
                        continue;
                    }
                }
            }
        }
    }


    /**
     * Add a $subquery (Zend_Search_Lucene_Query) to this query.
     *
     * The sign is specified as:
     *     TRUE  - subquery is required
     *     FALSE - subquery is prohibited
     *     NULL  - subquery is neither prohibited, nor required
     *
     * @param  Zend_Search_Lucene_Search_Query $subquery
     * @param  boolean|null $sign
     * @return void
     */
    public function addSubquery(Zend_Search_Lucene_Search_Query $subquery, $sign=null) {
        if ($sign !== null || $this->_signs !== null) {       // Skip, if all subqueries are optional
            if ($this->_signs === null) {                     // Check, If all previous subqueries are optional
                foreach ($this->_subqueries as $prevSubquery) {
                    $this->_signs[] = null;
                }
            }
            $this->_signs[] = $sign;
        }

        $this->_subqueries[] = $subquery;
    }


    /**
     * Returns subqueries
     *
     * @return array
     */
    public function getSubqueries()
    {
        return $this->_subqueries;
    }


    /**
     * Return subqueries signs
     *
     * @return array
     */
    public function getSigns()
    {
        return $this->_signs;
    }


    /**
     * Constructs an appropriate Weight implementation for this query.
     *
     * @param Zend_Search_Lucene $reader
     * @return Zend_Search_Lucene_Search_Weight
     */
    public function createWeight($reader)
    {
        $this->_weight = new Zend_Search_Lucene_Search_Weight_Boolean($this, $reader);
        return $this->_weight;
    }


    /**
     * Score calculator for conjunction queries (all subqueries are required)
     *
     * @param integer $docId
     * @param Zend_Search_Lucene $reader
     * @return float
     */
    public function _conjunctionScore($docId, $reader)
    {
        if ($this->_coord === null) {
            $this->_coord = $reader->getSimilarity()->coord(count($this->_subqueries),
                                                            count($this->_subqueries) );
        }

        $score = 0;

        foreach ($this->_subqueries as $subquery) {
            $score += $subquery->score($docId, $reader) * $this->_coord;
        }

        return $score * $this->_coord;
    }


    /**
     * Score calculator for non conjunction queries (not all subqueries are required)
     *
     * @param integer $docId
     * @param Zend_Search_Lucene $reader
     * @return float
     */
    public function _nonConjunctionScore($docId, $reader)
    {
        if ($this->_coord === null) {
            $this->_coord = array();

            $maxCoord = 0;
            foreach ($this->_signs as $sign) {
                if ($sign !== false /* not prohibited */) {
                    $maxCoord++;
                }
            }

            for ($count = 0; $count <= $maxCoord; $count++) {
                $this->_coord[$count] = $reader->getSimilarity()->coord($count, $maxCoord);
            }
        }

        $score = 0;
        $matchedSubqueries = 0;
        foreach ($this->_subqueries as $subqueryId => $subquery) {
            $subscore = $subquery->score($docId, $reader);

            // Prohibited
            if ($this->_signs[$subqueryId] === false && $subscore != 0) {
                return 0;
            }

            // is required, but doen't match
            if ($this->_signs[$subqueryId] === true &&  $subscore == 0) {
                return 0;
            }

            if ($subscore != 0) {
                $matchedSubqueries++;
                $score += $subscore;
            }
        }

        return $score * $this->_coord[$matchedSubqueries];
    }

    /**
     * Score specified document
     *
     * @param integer $docId
     * @param Zend_Search_Lucene $reader
     * @return float
     */
    public function score($docId, $reader)
    {
        if($this->_weight === null) {
            $this->_initWeight($reader);
        }

        if ($this->_signs === null) {
            return $this->_conjunctionScore($docId, $reader);
        } else {
            return $this->_nonConjunctionScore($docId, $reader);
        }
    }
}

