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
 * Zend_Sniffs_ControlStructures_SwitchDeclarationSniff.
 *
 * Ensures all the breaks and cases are aligned correctly according to their
 * parent switch's alignment and enforces other switch formatting.
 *
 * @category  Zend
 * @package   Zend_CodingStandard
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 * @version   $Id: $
 */
class Zend_Sniffs_ControlStructures_SwitchDeclarationSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_SWITCH);
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

        $switch        = $tokens[$stackPtr];
        $nextCase      = $stackPtr;
        $caseAlignment = ($switch['column'] + 4);
        $caseCount     = 0;
        $oldCase       = $stackPtr;

        while (($nextCase = $phpcsFile->findNext(array(T_CASE, T_SWITCH), ($nextCase + 1), $switch['scope_closer'])) !== false) {
            // Skip nested SWITCH statements; they are handled on their own.
            if ($tokens[$nextCase]['code'] === T_SWITCH) {
            	$oldCase  = $nextCase;
                $nextCase = $tokens[$nextCase]['scope_closer'];
                continue;
            }

            $caseCount++;

            $content = $tokens[$nextCase]['content'];
            if ($content !== strtolower($content)) {
                $expected = strtolower($content);
                $error    = "CASE keyword must be lowercase; expected \"$expected\" but found \"$content\"";
                $phpcsFile->addError($error, $nextCase);
            }

            if ($tokens[$nextCase]['column'] !== $caseAlignment) {
                $error = 'CASE keyword must be indented 4 spaces from SWITCH keyword';
                $phpcsFile->addError($error, $nextCase);
            }

            if ($tokens[($nextCase + 1)]['type'] !== 'T_WHITESPACE' || $tokens[($nextCase + 1)]['content'] !== ' ') {
                $error = 'CASE keyword must be followed by a single space';
                $phpcsFile->addError($error, $nextCase);
            }

            if (array_key_exists('scope_opener', $tokens[$nextCase])) {
                $opener = $tokens[$nextCase]['scope_opener'];
            } else {
                $opener = $tokens[$oldCase]['scope_opener'];
            }
            if ($tokens[($opener - 1)]['type'] === 'T_WHITESPACE') {
                $error = 'There must be no space before the colon in a CASE statement';
                $phpcsFile->addError($error, $nextCase);
            }

            $nextBreak = $phpcsFile->findNext(array(T_BREAK), ($nextCase + 1), $switch['scope_closer']);
            if ($nextBreak !== false && isset($tokens[$nextBreak]['scope_condition']) === true) {
                // Only check this BREAK statement if it matches the current CASE
                // statement. This stops the same break (used for multiple CASEs) being
                // checked more than once.
                if ($tokens[$nextBreak]['scope_condition'] === $nextCase) {
                    if ($tokens[$nextBreak]['column'] !== ($caseAlignment + 4)) {
                        $error = 'BREAK statement must be indented 4 spaces from CASE keyword';
                        $phpcsFile->addError($error, $nextBreak);
                    }

                    /*
                        Ensure empty CASE statements are not allowed.
                        They must have some code content in them or a defined
                        comment "// break intentionally omitted".
                    */

                    $foundContent = false;
                    $old = 0;
                    for ($i = ($tokens[$nextCase]['scope_opener'] + 1); $i < $nextBreak; $i++) {
                        if ($tokens[$i]['code'] === T_CASE) {
                        	$old = $i;
                            $i = $tokens[$i]['scope_opener'];
                            if ($tokens[$old]['code'] === T_CASE) {
                                $error = "You must insert a 'break intentionally omitted' comment within an empty case";
                                $phpcsFile->addError($error, $nextCase);
                            }
                            continue;
                        }

                        if (in_array($tokens[$i]['code'], PHP_CodeSniffer_Tokens::$emptyTokens) === false) {
                            $foundContent = true;
                            break;
                        }
                    }

                    if ($foundContent === false) {
                        $error = "Empty case statements are not allowed";
                        $phpcsFile->addError($error, $nextCase);
                    }

                    /*
                        Ensure there is no blank line before
                        the BREAK statement.
                    */

                    $breakLine = $tokens[$nextBreak]['line'];
                    $prevLine  = 0;
                    for ($i = ($nextBreak - 1); $i > $stackPtr; $i--) {
                        if ($tokens[$i]['type'] !== 'T_WHITESPACE') {
                            $prevLine = $tokens[$i]['line'];
                            break;
                        }
                    }

                    if ($prevLine !== ($breakLine - 1)) {
                        $error = 'Blank lines are not allowed before BREAK statements';
                        $phpcsFile->addError($error, $nextBreak);
                    }

                    /*
                        Ensure the BREAK statement is followed by
                        a single blank line, or the end switch brace.
                    */

                    $breakLine = $tokens[$nextBreak]['line'];
                    $nextLine  = $tokens[$tokens[$stackPtr]['scope_closer']]['line'];
                    $semicolon = $phpcsFile->findNext(T_SEMICOLON, $nextBreak);
                    for ($i = ($semicolon + 1); $i < $tokens[$stackPtr]['scope_closer']; $i++) {
                        if ($tokens[$i]['type'] !== 'T_WHITESPACE') {
                            $nextLine = $tokens[$i]['line'];
                            break;
                        }
                    }

                    if ($nextLine !== ($breakLine + 2) && $i !== $tokens[$stackPtr]['scope_closer']) {
                        $error = 'BREAK statements must be followed by a single blank line';
                        $phpcsFile->addError($error, $nextBreak);
                    }
                }
            } else {
            	if (array_key_exists('scope_closer', $tokens[$nextCase])) {
                    $nextBreak = $tokens[$nextCase]['scope_closer'];
                }
            }

            /*
                Ensure CASE statements are not followed by
                blank lines.
            */

            $caseLine = $tokens[$nextCase]['line'];
            $nextLine = $tokens[$nextBreak]['line'];
            for ($i = ($opener + 1); $i < $nextBreak; $i++) {
                if ($tokens[$i]['type'] !== 'T_WHITESPACE') {
                    $nextLine = $tokens[$i]['line'];
                    break;
                }
            }

            if ($nextLine !== ($caseLine + 1)) {
                $error = 'Blank lines are not allowed after CASE statements';
                $phpcsFile->addError($error, $nextCase);
            }
        }

        $default = $phpcsFile->findPrevious(T_DEFAULT, $switch['scope_closer'], $switch['scope_opener']);

        // Make sure this default belongs to us.
        if ($default !== false) {
            $conditions = array_keys($tokens[$default]['conditions']);
            $owner      = array_pop($conditions);
            if ($owner !== $stackPtr) {
                $default = false;
            }
        }

        if ($default !== false) {
            $content = $tokens[$default]['content'];
            if ($content !== strtolower($content)) {
                $expected = strtolower($content);
                $error    = "DEFAULT keyword must be lowercase; expected \"$expected\" but found \"$content\"";
                $phpcsFile->addError($error, $default);
            }

            $opener = $tokens[$default]['scope_opener'];
            if ($tokens[($opener - 1)]['type'] === 'T_WHITESPACE') {
                $error = 'There must be no space before the colon in a DEFAULT statement';
                $phpcsFile->addError($error, $default);
            }

            if ($tokens[$default]['column'] !== $caseAlignment) {
                $error = 'DEFAULT keyword must be indented 4 spaces from SWITCH keyword';
                $phpcsFile->addError($error, $default);
            }

            $nextBreak = $phpcsFile->findNext(array(T_BREAK), ($default + 1), $switch['scope_closer']);
            if ($nextBreak !== false) {
                if ($tokens[$nextBreak]['column'] !== ($caseAlignment + 4)) {
                    $error = 'BREAK statement must be indented 4 spaces from DEFAULT keyword';
                    $phpcsFile->addError($error, $nextBreak);
                }

                /*
                    Ensure the BREAK statement is not followed by
                    a blank line.
                */

                $breakLine = $tokens[$nextBreak]['line'];
                $nextLine  = $tokens[$tokens[$stackPtr]['scope_closer']]['line'];
                $semicolon = $phpcsFile->findNext(T_SEMICOLON, $nextBreak);
                for ($i = ($semicolon + 1); $i < $tokens[$stackPtr]['scope_closer']; $i++) {
                    if ($tokens[$i]['type'] !== 'T_WHITESPACE') {
                        $nextLine = $tokens[$i]['line'];
                        break;
                    }
                }

                if ($nextLine !== ($breakLine + 1)) {
                    $error = 'Blank lines are not allowed after the DEFAULT case\'s BREAK statement';
                    $phpcsFile->addError($error, $nextBreak);
                }
            } else {
                $error = 'DEFAULT case must have a BREAK statement';
                $phpcsFile->addError($error, $default);

                $nextBreak = $tokens[$default]['scope_closer'];
            }

            /*
                Ensure empty DEFAULT statements are not allowed.
                They must (at least) have a comment describing why
                the default case is being ignored.
            */

            $foundContent = false;
            for ($i = ($tokens[$default]['scope_opener'] + 1); $i < $nextBreak; $i++) {
                if ($tokens[$i]['type'] !== 'T_WHITESPACE') {
                    $foundContent = true;
                    break;
                }
            }

            if ($foundContent === false) {
                $error = 'Comment required for empty DEFAULT case';
                $phpcsFile->addError($error, $default);
            }

            /*
                Ensure DEFAULT statements are not followed by
                blank lines.
            */

            $defaultLine = $tokens[$default]['line'];
            $nextLine    = $tokens[$nextBreak]['line'];
            for ($i = ($opener + 1); $i < $nextBreak; $i++) {
                if ($tokens[$i]['type'] !== 'T_WHITESPACE') {
                    $nextLine = $tokens[$i]['line'];
                    break;
                }
            }

            if ($nextLine !== ($defaultLine + 1)) {
                $error = 'Blank lines are not allowed after DEFAULT statements';
                $phpcsFile->addError($error, $default);
            }

        } else {
            $error = 'All SWITCH statements must contain a DEFAULT case';
            $phpcsFile->addError($error, $stackPtr);
        }

        if ($tokens[$switch['scope_closer']]['column'] !== $switch['column']) {
            $error = 'Closing brace of SWITCH statement must be aligned with SWITCH keyword';
            $phpcsFile->addError($error, $switch['scope_closer']);
        }

        if ($caseCount === 0) {
            $error = 'SWITCH statements must contain at least one CASE statement';
            $phpcsFile->addError($error, $stackPtr);
        }
    }

}
