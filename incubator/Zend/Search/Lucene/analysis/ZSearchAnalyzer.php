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
 * @subpackage analysis
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */


/** ZSearchToken */
require_once 'Zend/Search/Lucene/analysis/ZSearchToken.php';

/** ZSearchTextAnalyzer */
require_once 'Zend/Search/Lucene/analysis/ZSearchTextAnalyzer.php';

/** ZSearchTextCIAnalyzer */
require_once 'Zend/Search/Lucene/analysis/ZSearchTextCIAnalyzer.php';



/**
 * An Analyzer is used to analyze text.
 * It thus represents a policy for extracting index terms from text.
 *
 * Note:
 * Lucene Java implementation is oriented to streams. It provides effective work
 * with a huge documents (more then 20Mb).
 * But engine itself is not oriented such documents.
 * Thus ZSearch analysis API works with data strings and sets (arrays).
 *
 * @package    ZSearch
 * @subpackage analysis
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */

abstract class ZSearchAnalyzer
{
    /**
     * The Analyzer implementation used by default.
     *
     * @var ZSearchAnalyzer
     */
    static private $_defaultImpl;

    /**
     * Tokenize text to a terms
     * Returns array of ZSearchToken objects
     *
     * @param string $data
     * @return array
     */
    abstract public function tokenize($data);


    /**
     * Set the default Analyzer implementation used by indexing code.
     *
     * @param ZSearchAnalyzer $similarity
     */
    static public function setDefault(ZSearchAnalyzer $analyzer)
    {
        self::$_defaultImpl = $analyzer;
    }


    /**
     * Return the default Analyzer implementation used by indexing code.
     *
     * @return ZSearchAnalyzer
     */
    static public function getDefault()
    {
        if (!self::$_defaultImpl instanceof ZSearchAnalyzer) {
            self::$_defaultImpl = new ZSearchTextCIAnalyzer();
        }

        return self::$_defaultImpl;
    }

}

