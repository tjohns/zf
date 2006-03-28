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


/** ZSearchTokenFilter */
require_once 'Zend/Search/Lucene/analysis/ZSearchTokenFilter.php';


/**
 * Lower case Token filter.
 *
 * @package    ZSearch
 * @subpackage analysis
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */

class ZSearchLowerCaseFilter extends ZSearchTokenFilter
{
    /**
     * Normalize Token or remove it (if null is returned)
     *
     * @param ZSearchToken $srcToken
     * @return ZSearchToken
     */
    public function normalize(ZSearchToken $srcToken)
    {
        $newToken = new ZSearchToken(strtolower( $srcToken->getTermText() ),
                                     $srcToken->getStartOffset(),
                                     $srcToken->getEndOffset(),
                                     $srcToken->getType());

        $newToken->setPositionIncrement($srcToken->getPositionIncrement());

        return $newToken;
    }
}

