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
 * @category  Zend
 * @package   Zend_CodingStandard
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 * @version   $Id: $
 */
if (class_exists('Generic_Sniffs_WhiteSpace_ScopeIndentSniff', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class Generic_Sniffs_WhiteSpace_ScopeIndentSniff not found');
}

/**
 * Zend_Sniffs_Whitespace_ScopeClosingBraceSniff.
 *
 * Checks that the closing braces of scopes are aligned correctly.
 *
 * @category  Zend
 * @package   Zend_CodingStandard
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Sniffs_WhiteSpace_ScopeIndentSniff extends Generic_Sniffs_WhiteSpace_ScopeIndentSniff
{

    /**
     * Does the indent need to be exactly right.
     *
     * If TRUE, indent needs to be exactly $ident spaces. If FALSE,
     * indent needs to be at least $ident spaces (but can be more).
     *
     * @var bool
     */
    protected $exact = false;

}//end class