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
 * Zend_Sniffs_ControlStructures_ForEachLoopDeclarationSniff
 *
 * Verifies that there is a space between each condition of foreach loops
 *
 * @category  Zend
 * @package   Zend_CodingStandard
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Sniffs_ControlStructures_ForEachLoopDeclarationSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_FOREACH
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
        $errors = array();

        $openingBracket = $phpcsFile->findNext(T_OPEN_PARENTHESIS, $stackPtr);
        $closingBracket = $tokens[$openingBracket]['parenthesis_closer'];

        if ($tokens[($openingBracket + 1)]['code'] === T_WHITESPACE) {
            $errors[] = 'Space found after opening bracket of FOREACH loop';
        }

        if ($tokens[($closingBracket - 1)]['code'] === T_WHITESPACE) {
            $errors[] = 'Space found before closing bracket of FOREACH loop';
        }

        $asToken = $phpcsFile->findNext(T_AS, $openingBracket);
        $content = $tokens[$asToken]['content'];
        if ($content !== strtolower($content)) {
            $expected = strtolower($content);
            $errors[] = "AS keyword must be lowercase; expected \"$expected\" but found \"$content\"";
        }

        $doubleArrow = $phpcsFile->findNext(T_DOUBLE_ARROW, $openingBracket, $closingBracket);

        if ($doubleArrow !== false) {
            if ($tokens[($doubleArrow - 1)]['code'] !== T_WHITESPACE) {
                $errors[] = 'Expected 1 space before "=>"; 0 found';
            } else {
                if (strlen($tokens[($doubleArrow - 1)]['content']) !== 1) {
                    $spaces   = strlen($tokens[($doubleArrow - 1)]['content']);
                    $errors[] = "Expected 1 space before \"=>\"; $spaces found";
                }

            }

            if ($tokens[($doubleArrow + 1)]['code'] !== T_WHITESPACE) {
                $errors[] = 'Expected 1 space after "=>"; 0 found';
            } else {
                if (strlen($tokens[($doubleArrow + 1)]['content']) !== 1) {
                    $spaces   = strlen($tokens[($doubleArrow + 1)]['content']);
                    $errors[] = "Expected 1 space after \"=>\"; $spaces found";
                }
            }
        }

        if ($tokens[($asToken - 1)]['code'] !== T_WHITESPACE) {
            $errors[] = 'Expected 1 space before "as"; 0 found';
        } else {
            if (strlen($tokens[($asToken - 1)]['content']) !== 1) {
                $spaces   = strlen($tokens[($asToken - 1)]['content']);
                $errors[] = "Expected 1 space before \"as\"; $spaces found";
            }
        }

        if ($tokens[($asToken + 1)]['code'] !== T_WHITESPACE) {
            $errors[] = 'Expected 1 space after "as"; 0 found';
        } else {
            if (strlen($tokens[($asToken + 1)]['content']) !== 1) {
                $spaces   = strlen($tokens[($asToken + 1)]['content']);
                $errors[] = "Expected 1 space after \"as\"; $spaces found";
            }

        }

        foreach ($errors as $error) {
            $phpcsFile->addError($error, $stackPtr);
        }
    }

}
