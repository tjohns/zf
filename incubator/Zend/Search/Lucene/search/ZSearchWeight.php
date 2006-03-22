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


/**
 * Calculate query weights and build query scorers.
 *
 * A Weight is constructed by a query Query->createWeight().
 * The sumOfSquaredWeights() method is then called on the top-level
 * query to compute the query normalization factor Similarity->queryNorm(float).
 * This factor is then passed to normalize(float).  At this point the weighting
 * is complete.
 *
 * @package    ZSearch
 * @subpackage search
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
abstract class ZSearchWeight
{
    /**
     * The weight for this query.
     *
     * @return float
     */
    public abstract function getValue();

    /**
     * The sum of squared weights of contained query clauses.
     *
     * @return float
     */
    public abstract function sumOfSquaredWeights();

    /**
     * Assigns the query normalization factor to this.
     *
     * @param $norm
     */
    public abstract function normalize($norm);
}

