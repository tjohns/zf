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
 * @category   Zend
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: HtmlEntities.php 16217 2009-06-21 19:39:00Z thomas $
 */

/** @see Zend_Filter_Interface */
require_once 'Zend/Filter/Interface.php';

/**
 * @category   Zend
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Filter_EntityEncode implements Zend_Filter_Interface
{

    /**
     * Predefined entity references.
     *
     * @var array
     * @TODO: define UTF8 using hexadecimal notation
     * @TODO: use single quotes
     */
    public static $_entityReferences = array(
        /* special entities */
        'special' => array(
            'amp'  => '&',
            'lt'   => '<',
            'gt'   => '>',
            'quot' => '"',
        ),

        /* available on xml without any definition */
        'xml' => array(
            'amp'  => '&',
            'lt'   => '<',
            'gt'   => '>',
            'quot' => '"',
            'apos' => "'", // not available in html
        ),

        /* All HTML 4.0 entities */
        'html' => array(
            /* special entities */
            'amp'  => '&',
            'lt'   => '<',
            'gt'   => '>',
            'quot' => '"',

            /* latin-1 (since HTML 2.0/3.2) */
            'nbsp'   => ' ',
            'iexcl'  => '¡', 'iquest' => '¿',
            'curren' => '¤', 'cent'   => '¢', 'pound'  => '£', 'yen'    => '¥',
            'brvbar' => '¦',
            'sect'   => '§',
            'uml'    => '¨',
            'copy'   => '©', 'reg'    => '®',
            'ordf'   => 'ª', 'ordm'   => 'º',
            'laquo'  => '«', 'raquo'  => '»',
            'not'    => '¬',
            'shy'    => ' ',
            'macr'   => '¯',
            'deg'    => '°',
            'plusmn' => '±',
            'sup1'   => '¹', 'sup2'   => '²', 'sup3'   => '³',
            'acute'  => '´',
            'micro'  => 'µ',
            'para'   => '¶',
            'middot' => '·',
            'cedil'  => '¸',
            'frac14' => '¼', 'frac12' => '½', 'frac34' => '¾',
            'Agrave' => 'À', 'Aacute' => 'Á', 'Acirc'  => 'Â', 'Atilde' => 'Ã', 'Auml'   => 'Ä', 'Aring'  => 'Å', 'AElig'  => 'Æ',
            'agrave' => 'à', 'aacute' => 'á', 'acirc'  => 'â', 'atilde' => 'ã', 'auml'   => 'ä', 'aring'  => 'å', 'aelig'  => 'æ',
            'Ccedil' => 'Ç', 'ccedil' => 'ç',
            'Egrave' => 'È', 'Eacute' => 'É', 'Ecirc'  => 'Ê', 'Euml'   => 'Ë',
            'egrave' => 'è', 'eacute' => 'é', 'ecirc'  => 'ê', 'euml'   => 'ë',
            'Igrave' => 'Ì', 'Iacute' => 'Í', 'Icirc'  => 'Î', 'Iuml'   => 'Ï',
            'igrave' => 'ì', 'iacute' => 'í', 'icirc'  => 'î', 'iuml'   => 'ï',
            'ETH'    => 'Ð', 'eth'    => 'ð',
            'Ntilde' => 'Ñ',
            'Ograve' => 'Ò', 'Oacute' => 'Ó', 'Ocirc'  => 'Ô', 'Otilde' => 'Õ', 'Ouml'   => 'Ö',
            'ograve' => 'ò', 'oacute' => 'ó', 'ocirc'  => 'ô', 'otilde' => 'õ', 'ouml'   => 'ö',
            'times'  => '×',
            'Oslash' => 'Ø',
            'Ugrave' => 'Ù', 'Uacute' => 'Ú', 'Ucirc'  => 'Û', 'Uuml'   => 'Ü',
            'ugrave' => 'ù', 'uacute' => 'ú', 'ucirc'  => 'û', 'uuml'   => 'ü',
            'THORN'  => 'Þ', 'thorn'  => 'þ',
            'szlig'  => 'ß',
            'ntilde' => 'ñ',
            'divide' => '÷',
            'oslash' => 'ø',
            'Yacute' => 'Ý', 'yacute' => 'ý',
            'yuml'   => 'ÿ',

            /* greece (since HTML 4.0) */
            'Alpha'    => 'Α', 'alpha'   => 'α',
            'Beta'     => 'Β', 'beta'    => 'β',
            'Gamma'    => 'Γ', 'gamma'   => 'γ',
            'Delta'    => 'Δ', 'delta'   => 'δ',
            'Epsilon'  => 'Ε', 'epsilon' => 'ε',
            'Zeta'     => 'Ζ', 'zeta'    => 'ζ',
            'Eta'      => 'Η', 'eta'     => 'η',
            'Theta'    => 'Θ', 'theta'   => 'θ',
            'Iota'     => 'Ι', 'iota'    => 'ι',
            'Kappa'    => 'Κ', 'kappa'   => 'κ',
            'Lambda'   => 'Λ', 'lambda'  => 'λ',
            'Mu'       => 'Μ', 'mu'      => 'μ',
            'Nu'       => 'Ν', 'nu'      => 'ν',
            'Xi'       => 'Ξ', 'xi'      => 'ξ',
            'Omicron'  => 'Ο', 'omicron' => 'ο',
            'Pi'       => 'Π', 'pi'      => 'π',
            'Rho'      => 'Ρ', 'rho'     => 'ρ',
            'Sigma'    => 'Σ', 'sigma'   => 'σ', 'sigmaf'  => 'ς',
            'Tau'      => 'Τ', 'tau'     => 'τ',
            'Upsilon'  => 'Υ', 'upsilon' => 'υ',
            'Phi'      => 'Φ', 'phi'     => 'φ',
            'Chi'      => 'Χ', 'chi'     => 'χ',
            'Psi'      => 'Ψ', 'psi'     => 'ψ',
            'Omega'    => 'Ω', 'omega'   => 'ω',
            'thetasym' => 'ϑ',
            'upsih'    => 'ϒ',
            'piv'      => 'ϖ',

            /* math (since HTML 4.0) */
            'forall' => '∀', 'part'  => '∂', 'exist'  => '∃', 'empty' => '∅',
            'nabla'  => '∇', 'isin'  => '∈', 'notin' => '∉', 'ni'     => '∋',
            'prod'   => '∏', 'sum'    => '∑', 'minus'  => '−', 'lowast' => '∗',
            'radic'  => '√', 'prop'   => '∝', 'infin' => '∞', 'ang'    => '∠',
            'and'    => '∧', 'or'    => '∨',
            'cap'    => '∩', 'cup'    => '∪',
            'sub'    => '⊂', 'sup'   => '⊃',
            'nsub'   => '⊄',
            'sube'   => '⊆', 'supe'  => '⊇',
            'int'    => '∫',
            'there4' => '∴',
            'sim'    => '∼', 'cong'   => '≅', 'asymp' => '≈',
            'ne'     => '≠', 'equiv'  => '≡',
            'le'     => '≤', 'ge'     => '≥',
            'oplus'  => '⊕', 'otimes' => '⊗',
            'perp'   => '⊥',
            'sdot'   => '⋅',
            'loz'    => '◊',

            /* tech (since HTML 4.0) */
            'lceil' => '⌈', 'rceil' => '⌉', 'lfloor' => '⌊', 'rfloor' => '⌋',
            'lang'  => '〈', 'rang'  => '〉',

            /* arrow (since HTML 4.0) */
            'larr' => '←', 'uarr'  => '↑', 'rarr' => '→',  'darr' => '↓',
            'harr' => '↔', 'crarr' => '↵',
            'lArr' => '⇐', 'uArr'  => '⇑', 'rArr' => '⇒', 'dArr' => '⇓', 'hArr' => '⇔',

            /* div (since HTML 4.0) */
            'bull'    => '•', 'prime' => '′', 'Prime'  => '″',
            'oline'   => '‾', 'frasl' => '⁄',
            'weierp'  => '℘', 'image' => 'ℑ', 'real'   => 'ℜ',
            'trade'   => '™',
            'euro'    => '€',
            'alefsym' => 'ℵ',
            'spades'  => '♠', 'clubs' => '♣', 'hearts' => '♥', 'diams' => '♦',

            /* latin (since HTML 4.0) */
            'OElig'   => 'Œ', 'oelig'  => 'œ',
            'Scaron'  => 'Š', 'scaron' => 'š',
            'Yuml'    => 'Ÿ',
            'fnof'    => 'ƒ',

            /* punctuation (since HTML 4.0) */
            'ensp'    => ' ', 'emsp'  => ' ', 'thinsp' => ' ',
            'zwnj'    => '‌',  'zwj'   => '‍',
            'lrm'     => '‎',  'rlm'   => '‏',
            'ndash'   => '–', 'mdash' => '—',
            'lsquo'   => '‘', 'rsquo' => '’',
            'sbquo'   => '‚', // 'bsquo' => '‚',
            'ldquo'   => '“', 'rdquo' => '”',
            'bdquo'   => '„',
            'dagger'  => '†', 'Dagger' => '‡',
            'hellip'  => '…',
            'permil'  => '‰',
            'lsaquo'  => '‹', 'rsaquo' => '›',

            /* diacritical (since HTML 4.0) */
            'circ'  => 'ˆ',
            'tilde' => '˜',
        ),
    );

    /**
     * Entity reference.
     *
     * @var array
     */
    protected $_entityReference = array();

    /**
     * Character set of input value.
     *
     * @var string
     */
    protected $_inputCharSet = 'ISO-8859-1';

    /**
     * Character set of output value.
     *
     * @var string
     */
    protected $_outputCharSet = 'ISO-8859-1';

    /**
     * Use hexadecimal or numeric entities for characters not in character reference
     * and not valit for output char set or special characters.
     *
     * @var boolean
     */
    protected $_hex = false;

    /**
     * Sets filter options
     *
     * @param  integer|array $quoteStyle
     * @param  string  $charSet
     * @return void
     */
    public function __construct($options = array())
    {
        foreach ($options as $k => $v) {
            if (method_exists($this, 'set'.$k)) {
                $this->{'set'.$k}($v);
            }
        }
    }

    /**
     * Returns input character set.
     *
     * @return string
     */
    public function getInputCharSet()
    {
        return $this->_inputCharSet;
    }

    /**
     * Set input character set.
     *
     * @param  string $enc
     * @return Zend_Filter_EntityEncode Provides a fluent interface
     */
    public function setInputCharSet($enc)
    {
        $this->_inputCharSet = $enc;
        return $this;
    }

    /**
     * Returns output character set.
     *
     * @return string
     */
    public function getOutputCharSet()
    {
        return $this->_outputCharSet;
    }

    /**
     * Set output character set.
     *
     * @param  string $enc
     * @return Zend_Filter_EntityEncode Provides a fluent interface
     */
    public function setOutputCharSet($enc)
    {
        $this->_outputCharSet = $enc;
        return $this;
    }

    /**
     * Returns entity reference.
     * Format: array("<string name>" => <utf8 value>[, ...])
     *
     * @return array
     */
    public function getEntityReference() {
        return $this->_entityReference;
    }

    /**
     * Set entity reference.
     * Format: array("<string name>" => <utf8 value>[, ...])
     *    or:  name of a predefined entity reference
     *
     * @param array|string $entityReference Entity reference.
     * @return Zend_Filter_EntityEncode Provides a fluent interface
     */
    public function setEntityReference($entityReference) {
        if (!$entityReference) {
            $this->_entityReference = array();
        } elseif (isset(self::$_entityReferences[$entityReference])) {
            $this->_entityReference = self::$_entityReferences[$entityReference];
        } elseif (!is_array($entityReference)) {
            throw new Zend_Filter_Exception('Invalid entity reference: must be an array or a name of a predefined entity reference');
        } else {
            $this->_entityReference = $entityReference;
        }
        return $this;
    }

    /**
     * Get the hex option
     *
     * @return boolean
     */
    public function getHex() {
        return $this->_hex;
    }

    /**
     * Sets the hex option.
     *
     * @param bool $flag
     * @return Zend_Filter_EntityEncode Provides a fluent interface
     */
    public function setHex($flag) {
        $this->_hex = (bool)$flag;
        return $this;
    }

    /**
     * Defined by Zend_Filter_Interface
     *
     * Returns the string $value, converting characters to their corresponding HTML entity
     * equivalents where they exist
     *
     * @param  string $value
     * @return string
     */
    public function filter($value)
    {
        $value = (string)$value;

        if ($this->getInputCharSet() != 'UTF-8') {
            $value = iconv($this->getInputCharSet(), 'UTF-8', $value);
        }

        $entSearch  = array_map(array($this, '_name2Entity'), $this->getEntityReference());
        $entReplace = array_keys($this->getEntityReference());

        // convert special chars to there numeric entities
        if (!in_array('"', $entSearch)) {
            $entSearch[]  = '"';
            $entReplace[] = $this->_code2Entity(34);
        }
        if (!in_array('&', $entSearch)) {
            $entSearch[]  = '&';
            $entReplace[] = $this->_code2Entity(38);
        }
        if (!in_array("'", $entSearch)) {
            $entSearch[]  = "'";
            $entReplace[] = $this->_code2Entity(39);
        }
        if (!in_array('<', $entSearch)) {
            $entSearch[]  = '<';
            $entReplace[] = $this->_code2Entity(60);
        }
        if (!in_array('>', $entSearch)) {
            $entSearch[]  = '>';
            $entReplace[] = $this->_code2Entity(62);
        }

        $value = str_replace($entSearch, $entReplace, $value);

        // on converting to output charset we create entities only if character can't be converted.
        if ( $this->getOutputCharSet() != 'UTF-8') {
            // convert 2Byte (110xxxxx 10xxxxxx)
            $value = preg_replace_callback('/([\xc0-\xdf][\x80-\xBF])/s',  array($this, '_convertUtf82ByteMatches'), $value);

            // convert 3Byte (1110xxxx 10xxxxxx 10xxxxxx)
            $value = preg_replace_callback('/([\xe0-\xef][\x80-\xBF]{2})/s', array($this, '_convertUtf83ByteMatches'), $value);

            // convert 4Byte (11110xxx 10xxxxxx 10xxxxxx 10xxxxxx)
            $value = preg_replace_callback('/([\xf0-\xf7][\x80-\xBF]{3})/s', array($this, '_convertUtf84ByteMatches'), $value);
        }

        return $value;
    }

    protected function _convertUtf82ByteMatches(array $matches)
    {
        if ( ($char=iconv('UTF-8', $this->getOutputCharSet().'//IGNORE', $matches[1])) ) {
            return $char;
        }

        $code = (ord($matches[1][0]) - 192) * 64
              + (ord($matches[1][1]) - 128);
        return $this->_code2Entity($code);
    }

    protected function _convertUtf83ByteMatches(array $matches)
    {
        if ( ($char=iconv('UTF-8', $this->getOutputCharSet().'//IGNORE', $matches[1])) ) {
            return $char;
        }

        $code = (ord($matches[1][0]) - 224) * 4096  // 2^12
              + (ord($matches[1][1]) - 128) * 64    // 2^6
              + (ord($matches[1][2]) - 128);
        return $this->_code2Entity($code);
    }

    protected function _convertUtf84ByteMatches(array $matches)
    {
        if ( ($char=iconv('UTF-8', $this->getOutputCharSet().'//IGNORE', $matches[1])) ) {
            return $char;
        }

        $code = (ord($matches[1][0]) - 240) * 262144 // 2^18
              + (ord($matches[1][1]) - 128) * 4096   // 2^12
              + (ord($matches[1][2]) - 128) * 64     // 2^6
              + (ord($matches[1][3]) - 128);
        return $this->_code2Entity($code);
    }

    protected function _name2Entity($name)
    {
        return '&' . $name . ';';
    }

    protected function _code2Entity($code)
    {
        return ($this->_hex === false) ? '&#' . $code . ';' : '&#x' . dechex($code) . ';';
    }

}

