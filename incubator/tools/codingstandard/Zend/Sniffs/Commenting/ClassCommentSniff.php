<?php
/**
 * Zend Framework Coding Standard
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
 * @category   Zend
 * @package    Zend_CodingStandard
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: $
 */
if (class_exists('PHP_CodeSniffer_Standards_ZendClassCommentParser', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_Standards_ZendClassCommentParser not found');
}

/**
 * Zend_Sniffs_Commenting_ClassCommentSniff
 *
 * Parses and verifies the class doc comment
 *
 * @category   Zend
 * @package    Zend_CodingStandard
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: $
 */
class Zend_Sniffs_Commenting_ClassCommentSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_CLASS);

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $this->currentFile = $phpcsFile;

        $tokens = $phpcsFile->getTokens();
        $find   = array (
                   T_ABSTRACT,
                   T_WHITESPACE,
                   T_FINAL,
                  );

        // Extract the class comment docblock.
        $commentEnd = $phpcsFile->findPrevious($find, ($stackPtr - 1), null, true);

        if ($commentEnd !== false && $tokens[$commentEnd]['code'] === T_COMMENT) {
            $phpcsFile->addError('You must use "/**" style comments for a class comment', $stackPtr);
            return;
        } else if ($commentEnd === false || $tokens[$commentEnd]['code'] !== T_DOC_COMMENT) {
            $phpcsFile->addError('Missing class doc comment', $stackPtr);
            return;
        }

        $commentStart = ($phpcsFile->findPrevious(T_DOC_COMMENT, ($commentEnd - 1), null, true) + 1);
        $commentNext  = $phpcsFile->findPrevious(T_WHITESPACE, ($commentEnd + 1), $stackPtr, false, $phpcsFile->eolChar);

        // Distinguish file and class comment.
        $prevClassToken = $phpcsFile->findPrevious(T_CLASS, ($stackPtr - 1));
        if ($prevClassToken === false) {
            // This is the first class token in this file, need extra checks.
            $prevNonComment = $phpcsFile->findPrevious(T_DOC_COMMENT, ($commentStart - 1), null, true);
            if ($prevNonComment !== false) {
                $prevComment = $phpcsFile->findPrevious(T_DOC_COMMENT, ($prevNonComment - 1));
                if ($prevComment === false) {
                    // There is only 1 doc comment between open tag and class token.
                    $newlineToken = $phpcsFile->findNext(T_WHITESPACE, ($commentEnd + 1), $stackPtr, false, $phpcsFile->eolChar);
                    if ($newlineToken !== false) {
                        $newlineToken = $phpcsFile->findNext(T_WHITESPACE, ($newlineToken + 1), $stackPtr, false, $phpcsFile->eolChar);
                        if ($newlineToken !== false) {
                            // Blank line between the class and the doc block.
                            // The doc block is most likely a file comment.
                            $phpcsFile->addError('Missing class doc comment', ($stackPtr + 1));
                            return;
                        }
                    }//end if
                }//end if

                // Exactly one blank line before the class comment.
                $prevTokenEnd = $phpcsFile->findPrevious(T_WHITESPACE, ($commentStart - 1), null, true);
                if ($prevTokenEnd !== false) {
                    $blankLineBefore = 0;
                    for ($i = ($prevTokenEnd + 1); $i < $commentStart; $i++) {
                        if ($tokens[$i]['code'] === T_WHITESPACE && $tokens[$i]['content'] === $phpcsFile->eolChar) {
                            $blankLineBefore++;
                        }
                    }

                    if ($blankLineBefore !== 2) {
                        $error = 'There must be exactly one blank line before the class comment';
                        $phpcsFile->addError($error, ($commentStart - 1));
                    }
                }

            }//end if
        }//end if

        $comment = $phpcsFile->getTokensAsString($commentStart, ($commentEnd - $commentStart + 1));

        // Parse the class comment docblock.
        try {
            $this->commentParser = new PHP_CodeSniffer_Standards_ZendClassCommentParser($comment, $phpcsFile);
            $this->commentParser->parse();
        } catch (PHP_CodeSniffer_CommentParser_ParserException $e) {
            $line = ($e->getLineWithinComment() + $commentStart);
            $phpcsFile->addError($e->getMessage(), $line);
            return;
        }

        $comment = $this->commentParser->getComment();
        if (is_null($comment) === true) {
            $error = 'Class doc comment is empty';
            $phpcsFile->addError($error, $commentStart);
            return;
        }

        // Check for a comment description.
        $short = rtrim($comment->getShortComment(), $phpcsFile->eolChar);
        if (trim($short) === '') {
            $error = 'Missing short description in class doc comment';
            $phpcsFile->addError($error, $commentStart);
            return;
        }

        // No extra newline before short description.
        $newlineCount = 0;
        $newlineSpan  = strspn($short, $phpcsFile->eolChar);
        if ($short !== '' && $newlineSpan > 0) {
            $line  = ($newlineSpan > 1) ? 'newlines' : 'newline';
            $error = "Extra $line found before class comment short description";
            $phpcsFile->addError($error, ($commentStart + 1));
        }

        $newlineCount = (substr_count($short, $phpcsFile->eolChar) + 1);

        // Exactly one blank line between short and long description.
        $long = $comment->getLongComment();
        if (empty($long) === false) {
            $between        = $comment->getWhiteSpaceBetween();
            $newlineBetween = substr_count($between, $phpcsFile->eolChar);
            if ($newlineBetween !== 2) {
                $error = 'There must be exactly one blank line between descriptions in class comment';
                $phpcsFile->addError($error, ($commentStart + $newlineCount + 1));
            }

            $newlineCount += $newlineBetween;

            $testLong = trim($long);
            if (preg_match('|[A-Z]|', $testLong[0]) === 0) {
                $error = 'Class comment long description must start with a capital letter';
                $phpcsFile->addError($error, ($commentStart + $newlineCount));
            }
        }

        // Exactly one blank line before tags.
        $tags = $this->commentParser->getTagOrders();
        if (count($tags) > 1) {
            $newlineSpan = $comment->getNewlineAfter();
            if ($newlineSpan !== 2) {
                $error = 'There must be exactly one blank line before the tags in class comment';
                if ($long !== '') {
                    $newlineCount += (substr_count($long, $phpcsFile->eolChar) - $newlineSpan + 1);
                }

                $phpcsFile->addError($error, ($commentStart + $newlineCount));
                $short = rtrim($short, $phpcsFile->eolChar.' ');
            }
        }

        // Short description must be single line and end with a full stop.
        $testShort = trim($short);
        $lastChar  = $testShort[(strlen($testShort) - 1)];
        if (substr_count($testShort, $phpcsFile->eolChar) !== 0) {
            $error = 'Class comment short description must be on a single line';
            $phpcsFile->addError($error, ($commentStart + 1));
        }

        if (preg_match('|[A-Z]|', $testShort[0]) === 0) {
            $error = 'Class comment short description must start with a capital letter';
            $phpcsFile->addError($error, ($commentStart + 1));
        }

        // Check for unknown/deprecated tags.
        $unknownTags = $this->commentParser->getUnknown();
        foreach ($unknownTags as $errorTag) {
            $error = "@$errorTag[tag] tag is not allowed in class comment";
            $phpcsFile->addWarning($error, ($commentStart + $errorTag['line']));
            return;
        }

        // Check each tag.
        $this->processTags($commentStart, $commentEnd);

    }//end process()


    /**
     * Processes each required or optional tag.
     *
     * @param int $commentStart The position in the stack where the comment started.
     * @param int $commentEnd   The position in the stack where the comment ended.
     *
     * @return void
     */
    protected function processTags($commentStart, $commentEnd)
    {
        // Tags in correct order and related info.
        $tags = array(
                 'category'   => array(
                                  'required'       => true,
                                  'allow_multiple' => false,
                                  'order_text'     => 'precedes @package',
                                 ),
                 'package'    => array(
                                  'required'       => true,
                                  'allow_multiple' => false,
                                  'order_text'     => 'follows @category',
                                 ),
                 'subpackage' => array(
                                  'required'       => false,
                                  'allow_multiple' => false,
                                  'order_text'     => 'follows @package',
                                 ),
                 'uses'       => array(
                                  'required'       => false,
                                  'allow_multiple' => true,
                                  'order_text'     => 'follows @subpackage (if used) or @package',
                                 ),
                 'see'        => array(
                                  'required'       => false,
                                  'allow_multiple' => true,
                                  'order_text'     => 'follows @uses (if used) or @subpackage (if used) or @package',
                                 ),
                 'since'      => array(
                                  'required'       => false,
                                  'allow_multiple' => false,
                                  'order_text'     => 'follows @see (if used) or @uses (if used) or @subpackage (if used) or @package',
                                 ),
                 'copyright'  => array(
                                  'required'       => true,
                                  'allow_multiple' => false,
                                  'order_text'     => 'follows @since (if used) or @see (if used) or @uses (if used) or @subpackage (if used) or @package',
                                 ),
                 'license'    => array(
                                  'required'       => true,
                                  'allow_multiple' => false,
                                  'order_text'     => 'follows @copyright',
                                 ),
                 'deprecated' => array(
                                  'required'       => false,
                                  'allow_multiple' => false,
                                  'order_text'     => 'follows @copyright',
                                 ),
                );

        $foundTags   = $this->commentParser->getTagOrders();
        $orderIndex  = 0;
        $indentation = array();
        $longestTag  = 0;
        $errorPos    = 0;

        foreach ($foundTags as $tag => $info) {
        	if (in_array($tag, $tags) === false) {
        		$error = "Tag @$tag is not allowed";
                $this->currentFile->addError($error, $commentEnd);
        	}
        }

        foreach ($tags as $tag => $info) {

            // Required tag missing.
            if ($info['required'] === true && in_array($tag, $foundTags) === false) {
                $error = "Missing @$tag tag in class comment";
                $this->currentFile->addError($error, $commentEnd);
                continue;
            }

             // Get the line number for current tag.
            $tagName = ucfirst($tag);
            if ((($info['allow_multiple'] === true) and ($tag != 'uses')) or ($tag == 'copyright')) {
                $tagName .= 's';
            }

            $getMethod  = 'get'.$tagName;
            if ($tagName === 'Uses') {
            	$tagElement = $this->getUses();
            } else {
                $tagElement = $this->commentParser->$getMethod();
            }
            if (is_null($tagElement) === true || empty($tagElement) === true) {
                continue;
            }

            $errorPos = $commentStart;
            if (is_array($tagElement) === false) {
                $errorPos = ($commentStart + $tagElement->getLine());
            }

            // Get the tag order.
            $foundIndexes = array_keys($foundTags, $tag);

            if (count($foundIndexes) > 1) {
                // Multiple occurance not allowed.
                if ($info['allow_multiple'] === false) {
                    $error = "Only 1 @$tag tag is allowed in a class comment";
                    $this->currentFile->addError($error, $errorPos);
                } else {
                    // Make sure same tags are grouped together.
                    $i     = 0;
                    $count = $foundIndexes[0];
                    foreach ($foundIndexes as $index) {
                        if ($index !== $count) {
                            $errorPosIndex = ($errorPos + $tagElement[$i]->getLine());
                            $error         = "@$tag tags must be grouped together";
                            $this->currentFile->addError($error, $errorPosIndex);
                        }

                        $i++;
                        $count++;
                    }
                }
            }//end if

            // Check tag order.
            if ($foundIndexes[0] > $orderIndex) {
                $orderIndex = $foundIndexes[0];
            } else {
                if (is_array($tagElement) === true && empty($tagElement) === false) {
                    $errorPos += $tagElement[0]->getLine();
                }

                $orderText = $info['order_text'];
                $error     = "The @$tag tag is in the wrong order; the tag $orderText";
                $this->currentFile->addError($error, $errorPos);
            }

            // Store the indentation for checking.
            $len = strlen($tag);
            if ($len > $longestTag) {
                $longestTag = $len;
            }

            if (is_array($tagElement) === true) {
                foreach ($tagElement as $key => $element) {
                    $indentation[] = array(
                                      'tag'   => $tag,
                                      'space' => $this->getIndentation($tag, $element),
                                      'line'  => $element->getLine(),
                                     );
                }
            } else {
                $indentation[] = array(
                                  'tag'   => $tag,
                                  'space' => $this->getIndentation($tag, $tagElement),
                                 );
            }

            $method = 'process'.$tagName;
            if (method_exists($this, $method) === true) {
                // Process each tag if a method is defined.
                call_user_func(array($this, $method), $errorPos);
            } else {
                if (is_array($tagElement) === true) {
                    foreach ($tagElement as $key => $element) {
                        $element->process($this->currentFile, $commentStart, 'class');
                    }
                } else {
                     $tagElement->process($this->currentFile, $commentStart, 'class');
                }
            }
        }//end foreach

        foreach ($indentation as $indentInfo) {
            if ($indentInfo['space'] !== 0 && $indentInfo['space'] !== ($longestTag + 1)) {
                $expected     = (($longestTag - strlen($indentInfo['tag'])) + 1);
                $space        = ($indentInfo['space'] - strlen($indentInfo['tag']));
                $error        = "@$indentInfo[tag] tag comment indented incorrectly. ";
                $error       .= "Expected $expected spaces but found $space.";
                $getTagMethod = 'get'.ucfirst($indentInfo['tag']);
                if (($tags[$indentInfo['tag']]['allow_multiple'] === true) or ($indentInfo['tag'] == 'copyright')) {
                    $line = $indentInfo['line'];
                } else {
                    $tagElem = $this->commentParser->$getTagMethod();
                    $line    = $tagElem->getLine();
                }

                $this->currentFile->addError($error, ($commentStart + $line));
            }
        }

    }//end processTags()


    /**
     * Get the indentation information of each tag.
     *
     * @param string                                   $tagName    The name of the doc comment element.
     * @param PHP_CodeSniffer_CommentParser_DocElement $tagElement The doc comment element.
     *
     * @return void
     */
    protected function getIndentation($tagName, $tagElement)
    {
        if ($tagElement instanceof PHP_CodeSniffer_CommentParser_SingleElement) {
            if ($tagElement->getContent() !== '') {
                return (strlen($tagName) + substr_count($tagElement->getWhitespaceBeforeContent(), ' '));
            }
        } else if ($tagElement instanceof PHP_CodeSniffer_CommentParser_PairElement) {
            if ($tagElement->getValue() !== '') {
                return (strlen($tagName) + substr_count($tagElement->getWhitespaceBeforeValue(), ' '));
            }
        }

        return 0;

    }//end getIndentation()


    /**
     * Process the category tag.
     *
     * @param int $errorPos The line number where the error occurs.
     *
     * @return void
     */
    protected function processCategory($errorPos)
    {
        $category = $this->commentParser->getCategory();
        if ($category !== null) {
            $content = $category->getContent();
            if ($content !== 'Zend') {
                $error = "Category name \"$content\" is not valid; consider \"Zend\" instead";
                $this->currentFile->addError($error, $errorPos);
            }
        }

    }//end processCategory()


    /**
     * Process the package tag.
     *
     * @param int $errorPos The line number where the error occurs.
     *
     * @return void
     */
    protected function processPackage($errorPos)
    {
        $package = $this->commentParser->getPackage();
        if ($package !== null) {
            $content = $package->getContent();
            if ($content !== '') {
                if (PHP_CodeSniffer::isUnderscoreName($content) !== true) {
                    $newContent = str_replace(' ', '_', $content);
                    $nameBits   = explode('_', $newContent);
                    $firstBit   = array_shift($nameBits);
                    $newName    = strtoupper($firstBit{0}).substr($firstBit, 1).'_';
                    foreach ($nameBits as $bit) {
                        $newName .= strtoupper($bit{0}).substr($bit, 1).'_';
                    }

                    $validName = trim($newName, '_');
                    $error     = "Package name \"$content\" is not valid; consider \"$validName\" instead";
                    $this->currentFile->addError($error, $errorPos);
                }
            } else {
                $error = '@package tag must contain a name';
                $this->currentFile->addError($error, $errorPos);
            }
        }

    }//end processPackage()


    /**
     * Process the subpackage tag.
     *
     * @param int $errorPos The line number where the error occurs.
     *
     * @return void
     */
    protected function processSubpackage($errorPos)
    {
        $package = $this->commentParser->getSubpackage();
        if ($package !== null) {
            $content = $package->getContent();
            if ($content !== '') {
                if (PHP_CodeSniffer::isUnderscoreName($content) !== true) {
                    $newContent = str_replace(' ', '_', $content);
                    $nameBits   = explode('_', $newContent);
                    $firstBit   = array_shift($nameBits);
                    $newName    = strtoupper($firstBit{0}).substr($firstBit, 1).'_';
                    foreach ($nameBits as $bit) {
                        $newName .= strtoupper($bit{0}).substr($bit, 1).'_';
                    }

                    $validName = trim($newName, '_');
                    $error     = "Subpackage name \"$content\" is not valid; consider \"$validName\" instead";
                    $this->currentFile->addError($error, $errorPos);
                }
            } else {
                $error = '@subpackage tag must contain a name';
                $this->currentFile->addError($error, $errorPos);
            }
        }

    }//end processSubpackage()


    /**
     * Process the uses tag.
     *
     * @param int $errorPos The line number where the error occurs.
     *
     * @return void
     */
    protected function processUses($errorPos)
    {
        $package = $this->commentParser->getUses();
        if ($package !== null) {
            $content = $package->getContent();
            if ($content !== '') {
                if (PHP_CodeSniffer::isUnderscoreName($content) !== true) {
                    $newContent = str_replace(' ', '_', $content);
                    $nameBits   = explode('_', $newContent);
                    $firstBit   = array_shift($nameBits);
                    $newName    = strtoupper($firstBit{0}).substr($firstBit, 1).'_';
                    foreach ($nameBits as $bit) {
                        $newName .= strtoupper($bit{0}).substr($bit, 1).'_';
                    }

                    $validName = trim($newName, '_');
                    $error     = "Uses name \"$content\" is not valid; consider \"$validName\" instead";
                    $this->currentFile->addError($error, $errorPos);
                }
            } else {
                $error = '@uses tag must contain a name';
                $this->currentFile->addError($error, $errorPos);
            }
        }

    }


    /**
     * Process the see tag.
     *
     * @param int $errorPos The line number where the error occurs.
     *
     * @return void
     */
    protected function processSee($errorPos)
    {
        $package = $this->commentParser->getSee();
        if ($package !== null) {
            $content = $package->getContent();
            if ($content !== '') {
                if (PHP_CodeSniffer::isUnderscoreName($content) !== true) {
                    $newContent = str_replace(' ', '_', $content);
                    $nameBits   = explode('_', $newContent);
                    $firstBit   = array_shift($nameBits);
                    $newName    = strtoupper($firstBit{0}).substr($firstBit, 1).'_';
                    foreach ($nameBits as $bit) {
                        $newName .= strtoupper($bit{0}).substr($bit, 1).'_';
                    }

                    $validName = trim($newName, '_');
                    $error     = "See name \"$content\" is not valid; consider \"$validName\" instead";
                    $this->currentFile->addError($error, $errorPos);
                }
            } else {
                $error = '@see tag must contain a name';
                $this->currentFile->addError($error, $errorPos);
            }
        }

    }


    /**
     * Process the since tag.
     *
     * @param int $errorPos The line number where the error occurs.
     *
     * @return void
     */
    protected function processSince($errorPos)
    {
        $package = $this->commentParser->getSince();
        if ($package !== null) {
            $content = $package->getContent();
            if ($content !== '') {
                if (preg_match('/^([0-9]+)\.([0-9]+)\.([0-9]+)/', $content) === 0) {
                    $error = 'Expected version number to be in the form x.x.x in @since tag';
                    $this->currentFile->addError($error, $errorPos);
                }
            } else {
                $error = '@since tag must contain a name';
                $this->currentFile->addError($error, $errorPos);
            }
        }

    }


    /**
     * Process the copyright tags.
     *
     * @param int $errorPos The line number where the error occurs.
     *
     * @return void
     */
    protected function processCopyrights($errorPos)
    {
        $copyrights = $this->commentParser->getCopyrights();
        if (count($copyrights) > 1) {
            $error = 'Only one @copyright tag allowed';
            $this->currentFile->addError($error, $errorPos);
        } else {
            $content  = $copyrights[0]->getContent();
            if ($content !== 'Copyright (c) 2005-' . date('Y') . ' Zend Technologies USA Inc. (http://www.zend.com)') {
                $error = "@copyright tag must be 'Copyright (c) 2005-" . date('Y') . " Zend Technologies USA Inc. (http://www.zend.com)'";
                $this->currentFile->addError($error, $errorPos);
            }//end if
        }//end if

    }//end processCopyrights()


    /**
     * Process the license tag.
     *
     * @param int $errorPos The line number where the error occurs.
     *
     * @return void
     */
    protected function processLicense($errorPos)
    {
        $license = $this->commentParser->getLicense();
        if ($license !== null) {
            $value   = $license->getValue();
            $comment = $license->getComment();
            if ($comment !== 'http://framework.zend.com/license/new-bsd     New BSD License') {
                $error = "@license tag must be 'http://framework.zend.com/license/new-bsd     New BSD License'";
                $this->currentFile->addError($error, $errorPos);
            }
        }

    }//end processLicense()


    /**
     * Process the depreciated tag.
     *
     * @param int $errorPos The line number where the error occurs.
     *
     * @return void
     */
    protected function processDepreciated($errorPos)
    {
        $depr = $this->commentParser->getDepreciated();
        if ($depr !== null) {
            $content = $depr->getContent();
            if ($content !== '') {
                if (preg_match('/^([0-9]+)\.([0-9]+)\.([0-9]+)/', $content) === 0) {
                    $error = 'Expected version number to be in the form x.x.x in @depreciated tag';
                    $this->currentFile->addError($error, $errorPos);
                }
            } else {
                $error = '@depreciated tag must contain a version';
                $this->currentFile->addError($error, $errorPos);
            }
        }
    }

    protected function getUses()
    {
    }


}//end class
?>
