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
 * Zend_Sniffs_WhiteSpace_LanguageConstructSpacingSniff.
 *
 * Ensures all language constructs (without brackets) contain a
 * single space between themselves and their content.
 *
 * @category  Zend
 * @package   Zend_CodingStandard
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Sniffs_WhiteSpace_LanguageConstructSpacingSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_ECHO,
                T_PRINT,
                T_RETURN,
                T_INCLUDE,
                T_INCLUDE_ONCE,
                T_REQUIRE,
                T_REQUIRE_ONCE,
                T_NEW
               );
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in
     *                                        the stack passed in $tokens.
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[($stackPtr + 1)]['code'] === T_SEMICOLON) {
            // No content for this language construct.
            return;
        }

        if ($tokens[($stackPtr + 1)]['code'] === T_WHITESPACE) {
            $content       = $tokens[($stackPtr + 1)]['content'];
            $contentLength = strlen($content);
            if ($contentLength !== 1) {
                $error = "Language constructs must be followed by a single space; expected 1 space but found $contentLength";
                $phpcsFile->addError($error, $stackPtr);
            }
        } else {
            $expected = $tokens[$stackPtr]['content'].' '.$tokens[($stackPtr + 1)]['content'];
            $found    = $tokens[$stackPtr]['content'].$tokens[($stackPtr + 1)]['content'];
            $error    = "Language constructs must be followed by a single space; expected \"$expected\" but found \"$found\"";
            $phpcsFile->addError($error, $stackPtr);
        }

    }

}
