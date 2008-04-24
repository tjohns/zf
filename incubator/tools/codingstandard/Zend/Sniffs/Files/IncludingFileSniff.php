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

/**
 * Zend_Sniffs_Files_IncludingFileSniff
 *
 * Checks that the include_once is used in conditional situations, and
 * require_once is used elsewhere. Also checks that brackets do not surround
 * the file being included. And checks that require_once and include_once are
 * used instead of require and include
 *
 * @category  Zend
 * @package   Zend_CodingStandard
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 * @version   $Id: $
 */
class Zend_Sniffs_Files_IncludingFileSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * Conditions that should use include_once
     *
     * @var array(int)
     */
    private static $_conditions = array(
                                   T_IF,
                                   T_ELSE,
                                   T_ELSEIF,
                                   T_SWITCH
                                  );

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_INCLUDE_ONCE,
                T_REQUIRE_ONCE,
                T_REQUIRE,
                T_INCLUDE
               );
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $nextToken = $phpcsFile->findNext(PHP_CodeSniffer_Tokens::$emptyTokens, ($stackPtr + 1), null, true);
        if ($tokens[$nextToken]['code'] === T_OPEN_PARENTHESIS) {
            $error  = '"'.$tokens[$stackPtr]['content'].'"';
            $error .= ' is a statement, not a function; ';
            $error .= 'no parentheses are required';
            $phpcsFile->addError($error, $stackPtr);
        }

        $inCondition = (count($tokens[$stackPtr]['conditions']) !== 0) ? true : false;

        // Check to see if this including statement is within the parenthesis of a condition.
        // If that's the case then we need to process it as being within a condition, as they
        // are checking the return value.
        if (isset($tokens[$stackPtr]['nested_parenthesis']) === true) {
            foreach ($tokens[$stackPtr]['nested_parenthesis'] as $left => $right) {
                if (isset($tokens[$left]['parenthesis_owner']) === true) {
                    $inCondition = true;
                }
            }
        }

        // Check to see if they are assigning the return value of this including call.
        // If they are then they are probably checking it, so its conditional.
        $previous = $phpcsFile->findPrevious(PHP_CodeSniffer_Tokens::$emptyTokens, ($stackPtr - 1), null, true);
        if (in_array($tokens[$previous]['code'], PHP_CodeSniffer_Tokens::$assignmentTokens) === true) {
            // The have assigned the return value to it, so its conditional.
            $inCondition = true;
        }

        $tokenCode = $tokens[$stackPtr]['code'];
        if ($tokenCode === T_REQUIRE) {
            $error  = 'The use of "require" is depreciated, use "require_once" instead';
            $phpcsFile->addError($error, $stackPtr);
        }
        if ($tokenCode === T_INCLUDE) {
            $error  = 'The use of "include" is depreciated, use "include_once" instead';
            $phpcsFile->addError($error, $stackPtr);
        }
        if ($inCondition === true) {
            // We are inside a conditional statement. We need an include_once.
            if ($tokenCode === T_REQUIRE_ONCE) {
                $error  = 'File is being conditionally included; ';
                $error .= 'use "include_once" instead';
                $phpcsFile->addError($error, $stackPtr);
            }
        } else {
            // We are unconditionally including, we need a require_once.
            if ($tokenCode === T_INCLUDE_ONCE) {
                $error  = 'File is being unconditionally included; ';
                $error .= 'use "require_once" instead';
                $phpcsFile->addError($error, $stackPtr);
            }
        }
    }

}
