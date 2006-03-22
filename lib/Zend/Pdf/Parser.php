<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to version 1.0 of the Zend Framework
 * license, that is bundled with this package in the file LICENSE, and
 * is available through the world-wide-web at the following URL:
 * http://www.zend.com/license/framework/1_0.txt. If you did not receive
 * a copy of the Zend Framework license and are unable to obtain it
 * through the world-wide-web, please send a note to license@zend.com
 * so we can mail you a copy immediately.
 *
 * @package    Zend_Pdf
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */


/** Zend_Pdf_Const */
require_once 'Zend/Pdf/Const.php';

/** Zend_Pdf_Element */
require_once 'Zend/Pdf/Element.php';

/** Zend_Pdf_Element_Array */
require_once 'Zend/Pdf/Element/Array.php';

/** Zend_Pdf_Element_String_Binary */
require_once 'Zend/Pdf/Element/String/Binary.php';

/** Zend_Pdf_Element_Boolean */
require_once 'Zend/Pdf/Element/Boolean.php';

/** Zend_Pdf_Element_Dictionary */
require_once 'Zend/Pdf/Element/Dictionary.php';

/** Zend_Pdf_Element_Name */
require_once 'Zend/Pdf/Element/Name.php';

/** Zend_Pdf_Element_Numeric */
require_once 'Zend/Pdf/Element/Numeric.php';

/** Zend_Pdf_Element_Object */
require_once 'Zend/Pdf/Element/Object.php';

/** Zend_Pdf_Element_Reference */
require_once 'Zend/Pdf/Element/Reference.php';

/** Zend_Pdf_Element_Stream */
require_once 'Zend/Pdf/Element/Stream.php';

/** Zend_Pdf_Element_Object_Stream */
require_once 'Zend/Pdf/Element/Object/Stream.php';

/** Zend_Pdf_Element_String */
require_once 'Zend/Pdf/Element/String.php';

/** Zend_Pdf_Element_Null */
require_once 'Zend/Pdf/Element/Null.php';

/** Zend_Pdf_Element_Reference_Context */
require_once 'Zend/Pdf/Element/Reference/Context.php';

/** Zend_Pdf_Element_Reference_Table */
require_once 'Zend/Pdf/Element/Reference/Table.php';

/** Zend_Pdf_Trailer_Keeper */
require_once 'Zend/Pdf/Trailer/Keeper.php';

/** Zend_Pdf */
require_once 'Zend/Pdf.php';

/** Zend_Pdf_ElementFactory */
require_once 'Zend/Pdf/ElementFactory.php';

/** Zend_Pdf_PHPArray */
require_once 'Zend/Pdf/PHPArray.php';



/**
 * PDF file parser
 *
 * @package    Zend_Pdf
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
class Zend_Pdf_Parser
{
    /**
     * Source PDF
     *
     * @var string
     */
    private $_pdfString;

    /**
     * Current position in a _pdfString
     *
     * @var integer
     */
    private $_current;

    /**
     * Current reference context
     *
     * @var Zend_Pdf_Element_Reference_Context
     */
    private $_currentContext;

    /**
     * Array of elements of the currently parsed object/trailer
     *
     * @var Zend_Pdf_PHPArray
     */
    private $_elements;

    /**
     * Stack for PDF reference contexts, positions and current objects
     *
     * @var array
     */
    private $_contextStack;

    /**
     * Last PDF file trailer
     *
     * @var Zend_Pdf_Trailer_Keeper
     */
    private $_trailer;

    /**
     * PDF objects factory.
     *
     * @var Zend_Pdf_ElementFactory
     */
    private $_objFactory = null;


    /**
     * Save current position on a top of stack
     */
    private function _pushContext()
    {
        $this->_contextStack[] = $this->_current;
        $this->_contextStack[] = $this->_currentContext;
        $this->_contextStack[] = $this->_elements;
    }


    /**
     * Restore current position from a top of stack
     */
    private function _popContext()
    {
        $this->_elements       = array_pop($this->_contextStack);
        $this->_currentContext = array_pop($this->_contextStack);
        $this->_current        = array_pop($this->_contextStack);
    }


    /**
     * Character with code $chCode is white space
     *
     * @param integer $chCode
     * @return boolean
     */
    private static function isWhiteSpace($chCode )
    {
        if ($chCode == 0x00 || // null character
            $chCode == 0x09 || // Tab
            $chCode == 0x0A || // Line feed
            $chCode == 0x0C || // Form Feed
            $chCode == 0x0D || // Carriage return
            $chCode == 0x20    // Space
           ) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Character with code $chCode is a delimiter character
     *
     * @param integer $chCode
     * @return boolean
     */
    private static function isDelimiter($chCode )
    {
        if ($chCode == 0x28 || // '('
            $chCode == 0x29 || // ')'
            $chCode == 0x3C || // '<'
            $chCode == 0x3E || // '>'
            $chCode == 0x5B || // '['
            $chCode == 0x5D || // ']'
            $chCode == 0x7B || // '{'
            $chCode == 0x7D || // '}'
            $chCode == 0x2F || // '/'
            $chCode == 0x25    // '%'
           ) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Skip white space
     *
     * @param boolean $skipComment
     */
    private function _skipWhiteSpace($skipComment = true)
    {
        while ($this->_current < strlen($this->_pdfString)) {
            if (self::isWhiteSpace( ord($this->_pdfString{$this->_current}) )) {
                $this->_current++;
            } else if (ord($this->_pdfString{$this->_current}) == 0x25 && $skipComment) { // '%'
                $this->_skipComment();
            } else {
                return;
            }
        }
    }


    /**
     * Skip comment
     */
    private function _skipComment()
    {
        while ($this->_current < strlen($this->_pdfString))
        {
            if (ord($this->_pdfString{$this->_current}) != 0x0A || // Line feed
                ord($this->_pdfString{$this->_current}) != 0x0d    // Carriage return
               ) {
                $this->_current++;
            } else {
                return;
            }
        }
    }


    /**
     * Read comment line
     *
     * @return string
     */
    private function _readComment()
    {
        $this->_skipWhiteSpace(false);

        /**
         * Check if it's a comment line
         */
        if ($this->_pdfString{$this->_current} != '%') {
            return '';
        }

        for ($start = $this->_current;
             $this->_current < strlen($this->_pdfString);
             $this->_current++) {
            if (ord($this->_pdfString{$this->_current}) == 0x0A || // Line feed
                ord($this->_pdfString{$this->_current}) == 0x0d    // Carriage return
               ) {
                break;
            }
        }

        return substr($this->_pdfString, $start, $this->_current-$start);
    }


    /**
     * Returns next lexeme from a pdf stream
     *
     * @return string
     */
    private function _readLexeme()
    {
        $this->_skipWhiteSpace();
        $start = $this->_current;

        if (self::isDelimiter( ord($this->_pdfString{$start}) )) {
            if ($this->_pdfString{$start} == '<' && $this->_pdfString{$start+1} == '<') {
                $this->_current += 2;
                return '<<';
            } else if ($this->_pdfString{$start} == '>' && $this->_pdfString{$start+1} == '>') {
                $this->_current += 2;
                return '>>';
            } else {
                $this->_current++;
                return $this->_pdfString{$start};
            }
        } else {
            while ( (!self::isDelimiter(  ord($this->_pdfString{$this->_current}) )) &&
                    (!self::isWhiteSpace( ord($this->_pdfString{$this->_current}) ))   ) {
                $this->_current++;
            }

            return substr($this->_pdfString, $start, $this->_current - $start);
        }
    }


    /**
     * Read elemental object from a PDF stream
     *
     * @return Zend_Pdf_Element
     * @throws Zend_Pdf_Exception
     */
    private function _readElementalObject($nextLexeme = null)
    {
        if ($nextLexeme === null) {
            $nextLexeme = $this->_readLexeme();
        }

        switch ($nextLexeme) {
            case '(':
                return ($this->_elements[] = $this->_readString());

            case '<':
                return ($this->_elements[] = $this->_readBinaryString());

            case '/':
                return ($this->_elements[] = new Zend_Pdf_Element_Name(
                                                Zend_Pdf_Element_Name::unescape( $this->_readLexeme() )
                                                                      ));

            case '[':
                return ($this->_elements[] = $this->_readArray());

            case '<<':
                return ($this->_elements[] = $this->_readDictionary());

            case ')':
                // fall through to next case
            case '>':
                // fall through to next case
            case ']':
                // fall through to next case
            case '>>':
                // fall through to next case
            case '{':
                // fall through to next case
            case '}':
                throw new Zend_Pdf_Exception(sprintf('PDF file syntax error. Offset - 0x%X.',
                                                $this->_current));

            default:
                if (strcasecmp($nextLexeme, 'true') == 0) {
                    return ($this->_elements[] = new Zend_Pdf_Element_Boolean(true));
                } else if (strcasecmp($nextLexeme, 'false') == 0) {
                    return ($this->_elements[] = new Zend_Pdf_Element_Boolean(false));
                } else if (strcasecmp($nextLexeme, 'null') == 0) {
                    return ($this->_elements[] = new Zend_Pdf_Element_Null());
                }

                $ref = $this->_readReference($nextLexeme);
                if ($ref !== null) {
                    return ($this->_elements[] = $ref);
                }

                return ($this->_elements[] = $this->_readNumeric($nextLexeme));
        }
    }


    /**
     * Read string PDF object
     * Also reads trailing ')' from a pdf stream
     *
     * @return Zend_Pdf_Element_String
     * @throws Zend_Pdf_Exception
     */
    private function _readString()
    {
        $start = $this->_current;
        $openedBrackets = 1;

        while ($this->_current < strlen($this->_pdfString)) {
            switch (ord( $this->_pdfString{$this->_current} )) {
                case 0x28: // '(' - opened bracket in the string, needs balanced pair.
                    $openedBrackets++;
                    break;

                case 0x29: // ')' - pair to the opened bracket
                    $openedBrackets--;
                    break;

                case 0x5C: // '\\' - escape sequence, skip next char from a check
                    $this->_current++;
            }

            $this->_current++;
            if ($openedBrackets == 0) {
                break; // end of string
            }
        }
        if ($openedBrackets != 0) {
            throw new Zend_Pdf_Exception(sprintf('PDF file syntax error. Unexpected end of file while string reading. Offset - 0x%X. \')\' expected.', $start));
        }

        return new Zend_Pdf_Element_String(Zend_Pdf_Element_String::unescape( substr($this->_pdfString,
                                                                 $start,
                                                                 $this->_current - $start - 1) ));
    }


    /**
     * Read binary string PDF object
     * Also reads trailing '>' from a pdf stream
     *
     * @return Zend_Pdf_Element_String_Binary
     * @throws Zend_Pdf_Exception
     */
    private function _readBinaryString()
    {
        $start = $this->_current;

        while ($this->_current < strlen($this->_pdfString)) {
            if (self::isWhiteSpace( ord($this->_pdfString{$this->_current}) ) ||
                ctype_xdigit( $this->_pdfString{$this->_current} ) ) {
                $this->_current++;
            } else if ($this->_pdfString{$this->_current} == '>') {
                $this->_current++;
                return new Zend_Pdf_Element_String_Binary(
                               Zend_Pdf_Element_String_Binary::unescape( substr($this->_pdfString,
                                                                    $start,
                                                                    $this->_current - $start - 1) ));
            } else {
                throw new Zend_Pdf_Exception(sprintf('PDF file syntax error. Unexpected character while binary string reading. Offset - 0x%X.', $this->_current));
            }
        }
        throw new Zend_Pdf_Exception(sprintf('PDF file syntax error. Unexpected end of file while binary string reading. Offset - 0x%X. \'>\' expected.', $start));
    }


    /**
     * Read array PDF object
     * Also reads trailing ']' from a pdf stream
     *
     * @return Zend_Pdf_Element_Array
     * @throws Zend_Pdf_Exception
     */
    private function _readArray()
    {
        $elements = array();

        while ( strlen($nextLexeme = $this->_readLexeme()) != 0 ) {
            if ($nextLexeme != ']') {
                $elements[] = $this->_readElementalObject($nextLexeme);
            } else {
                return new Zend_Pdf_Element_Array($elements);
            }
        }

        throw new Zend_Pdf_Exception(sprintf('PDF file syntax error. Unexpected end of file while array reading. Offset - 0x%X. \']\' expected.', $this->_current));
    }


    /**
     * Read dictionary PDF object
     * Also reads trailing '>>' from a pdf stream
     *
     * @return Zend_Pdf_Element_Dictionary
     * @throws Zend_Pdf_Exception
     */
    private function _readDictionary()
    {
        $dictionary = new Zend_Pdf_Element_Dictionary();

        while ( strlen($nextLexeme = $this->_readLexeme()) != 0 ) {
            if ($nextLexeme != '>>') {
                $nameStart = $this->_current - strlen($nextLexeme);

                $name  = $this->_readElementalObject($nextLexeme);
                $value = $this->_readElementalObject();

                if (!$name instanceof Zend_Pdf_Element_Name) {
                    throw new Zend_Pdf_Exception(sprintf('PDF file syntax error. Name object expected while dictionary reading. Offset - 0x%X.', $nameStart));
                }

                $dictionary->add($name, $value);
            } else {
                return $dictionary;
            }
        }

        throw new Zend_Pdf_Exception(sprintf('PDF file syntax error. Unexpected end of file while dictionary reading. Offset - 0x%X. \'>>\' expected.', $this->_current));
    }


    /**
     * Read reference PDF object
     *
     * @param string $nextLexeme
     * @return Zend_Pdf_Element_Reference
     */
    private function _readReference($nextLexeme = null)
    {
        $start = $this->_current;

        if ($nextLexeme === null) {
            $objNum = $this->_readLexeme();
        } else {
            $objNum = $nextLexeme;
        }
        if (!ctype_digit($objNum)) { // it's not a reference
            $this->_current = $start;
            return null;
        }

        $genNum = $this->_readLexeme();
        if (!ctype_digit($genNum)) { // it's not a reference
            $this->_current = $start;
            return null;
        }

        $rMark  = $this->_readLexeme();
        if ($rMark != 'R') { // it's not a reference
            $this->_current = $start;
            return null;
        }

        $ref = new Zend_Pdf_Element_Reference((int)$objNum, (int)$genNum, $this->_currentContext, $this->_objFactory);

        return $ref;
    }


    /**
     * Read numeric PDF object
     *
     * @param string $nextLexeme
     * @return Zend_Pdf_Element_Numeric
     */
    private function _readNumeric($nextLexeme = null)
    {
        if ($nextLexeme === null) {
            $nextLexeme = $this->_readLexeme();
        }

        return new Zend_Pdf_Element_Numeric($nextLexeme);
    }


    /**
     * Read inderect object from a PDF stream
     *
     * @param integer $offset
     * @param Zend_Pdf_Element_Reference_Context $context
     * @return Zend_Pdf_Element_Object
     */
    public function getObject($offset, Zend_Pdf_Element_Reference_Context $context)
    {
        if ($offset === null ) {
            return null;
        }

        $this->_pushContext();
        $this->_current        = $offset;
        $this->_currentContext = $context;
        $this->_elements       = new Zend_Pdf_PHPArray();

        $objNum = $this->_readLexeme();
        if (!ctype_digit($objNum)) {
            throw new Zend_Pdf_Exception(sprintf('PDF file syntax error. Offset - 0x%X. Object number expected.', $this->_current - strlen($objNum)));
        }

        $genNum = $this->_readLexeme();
        if (!ctype_digit($genNum)) {
            throw new Zend_Pdf_Exception(sprintf('PDF file syntax error. Offset - 0x%X. Object generation number expected.', $this->_current - strlen($genNum)));
        }

        $objKeyword = $this->_readLexeme();
        if ($objKeyword != 'obj') {
            throw new Zend_Pdf_Exception(sprintf('PDF file syntax error. Offset - 0x%X. \'obj\' keyword expected.', $this->_current - strlen($objKeyword)));
        }

        $objValue = $this->_readElementalObject();

        $nextLexeme = $this->_readLexeme();

        if( $nextLexeme == 'endobj' ) {
            /**
             * Object is not generated by factory (thus it's not marked as modified object).
             * But factory is assigned to the obect.
             */
            $obj = new Zend_Pdf_Element_Object($objValue, (int)$objNum, (int)$genNum, $this->_objFactory);

            foreach ($this->_elements as $element) {
                $element->setParentObject($obj);
            }

            $this->_popContext();
            return $obj;
        }

        /**
         * It's a stream object
         */
        if ($nextLexeme != 'stream') {
            throw new Zend_Pdf_Exception(sprintf('PDF file syntax error. Offset - 0x%X. \'endobj\' or \'stream\' keywords expected.', $this->_current - strlen($nextLexeme)));
        }

        if (!$objValue instanceof Zend_Pdf_Element_Dictionary) {
            throw new Zend_Pdf_Exception(sprintf('PDF file syntax error. Offset - 0x%X. Stream extent must be preceded by stream dictionary.', $this->_current - strlen($nextLexeme)));
        }

        /**
         * References are automatically dereferenced at this moment.
         */
        $streamLength = $objValue->Length->value;

        if ($this->_pdfString{$this->_current} != "\n" &&
            $this->_pdfString{$this->_current} != "\r"    ) {
            throw new Zend_Pdf_Exception(sprintf('PDF file syntax error. Offset - 0x%X. \'stream\' must be followed by new line marker.', $this->_current - strlen($nextLexeme)));
        }

        $this->_current++;

        if (( $this->_pdfString{$this->_current}                 == "\n" ||
              $this->_pdfString{$this->_current}                 == "\r"    )&&
            ( $this->_pdfString{$this->_current+$streamLength+1} == "\n" ||
              $this->_pdfString{$this->_current+$streamLength+1} == "\r"    )&&
            ( $this->_pdfString{$this->_current+$streamLength+2} == "\n" ||
              $this->_pdfString{$this->_current+$streamLength+2} != "\r"    )&&
            substr($this->_pdfString, $this->_current+$streamLength+3, 9) == 'endstream') {
            // Long end-of-line markers
            $obj = new Zend_Pdf_Element_Object_Stream(substr($this->_pdfString,
                                                             $this->_current+1,
                                                             $streamLength),
                                                      (int)$objNum,
                                                      (int)$genNum,
                                                      $this->_objFactory);
            $this->_current += $streamLength+3;
        } else if (( $this->_pdfString{$this->_current+$streamLength} == "\n" ||
                     $this->_pdfString{$this->_current+$streamLength} == "\r"    )&&
                   substr($this->_pdfString, $this->_current+$streamLength+1, 9) == 'endstream') {
            // Short end-of-line markers
            $obj = new Zend_Pdf_Element_Object_Stream(substr($this->_pdfString,
                                                             $this->_current,
                                                             $streamLength),
                                                      (int)$objNum,
                                                      (int)$genNum,
                                                      $this->_objFactory);
            $this->_current += $streamLength+1;
        } else if (( $this->_pdfString{$this->_current}                 == "\n" ||
                     $this->_pdfString{$this->_current}                 == "\r"    )&&
                   ( $this->_pdfString{$this->_current+$streamLength+1} == "\n" ||
                     $this->_pdfString{$this->_current+$streamLength+1} == "\r"    )&&
                   substr($this->_pdfString, $this->_current+$streamLength+2, 9) == 'endstream') {
            // First long, second short
            $obj = new Zend_Pdf_Element_Object_Stream(substr($this->_pdfString,
                                                             $this->_current+1,
                                                             $streamLength),
                                                      (int)$objNum,
                                                      (int)$genNum,
                                                      $this->_objFactory);
            $this->_current += $streamLength+2;
        } else if (( $this->_pdfString{$this->_current+$streamLength}   == "\n" ||
                     $this->_pdfString{$this->_current+$streamLength}   == "\r"    )&&
                   ( $this->_pdfString{$this->_current+$streamLength+1} == "\n" ||
                     $this->_pdfString{$this->_current+$streamLength+1} == "\r"    )&&
                   substr($this->_pdfString, $this->_current+$streamLength+2, 9) == 'endstream') {
            // First short, second long
            $obj = new Zend_Pdf_Element_Object_Stream(substr($this->_pdfString,
                                                             $this->_current,
                                                             $streamLength),
                                                      (int)$objNum,
                                                      (int)$genNum,
                                                      $this->_objFactory);
            $this->_current += $streamLength+2;
        } else {
            throw new Zend_Pdf_Exception(sprintf('PDF file syntax error. Offset - 0x%X. Stream must be enclosed with \'stream<EOL>\' and \'<EOL>endstream\' markers.', $this->_current));
        }
        $obj->dictionary = $objValue;

        $nextLexeme = $this->_readLexeme();
        if ($nextLexeme != 'endstream') {
            throw new Zend_Pdf_Exception(sprintf('PDF file syntax error. Offset - 0x%X. \'endstream\' keyword expected.', $this->_current - strlen($nextLexeme)));
        }

        $nextLexeme = $this->_readLexeme();
        if ($nextLexeme != 'endobj') {
            throw new Zend_Pdf_Exception(sprintf('PDF file syntax error. Offset - 0x%X. \'endobj\' keyword expected.', $this->_current - strlen($nextLexeme)));
        }

        foreach ($this->_elements as $element) {
            $element->setParentObject($obj);
        }
        $this->_popContext();
        return $obj;
    }


    /**
     * Get length of source PDF
     *
     * @return integer
     */
    public function getPDFLength()
    {
        return strlen($this->_pdfString);
    }

    /**
     * Get PDF String
     *
     * @return string
     */
    public function getPDFString()
    {
        return $this->_pdfString;
    }

    /**
     * Load XReference table and referenced objects
     *
     * @param integer $offset
     * @throws Zend_Pdf_Exception
     * @return Zend_Pdf_Trailer_Keeper
     */
    private function _loadXRefTable($offset)
    {
        $this->_pushContext();
        $this->_current = $offset;

        $refTable = new Zend_Pdf_Element_Reference_Table();
        $context  = new Zend_Pdf_Element_Reference_Context($this, $refTable);
        $this->_currentContext = $context;

        $nextLexeme = $this->_readLexeme();
        if ($nextLexeme == 'xref') {
            /**
             * Common cross-reference table
             */
            $this->_skipWhiteSpace();
            while ( ($nextLexeme = $this->_readLexeme()) != 'trailer' ) {
                if (!ctype_digit($nextLexeme)) {
                    throw new Zend_Pdf_Exception(sprintf('PDF file syntax error. Offset - 0x%X. Cross-reference table subheader values must contain only digits.', $this->_current-strlen($nextLexeme)));
                }
                $objNum = (int)$nextLexeme;

                $refCount = $this->_readLexeme();
                if (!ctype_digit($refCount)) {
                    throw new Zend_Pdf_Exception(sprintf('PDF file syntax error. Offset - 0x%X. Cross-reference table subheader values must contain only digits.', $this->_current-strlen($refCount)));
                }

                $this->_skipWhiteSpace();
                while ($refCount > 0) {
                    $objectOffset = substr($this->_pdfString, $this->_current, 10);
                    if (!ctype_digit($objectOffset)) {
                        throw new Zend_Pdf_Exception(sprintf('PDF file cross-reference table syntax error. Offset - 0x%X. Offset must contain only digits.', $this->_current));
                    }
                    // Force $objectOffset to be treated as decimal instead of octal number
                    for ($numStart = 0; $numStart < strlen($objectOffset)-1; $numStart++) {
                        if ($objectOffset{$numStart} != '0') {
                            break;
                        }
                    }
                    $objectOffset = substr($objectOffset, $numStart);
                    $this->_current += 10;

                    if ( !self::isWhiteSpace(ord( $this->_pdfString{$this->_current} )) ) {
                        throw new Zend_Pdf_Exception(sprintf('PDF file cross-reference table syntax error. Offset - 0x%X. Value separator must be white space.', $this->_current));
                    }
                    $this->_current++;

                    $genNumber = substr($this->_pdfString, $this->_current, 5);
                    if (!ctype_digit($objectOffset)) {
                        throw new Zend_Pdf_Exception(sprintf('PDF file cross-reference table syntax error. Offset - 0x%X. Offset must contain only digits.', $this->_current));
                    }
                    // Force $objectOffset to be treated as decimal instead of octal number
                    for ($numStart = 0; $numStart < strlen($genNumber)-1; $numStart++) {
                        if ($genNumber{$numStart} != '0') {
                            break;
                        }
                    }
                    $genNumber = substr($genNumber, $numStart);
                    $this->_current += 5;

                    if ( !self::isWhiteSpace(ord( $this->_pdfString{$this->_current} )) ) {
                        throw new Zend_Pdf_Exception(sprintf('PDF file cross-reference table syntax error. Offset - 0x%X. Value separator must be white space.', $this->_current));
                    }
                    $this->_current++;

                    $inUseKey = $this->_pdfString{$this->_current};
                    $this->_current++;

                    switch ($inUseKey) {
                        case 'f':
                            // free entry
                            unset( $this->_refTable[$objNum . ' ' . $genNumber . ' R'] );
                            $refTable->addReference($objNum . ' ' . $genNumber . ' R',
                                                    $objectOffset,
                                                    false);
                            break;

                        case 'n':
                            // in-use entry

                            $refTable->addReference($objNum . ' ' . $genNumber . ' R',
                                                    $objectOffset,
                                                    true);
                    }

                    if ( !self::isWhiteSpace(ord( $this->_pdfString{$this->_current} )) ) {
                        throw new Zend_Pdf_Exception(sprintf('PDF file cross-reference table syntax error. Offset - 0x%X. Value separator must be white space.', $this->_current));
                    }
                    $this->_current++;
                    if ( !self::isWhiteSpace(ord( $this->_pdfString{$this->_current} )) ) {
                        throw new Zend_Pdf_Exception(sprintf('PDF file cross-reference table syntax error. Offset - 0x%X. Value separator must be white space.', $this->_current));
                    }
                    $this->_current++;

                	$refCount--;
                	$objNum++;
                }
            }

            $trailerDictOffset = $this->_current;
            $trailerDict = $this->_readElementalObject();
            if (!$trailerDict instanceof Zend_Pdf_Element_Dictionary) {
                throw new Zend_Pdf_Exception(sprintf('PDF file syntax error. Offset - 0x%X.  Dictionary expected after \'trailer\' keyword.', $trailerDictOffset));
            }

            $trailerObj = new Zend_Pdf_Trailer_Keeper($trailerDict, $context);

            if ($trailerDict->Prev instanceof Zend_Pdf_Element_Numeric ||
                $trailerDict->Prev instanceof Zend_Pdf_Element_Reference ) {
                $trailerObj->setPrev($this->_loadXRefTable($trailerDict->Prev->value));
                $context->getRefTable()->setParent($trailerObj->getPrev()->getRefTable());
            }

            /**
             * We set '/Prev' dictionary property to the current cross-reference section offset.
             * It doesn't correspond to the actual data, but is true when trailer will be used
             * as trailer for next generated PDF section.
             */
            $trailerObj->Prev = new Zend_Pdf_Element_Numeric($offset);

        } else {
            throw new Zend_Exception(sprintf('Pdf file syntax error. Offset - 0x%X. \'xref\' lexeme expected.', $this->_current-strlen($nextLexeme)));

            /**
             * @todo Cross-Reference stream object must be loaded here.
             */
        }

        $this->_popContext();
        return $trailerObj;
    }


    /**
     * Get Trailer object
     *
     * @return Zend_Pdf_Trailer_Keeper
     */
    public function getTrailer()
    {
        return $this->_trailer;
    }

    /**
     * Object constructor
     *
     * @param string $pdfString
     * @param Zend_Pdf_ElementFactory $factory
     * @throws Zend_Exception
     */
    public function __construct(&$source, Zend_Pdf_ElementFactory $factory, $load)
    {
        if ($load) {
            if (($pdfFile = @fopen($source, 'rb')) === false ) {
                throw new Zend_Pdf_Exception( "Can not open '$source' file for reading." );
            }

            $byteCount = filesize($source);

            $this->_pdfString = '';
            while ( $byteCount > 0 && ($nextBlock = fread($pdfFile, $byteCount)) != false ) {
                $this->_pdfString .= $nextBlock;
                $byteCount -= strlen($nextBlock);
            }
            fclose($pdfFile);
        } else {
            $this->_pdfString  = $source;
        }
        $this->_current        = 0;
        $this->_currentContext = null;
        $this->_contextStack   = array();
        $this->_elements       = new Zend_Pdf_PHPArray();
        $this->_objFactory     = $factory;

        $pdfVersionComment = $this->_readComment();
        if (substr($pdfVersionComment, 0, 5) != '%PDF-') {
            throw new Zend_Pdf_Exception('File is not a PDF.');
        }

        $pdfVersion = (float)substr($pdfVersionComment, 5);
        if ($pdfVersion < 0.9 || $pdfVersion > 1.45) {
            /**
             * @todo
             * To support PDF versions 1.5 (Acrobat 6) and PDF version 1.7 (Acrobat 7)
             * Stream compression filter must be implemented (for compressed object streams).
             * Cross reference streams must be implemented
             */
            throw new Zend_Pdf_Exception(sprintf('Unsupported PDF version. Zend_Pdf supports PDF 1.0-1.4. Current version - \'%f\'', $pdfVersion));
        }

        $this->_current = strrpos($this->_pdfString, '%%EOF');
        if ($this->_current === false ||
            strlen($this->_pdfString) - $this->_current > 7) {
            throw new Zend_Pdf_Exception('Pdf file syntax error. End-of-fle marker expected at the end of file.');
        }

        $this->_current--;
        /**
         * Go to end of cross-reference table offset
         */
        while (self::isWhiteSpace( ord($this->_pdfString{$this->_current}) )&&
               ($this->_current > 0)) {
            $this->_current--;
        }
        /**
         * Go to the start of cross-reference table offset
         */
        while ( (!self::isWhiteSpace( ord($this->_pdfString{$this->_current}) ))&&
               ($this->_current > 0)) {
            $this->_current--;
        }
        /**
         * Go to the end of 'startxref' keyword
         */
        while (self::isWhiteSpace( ord($this->_pdfString{$this->_current}) )&&
               ($this->_current > 0)) {
            $this->_current--;
        }
        /**
         * Go to the white space (eol marker) before 'startxref' keyword
         */
        $this->_current -= 9;

        $nextLexeme = $this->_readLexeme();
        if ($nextLexeme != 'startxref') {
            throw new Zend_Pdf_Exception(sprintf('Pdf file syntax error. \'startxref\' keyword expected. Offset - 0x%X.', $this->_current-strlen($nextLexeme)));
        }

        $startXref = $this->_readLexeme();
        if (!ctype_digit($startXref)) {
            throw new Zend_Pdf_Exception(sprintf('Pdf file syntax error. Cross-reference table offset must contain only digits. Offset - 0x%X.', $this->_current-strlen($nextLexeme)));
        }

        $this->_trailer = $this->_loadXRefTable($startXref);

        $this->_objFactory->setObjectCount($this->_trailer->Size->value);
    }
}
