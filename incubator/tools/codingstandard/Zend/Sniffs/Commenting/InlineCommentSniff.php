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
 * Zend_CodeSniffer_Sniffs_PEAR_Commenting_InlineCommentSniff
 *
 * Checks that no perl-style comments are used
 *
 * @category  Zend
 * @package   Zend_CodingStandard
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 * @version   $Id: $
 */
class Zend_Sniffs_Commenting_InlineCommentSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_COMMENT);
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

        if ($tokens[$stackPtr]['content']{0} === '#') {
            $error  = 'Perl-style comments are not allowed; use "// Comment" ';
            $error .= ' or "/* comment */" instead.';
            $phpcsFile->addError($error, $stackPtr);
        }

        // We don't want end of block comments. If the last comment is a closing
        // curly brace.
        $previousContent = $phpcsFile->findPrevious(array(T_WHITESPACE), ($stackPtr - 1), null, true);
        if (($tokens[$previousContent]['line'] === $tokens[$stackPtr]['line']) && ($tokens[$previousContent]['code'] === T_CLOSE_CURLY_BRACKET)) {
            return;
        }

        $comment = rtrim($tokens[$stackPtr]['content']);
        // Only want inline comments.
        if (substr($comment, 0, 2) !== '//') {
            return;
        }

        $spaceCount = 0;
        for ($i = 2; $i < strlen($comment); $i++) {
            if ($comment[$i] !== ' ') {
                break;
            }

            $spaceCount++;
        }

        if ($spaceCount === 0) {
            $error = 'No space before comment text; expected "// '.substr($comment, 2).'" but found "'.$comment.'"';
            $phpcsFile->addError($error, $stackPtr);
        }

        if ($spaceCount > 1) {
            $error = $spaceCount.' spaces found before inline comment; expected "// '.substr($comment, (2 + $spaceCount)).'" but found "'.$comment.'"';
            $phpcsFile->addError($error, $stackPtr);
        }


        // The below section determines if a comment block is correctly capitalised,
        // and ends in a full-stop. It will find the last comment in a block, and
        // work its way up.
        $nextComment = $phpcsFile->findNext(array(T_COMMENT), ($stackPtr + 1), null, false);

        if (($nextComment !== false) && (($tokens[$nextComment]['line']) === ($tokens[$stackPtr]['line'] + 1))) {
            return;
        }

        $topComment  = $stackPtr;
        $lastComment = $stackPtr;
        while (($topComment = $phpcsFile->findPrevious(array(T_COMMENT), ($lastComment - 1), null, false)) !== false) {
            if ($tokens[$topComment]['line'] !== ($tokens[$lastComment]['line'] - 1)) {
                break;
            }

            $lastComment = $topComment;
        }

        $topComment  = $lastComment;
        $commentText = '';

        for ($i = $topComment; $i <= $stackPtr; $i++) {
            if ($tokens[$i]['code'] === T_COMMENT) {
                $commentText .= trim(substr($tokens[$i]['content'], 2));
            }
        }

        if ($commentText === '') {
            $error = 'Blank comments are not allowed';
            $phpcsFile->addError($error, $stackPtr);
            return;
        }

        if (preg_match('|[A-Z]|', $commentText[0]) === 0) {
            $error = 'Inline comments must start with a capital letter';
            $phpcsFile->addError($error, $topComment);
        }

        // Finally, the line below the last comment cannot be empty.
        $start = false;
        for ($i = ($stackPtr + 1); $i < $phpcsFile->numTokens; $i++) {
            if ($tokens[$i]['line'] === ($tokens[$stackPtr]['line'] + 1)) {
                if ($tokens[$i]['code'] !== T_WHITESPACE) {
                    return;
                }
            } else if ($tokens[$i]['line'] > ($tokens[$stackPtr]['line'] + 1)) {
                break;
            }
        }

        $error = 'There must be no blank line following an inline comment';
        $phpcsFile->addError($error, $stackPtr);
    }

}
