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
if (class_exists('PHP_CodeSniffer_Standards_ZendClassCommentParser', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_Standards_ZendClassCommentParser not found');
}

/**
 * Zend_Sniffs_Commenting_ClassCommentSniff
 *
 * Parses and verifies the doc comments for files
 *
 * @category  Zend
 * @package   Zend_CodingStandard
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 * @version   $Id: $
 */
class Zend_Sniffs_Commenting_FileCommentSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * The header comment parser for the current file.
     *
     * @var PHP_CodeSniffer_Comment_Parser_ClassCommentParser
     */
    protected $commentParser = null;

    /**
     * The current PHP_CodeSniffer_File object we are processing.
     *
     * @var PHP_CodeSniffer_File
     */
    protected $currentFile = null;


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_OPEN_TAG);

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
        $this->currentFile = $phpcsFile;

        // We are only interested if this is the first open tag.
        if ($stackPtr !== 0) {
            if ($phpcsFile->findPrevious(T_OPEN_TAG, ($stackPtr - 1)) !== false) {
                return;
            }
        }

        $tokens = $phpcsFile->getTokens();

        // Find the next non whitespace token.
        $commentStart = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);

        if ($tokens[$commentStart]['code'] === T_CLOSE_TAG) {
            // We are only interested if this is the first open tag.
            return;
        } else if ($tokens[$commentStart]['code'] === T_COMMENT) {
            $phpcsFile->addError('You must use "/**" style comments for a file comment', ($stackPtr + 1));
            return;
        } else if ($commentStart === false || $tokens[$commentStart]['code'] !== T_DOC_COMMENT) {
            $phpcsFile->addError('Missing file doc comment', ($stackPtr + 1));
            return;
        } else {

            if ($tokens[$commentStart]['line'] !== 2) {
                $phpcsFile->addError('File doc comment must be in the second line, no whitespace allowed', ($stackPtr + 1));
                return;
            }
            // Extract the header comment docblock.
            $commentEnd = ($phpcsFile->findNext(T_DOC_COMMENT, ($commentStart + 1), null, true) - 1);

            // Check if there is only 1 doc comment between the open tag and class token.
            $nextToken   = array(
                            T_ABSTRACT,
                            T_CLASS,
                            T_FUNCTION,
                            T_DOC_COMMENT,
                           );
            $commentNext = $phpcsFile->findNext($nextToken, ($commentEnd + 1));
            if ($commentNext !== false && $tokens[$commentNext]['code'] !== T_DOC_COMMENT) {
                // Found a class token right after comment doc block.
                $newlineToken = $phpcsFile->findNext(T_WHITESPACE, ($commentEnd + 1), $commentNext, false, $phpcsFile->eolChar);
                if ($newlineToken !== false) {
                    $newlineToken = $phpcsFile->findNext(T_WHITESPACE, ($newlineToken + 1), $commentNext, false, $phpcsFile->eolChar);
                    if ($newlineToken === false) {
                        // No blank line between the class token and the doc block.
                        // The doc block is most likely a class comment.
                        $phpcsFile->addError('Missing file doc comment', ($stackPtr + 1));
                        return;
                    }
                }
            }

            $comment = $phpcsFile->getTokensAsString($commentStart, ($commentEnd - $commentStart + 1));

            // Parse the header comment docblock.
            try {
                $this->commentParser = new PHP_CodeSniffer_CommentParser_ClassCommentParser($comment, $phpcsFile);
                $this->commentParser->parse();
            } catch (PHP_CodeSniffer_CommentParser_ParserException $e) {
                $line = ($e->getLineWithinComment() + $commentStart);
                $phpcsFile->addError($e->getMessage(), $line);
                return;
            }

            $comment = $this->commentParser->getComment();
            if (is_null($comment) === true) {
                $error = 'File doc comment is empty';
                $phpcsFile->addError($error, $commentStart);
                return;
            }

            // No extra newline before short description.
            $short        = $comment->getShortComment();
            $newlineCount = 0;
            $newlineSpan  = strspn($short, $phpcsFile->eolChar);
            if ($short !== '' && $newlineSpan > 0) {
                $line  = ($newlineSpan > 1) ? 'newlines' : 'newline';
                $error = "Extra $line found before file comment short description";
                $phpcsFile->addError($error, ($commentStart + 1));
            }

            $newlineCount = (substr_count($short, $phpcsFile->eolChar) + 1);

            // Exactly one blank line between short and long description.
            $long = $comment->getLongComment();
            if (empty($long) === false) {
                $between        = $comment->getWhiteSpaceBetween();
                $newlineBetween = substr_count($between, $phpcsFile->eolChar);
                if ($newlineBetween !== 2) {
                    $error = 'There must be exactly one blank line between descriptions in file comment';
                    $phpcsFile->addError($error, ($commentStart + $newlineCount + 1));
                }

                $newlineCount += $newlineBetween;
            } else {
                $error = 'File Doc Block incomplete... missing LICENSE on line 4';
                $phpcsFile->addError($error, ($commentStart + $newlineCount + 1));
            }

            // Exactly one blank line before tags.
            $tags = $this->commentParser->getTagOrders();
            if (count($tags) > 1) {
                $newlineSpan = $comment->getNewlineAfter();
                if ($newlineSpan !== 2) {
                    $error = 'There must be exactly one blank line before the tags in file comment';
                    if ($long !== '') {
                        $newlineCount += (substr_count($long, $phpcsFile->eolChar) - $newlineSpan + 1);
                    }

                    $phpcsFile->addError($error, ($commentStart + $newlineCount));
                    $short = rtrim($short, $phpcsFile->eolChar.' ');
                }
            }

            if ($short !== " Zend Framework") {
                $error = "Line 2 of the file comment should read 'Zend Framework', found '$short'";
                $phpcsFile->addError($error, $commentStart);
            }

            $longcomment = explode("\n", $long);
            $error = array();
            foreach ($longcomment as $line => $comm) {
            	switch ($line) {
            		case 0:
            		    $check = "LICENSE";
            		    $row   = $line + 5;
            		    if ($comm !== $check) {
            		    	$error[$row] = "Line $row of the file comment should read '$check', found '$comm'";
            		    }
            		    break;
            		case 1:
            		    $row = $line + 5;
            		    if ($comm !== "") {
            		    	$error[$row] = "Line $row of the file comment should be empty, found '$comm'";
            		    }
            		    break;
            		case 2:
            		    $check = " This source file is subject to the new BSD license that is bundled";
            		    $row   = $line + 5;
            		    if ($comm !== $check) {
            		    	$error[$row] = "Line $row of the file comment should read '$check', found '$comm'";
            		    }
            		    break;
            		case 3:
            		    $check = " with this package in the file LICENSE.txt.";
            		    $row   = $line + 5;
            		    if ($comm !== $check) {
            		    	$error[$row] = "Line $row of the file comment should read '$check', found '$comm'";
            		    }
            		    break;
            		case 4:
            		    $check = " It is also available through the world-wide-web at this URL:";
            		    $row   = $line + 5;
            		    if ($comm !== $check) {
            		    	$error[$row] = "Line $row of the file comment should read '$check', found '$comm'";
            		    }
            		    break;
            		case 5:
            		    $check = " http://framework.zend.com/license/new-bsd";
            		    $row   = $line + 5;
            		    if ($comm !== $check) {
            		    	$error[$row] = "Line $row of the file comment should read '$check', found '$comm'";
            		    }
            		    break;
            		case 6:
            		    $check = " If you did not receive a copy of the license and are unable to";
            		    $row   = $line + 5;
            		    if ($comm !== $check) {
            		    	$error[$row] = "Line $row of the file comment should read '$check', found '$comm'";
            		    }
            		    break;
            		case 7:
            		    $check = " obtain it through the world-wide-web, please send an email";
            		    $row   = $line + 5;
            		    if ($comm !== $check) {
            		    	$error[$row] = "Line $row of the file comment should read '$check', found '$comm'";
            		    }
            		    break;
            		case 8:
            		    $check = " to license@zend.com so we can send you a copy immediately.";
            		    $row   = $line + 5;
            		    if ($comm !== $check) {
            		    	$error[$row] = "Line $row of the file comment should read '$check', found '$comm'";
            		    }
            		    break;
            	}
            }
            if (!empty($error)) {
            	foreach ($error as $line => $comm) {
                    $phpcsFile->addError($comm, $line);
                }
            }

            // Check each tag.
            $this->processTags($commentStart, $commentEnd);
        }

    }


    /**
     * Processes each required or optional tag.
     *
     * @param  integer $commentStart The position in the stack where the comment started.
     * @param  integer $commentEnd   The position in the stack where the comment ended.
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
                 'copyright'  => array(
                                  'required'       => true,
                                  'allow_multiple' => false,
                                  'order_text'     => 'follows @subpackage (if used) or @package',
                                 ),
                 'license'    => array(
                                  'required'       => true,
                                  'allow_multiple' => false,
                                  'order_text'     => 'follows @copyright',
                                 ),
                 'version'    => array(
                                  'required'       => true,
                                  'allow_multiple' => false,
                                  'order_text'     => 'follows @license',
                                 ),
                 'deprecated' => array(
                                  'required'       => false,
                                  'allow_multiple' => false,
                                  'order_text'     => 'follows @version',
                                 ),
                );

        $foundTags   = $this->commentParser->getTagOrders();
        $orderIndex  = 0;
        $indentation = array();
        $longestTag  = 0;
        $errorPos    = 0;

        foreach ($foundTags as $tag => $info) {
        	if ((array_key_exists($info, $tags) === false) and ($info !== "comment")) {
        		$error = "Tag @$info is not allowed";
                $this->currentFile->addError($error, ($commentStart + $tag + 13));
        	}
        }

        foreach ($tags as $tag => $info) {

            // Required tag missing.
            if ($info['required'] === true && in_array($tag, $foundTags) === false) {
                $error = "Missing @$tag tag in file comment";
                $this->currentFile->addError($error, $commentEnd);
                continue;
            }

             // Get the line number for current tag.
            $tagName = ucfirst($tag);
            if (($info['allow_multiple'] === true) or ($tag == 'copyright')) {
                $tagName .= 's';
            }

            $getMethod  = 'get'.$tagName;
            $tagElement = $this->commentParser->$getMethod();
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
                    $error = "Only 1 @$tag tag is allowed in a file comment";
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
            }

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
                        $element->process($this->currentFile, $commentStart, 'file');
                    }
                } else {
                     $tagElement->process($this->currentFile, $commentStart, 'file');
                }
            }
        }

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

    }


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

    }


    /**
     * Process the category tag.
     *
     * @param  integer $errorPos The line number where the error occurs.
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

    }


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

    }


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

    }


    /**
     * Process the copyright tags.
     *
     * @param int $commentStart The position in the stack where
     *                          the comment started.
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
            }
        }

    }


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
            if ($value !== 'http://framework.zend.com/license/new-bsd' and $comment !== 'New BSD License') {
                $error = "@license tag 'http://framework.zend.com/license/new-bsd     New BSD License' expected '$comment' found";
                $this->currentFile->addError($error, $errorPos);
            }
        }
    }


    /**
     * Process the version tag.
     *
     * @param int $errorPos The line number where the error occurs.
     *
     * @return void
     */
    protected function processVersion($errorPos)
    {
        $version = $this->commentParser->getVersion();
        if ($version !== null) {
            $content = $version->getContent();
            $matches = array();
            if (empty($content) === true) {
                $error = 'Content missing for @version tag in file comment';
                $this->currentFile->addError($error, $errorPos);
            } else if (strstr($content, '$Id: ') === false) {
                $error = "Invalid version \"$content\" in file comment; consider \"\$Id: $\"";
                $this->currentFile->addWarning($error, $errorPos);
            }
        }
    }

}
