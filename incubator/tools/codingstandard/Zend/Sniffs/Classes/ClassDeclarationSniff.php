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
 * Zend_Sniffs_Classes_ClassDeclarationSniff
 *
 * Checks the declaration of the class and its inheritance is correct
 *
 * @category  Zend
 * @package   Zend_CodingStandard
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Sniffs_Classes_ClassDeclarationSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_CLASS,
                T_INTERFACE,
               );
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param  PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param  integer              $stackPtr  The position of the current token
     *                                         in the stack passed in $tokens.
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $curlyBrace  = $tokens[$stackPtr]['scope_opener'];
        $lastContent = $phpcsFile->findPrevious(T_WHITESPACE, ($curlyBrace - 1), $stackPtr, true);
        $classLine   = $tokens[$lastContent]['line'];
        $braceLine   = $tokens[$curlyBrace]['line'];
        if ($braceLine === $classLine) {
            $error  = 'Opening brace of a ';
            $error .= $tokens[$stackPtr]['content'];
            $error .= ' must be on the line after the definition';
            $phpcsFile->addError($error, $curlyBrace);
            return;
        } else if ($braceLine > ($classLine + 1)) {
            $difference  = ($braceLine - $classLine - 1);
            $difference .= ($difference === 1) ? ' line' : ' lines';
            $error       = 'Opening brace of a ';
            $error      .= $tokens[$stackPtr]['content'];
            $error      .= ' must be on the line following the ';
            $error      .= $tokens[$stackPtr]['content'];
            $error      .= ' declaration; found ' . $difference;
            $phpcsFile->addError($error, $curlyBrace);
            return;
        }

        if ($tokens[($curlyBrace + 1)]['content'] !== $phpcsFile->eolChar) {
            $type  = strtolower($tokens[$stackPtr]['content']);
            $error = "Opening $type brace must be on a line by itself";
            $phpcsFile->addError($error, $curlyBrace);
        }

        if ($tokens[($curlyBrace - 1)]['code'] === T_WHITESPACE) {
            $prevContent = $tokens[($curlyBrace - 1)]['content'];
            if ($prevContent !== $phpcsFile->eolChar) {
                $blankSpace = substr($prevContent, strpos($prevContent, $phpcsFile->eolChar));
                $spaces     = strlen($blankSpace);
                if ($spaces !== 0) {
                    $error = "Expected 0 spaces before opening brace; $spaces found";
                    $phpcsFile->addError($error, $curlyBrace);
                }
            }
        }

        /*
            Check that this is the only class or interface in the file.
        */

        $nextClass = $phpcsFile->findNext(array(T_CLASS, T_INTERFACE), ($stackPtr + 1));

        if ($nextClass !== false) {
            // We have another, so an error is thrown.
            $error = 'Only one interface or class is allowed in a file';
            $phpcsFile->addError($error, $nextClass);
        }

        /*
            Check alignment of the keyword and braces.
        */

        if ($tokens[($stackPtr - 1)]['code'] === T_WHITESPACE) {
            $prevContent = $tokens[($stackPtr - 1)]['content'];
            if ($prevContent !== $phpcsFile->eolChar) {
                $blankSpace = substr($prevContent, strpos($prevContent, $phpcsFile->eolChar));
                $spaces     = strlen($blankSpace);

                if (in_array($tokens[($stackPtr - 2)]['code'],
                             array(T_ABSTRACT, T_FINAL)) === false) {
                    if ($spaces !== 0) {
                        $type  = strtolower($tokens[$stackPtr]['content']);
                        $error = "Expected 0 spaces before $type keyword; $spaces found";
                        $phpcsFile->addError($error, $stackPtr);
                    }
                } else {
                    if ($spaces !== 1) {
                        $type        = strtolower($tokens[$stackPtr]['content']);
                        $prevContent = strtolower($tokens[($stackPtr - 2)]['content']);
                        $error       = "Expected 1 space between $prevContent and "
                                     . "$type keywords; $spaces found";
                        $phpcsFile->addError($error, $stackPtr);
                    }
                }
            }
        }

        $closeBrace = $tokens[$stackPtr]['scope_closer'];
        if ($tokens[($closeBrace - 1)]['code'] === T_WHITESPACE) {
            $prevContent = $tokens[($closeBrace - 1)]['content'];
            if ($prevContent !== $phpcsFile->eolChar) {
                $blankSpace = substr($prevContent, strpos($prevContent, $phpcsFile->eolChar));
                $spaces     = strlen($blankSpace);
                if ($spaces !== 0) {
                    $error = "Expected 0 spaces before closing brace; $spaces found";
                    $phpcsFile->addError($error, $closeBrace);
                }
            }
        }

        // Check that the closing brace has one blank line after it.
        $nextContent = $phpcsFile->findNext(array(T_COMMENT, T_WHITESPACE), ($closeBrace + 1), null, true);
        if ($nextContent === false) {
        	if (array_key_exists(($closeBrace + 1), $tokens)) {
        		if ($tokens[($closeBrace + 1)]['content'] != "\n") {
                    // No content found, we expect to have a line break at this point
                    $error  = 'Closing brace of a ';
                    $error .= $tokens[$stackPtr]['content'];
                    $error .= ' must be followed by a blank line';
                    $phpcsFile->addError($error, $closeBrace+1);
                }
            }
            if (array_key_exists(($closeBrace + 2), $tokens)) {
                // More than one linebreak or other content found
                $error  = 'Content after closing brace of a ';
                $error .= $tokens[$stackPtr]['content'];
                $error .= ' found. Only one empty line allowed.';
                $phpcsFile->addError($error, $closeBrace);
            }
        } else {
            $nextLine  = $tokens[$nextContent]['line'];
            $braceLine = $tokens[$closeBrace]['line'];
            if ($braceLine === $nextLine) {
                $error  = 'Closing brace of a ';
                $error .= $tokens[$stackPtr]['content'];
                $error .= ' must be followed by a single blank line';
                $phpcsFile->addError($error, $closeBrace);
            } else if ($nextLine !== ($braceLine + 2)) {
                $difference = ($nextLine - $braceLine - 1) . ' lines';
                $error      = 'Closing brace of a ';
                $error     .= $tokens[$stackPtr]['content'];
                $error     .= ' must be followed by a single blank line; found ' . $difference;
                $phpcsFile->addError($error, $closeBrace);
            }
        }

        // Check the closing brace is on it's own line, but allow
        // for comments like "//end class".
        $nextContent = $phpcsFile->findNext(T_COMMENT, ($closeBrace + 1), null, true);
        if (($tokens[$nextContent]['content'] !== $phpcsFile->eolChar) &&
            ($tokens[$nextContent]['line'] === $tokens[$closeBrace]['line'])) {
            $type  = strtolower($tokens[$stackPtr]['content']);
            $error = "Closing $type brace must be on a line by itself";
            $phpcsFile->addError($error, $closeBrace);
        }

        /*
            Check that each of the parent classes or interfaces specified
            are spaced correctly.
        */

        // We need to map out each of the possible tokens in the declaration.
        $keyword      = $stackPtr;
        $openingBrace = $tokens[$stackPtr]['scope_opener'];
        $className    = $phpcsFile->findNext(T_STRING, $stackPtr);

        /*
            Now check the spacing of each token.
        */

        $name = strtolower($tokens[$keyword]['content']);

        // Spacing of the keyword.
        $gap = $tokens[($stackPtr + 1)]['content'];
        if (strlen($gap) !== 1) {
            $found = strlen($gap);
            $error = "Expected 1 space between $name keyword and $name name; $found found";
            $phpcsFile->addError($error, $stackPtr);
        }

        // Check after the name.
        $gap = $tokens[($className + 1)]['content'];
        if (strlen($gap) !== 1) {
            $found = strlen($gap);
            $error = "Expected 1 space after $name name; $found found";
            $phpcsFile->addError($error, $stackPtr);
        }

        // Now check each of the parents.
        $parents    = array();
        $nextParent = ($className + 1);
        $token      = array(T_STRING, T_IMPLEMENTS);
        $nextParent = $phpcsFile->findNext($token, ($nextParent + 1), ($openingBrace - 1));
        while (($nextParent) !== false) {
            $parents[]  = $nextParent;
            $nextParent = $phpcsFile->findNext($token, ($nextParent + 1), ($openingBrace - 1));
        }

        $parentCount = count($parents);

        for ($i = 0; $i < $parentCount; $i++) {
            if ($tokens[$parents[$i]]['code'] === T_IMPLEMENTS) {
                continue;
            }

            if ($tokens[($parents[$i] - 1)]['code'] !== T_WHITESPACE) {
                $name  = $tokens[$parents[$i]]['content'];
                $error = "Expected 1 space before \"$name\"; 0 found";
                $phpcsFile->addError($error, ($nextComma + 1));
            } else {
                $spaceBefore = strlen($tokens[($parents[$i] - 1)]['content']);
                if ($spaceBefore !== 1) {
                    $name  = $tokens[$parents[$i]]['content'];
                    $error = "Expected 1 space before \"$name\"; $spaceBefore found";
                    $phpcsFile->addError($error, $stackPtr);
                }
            }

            if ($tokens[($parents[$i] + 1)]['code'] !== T_COMMA) {
                if ($i !== ($parentCount - 1)) {
                    // This is not the last parent, and the comma
                    // is not where we expect it to be.
                    if ($tokens[($parents[$i] + 2)]['code'] !== T_IMPLEMENTS) {
                        $found = strlen($tokens[($parents[$i] + 1)]['content']);
                        $name  = $tokens[$parents[$i]]['content'];
                        $error = "Expected 0 spaces between \"$name\" and comma; $found found";
                        $phpcsFile->addError($error, $stackPtr);
                    }
                }

                $nextComma = $phpcsFile->findNext(T_COMMA, $parents[$i]);
            } else {
                $nextComma = ($parents[$i] + 1);
            }
        }
    }

}
