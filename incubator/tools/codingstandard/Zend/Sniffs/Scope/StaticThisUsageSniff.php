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

if (class_exists('PHP_CodeSniffer_Standards_AbstractScopeSniff', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_Standards_AbstractScopeSniff not found');
}

/**
 * Zend_Sniffs_Scope_StaticThisUsageSniff.
 *
 * Checks for usage of "$this" in static methods, which will cause
 * runtime errors
 *
 * @category  Zend
 * @package   Zend_CodingStandard
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Sniffs_Scope_StaticThisUsageSniff extends PHP_CodeSniffer_Standards_AbstractScopeSniff
{
    /**
     * Constructs the test with the tokens it wishes to listen for.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct(array(T_CLASS), array(T_FUNCTION));
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The current file being scanned.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     * @param int                  $currScope A pointer to the start of the scope.
     * @return void
     */
    public function processTokenWithinScope(PHP_CodeSniffer_File $phpcsFile, $stackPtr, $currScope)
    {
        $tokens   = $phpcsFile->getTokens();
        $function = $tokens[($stackPtr + 2)];

        if ($function['code'] !== T_STRING) {
            return;
        }

        $functionName = $function['content'];
        $classOpener  = $tokens[$currScope]['scope_condition'];
        $className    = $tokens[($classOpener + 2)]['content'];

        $methodProps = $phpcsFile->getMethodProperties($stackPtr);

        if ($methodProps['is_static'] === true) {
            if (isset($tokens[$stackPtr]['scope_closer']) === false) {
                // There is no scope opener or closer, so the function
                // must be abstract.
                return;
            }

            $thisUsage = $stackPtr;
            while (($thisUsage = $phpcsFile->findNext(array(T_VARIABLE), ($thisUsage + 1), $tokens[$stackPtr]['scope_closer'], false, '$this')) !== false) {
                if ($thisUsage === false) {
                    return;
                }

                $error = 'Usage of "$this" in static methods will cause runtime errors';
                $phpcsFile->addError($error, $thisUsage);
            }
        }
    }

}
