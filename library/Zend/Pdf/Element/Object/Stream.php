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


/** Zend_Pdf_Element_Object */
require_once 'Zend/Pdf/Element/Object.php';

/** Zend_Pdf_Element_Stream */
require_once 'Zend/Pdf/Element/Stream.php';


/**
 * PDF file 'stream object' element implementation
 *
 * @package    Zend_Pdf
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
class Zend_Pdf_Element_Object_Stream extends Zend_Pdf_Element_Object
{
    /**
     * StreamObject dictionary
     * Required enries:
     * Length
     *
     * @var Zend_Pdf_Element_Dictionary
     */
    private $_dictionary;

    /**
     * Flag which signals, that stream is decoded
     *
     * @var boolean
     */
    private $_streamDecoded;

    /**
     * Stored original stream object dictionary.
     * Used to decode stream during an access time.
     *
     * The only properties, which affect decoding, are sored here.
     *
     * @var array|null
     */
    private $_originalDictionary = null;

    /**
     * Object constructor
     *
     * @param mixed $val
     * @param integer $objNum
     * @param integer $genNum
     * @param Zend_Pdf_ElementFactory $factory
     * @param Zend_Pdf_Element_Dictionary|null $dictionary
     * @throws Zend_Pdf_Exception
     */
    public function __construct($val, $objNum, $genNum, Zend_Pdf_ElementFactory $factory, $dictionary = null)
    {
        parent::__construct(new Zend_Pdf_Element_Stream($val), $objNum, $genNum, $factory);

        if ($dictionary === null) {
            $this->_dictionary    = new Zend_Pdf_Element_Dictionary();
            $this->_dictionary->Length = new Zend_Pdf_Element_Numeric(strlen( $val ));
            $this->_streamDecoded = true;
        } else {
            $this->_dictionary    = $dictionary;
            $this->_streamDecoded = false;
        }
    }


    /**
     * Store original dictionary information in $_originalDictionary class member.
     * Used to store information and to normalize filters information before defiltering.
     *
     */
    private function _storeOriginalDictionary()
    {
        $this->_originalDictionary = array();

        $this->_originalDictionary['Filter']      = array();
        $this->_originalDictionary['DecodeParms'] = array();
        if ($this->_dictionary->Filter === null) {
            // Do nothing.
        } else if ($this->_dictionary->Filter->getType() == Zend_Pdf_Element::TYPE_ARRAY) {
            foreach ($this->_dictionary->Filter->items as $id => $filter) {
                $this->_originalDictionary['Filter'][$id]      = $filter->value;
                $this->_originalDictionary['DecodeParms'][$id] = array();

                if ($this->_dictionary->DecodeParms !== null ) {
                    if ($this->_dictionary->DecodeParms->items[$id] !== null &&
                        $this->_dictionary->DecodeParms->items[$id]->value !== null ) {
                        foreach ($this->_dictionary->DecodeParms->items[$id]->getKeys() as $paramKey) {
                            $this->_originalDictionary['DecodeParms'][$id][$paramKey] =
                                  $this->_dictionary->DecodeParms->items[$id]->$paramKey->value;
                        }
                    }
                }
            }
        } else {
            $this->_originalDictionary['Filter'][0]      = $this->_dictionary->Filter->value;
            $this->_originalDictionary['DecodeParms'][0] = array();
            if ($this->_dictionary->DecodeParms !== null ) {
                foreach ($this->_dictionary->DecodeParms->getKeys() as $paramKey) {
                    $this->_originalDictionary['DecodeParms'][0][$paramKey] =
                          $this->_dictionary->DecodeParms->$paramKey->value;
                }
            }
        }

        if ($this->_dictionary->F !== null) {
            $this->_originalDictionary['F'] = $this->_dictionary->F->value;
        }

        $this->_originalDictionary['FFilter']      = array();
        $this->_originalDictionary['FDecodeParms'] = array();
        if ($this->_dictionary->FFilter === null) {
            // Do nothing.
        } else if ($this->_dictionary->FFilter->getType() == Zend_Pdf_Element::TYPE_ARRAY) {
            foreach ($this->_dictionary->FFilter->items as $id => $filter) {
                $this->_originalDictionary['FFilter'][$id]      = $filter->value;
                $this->_originalDictionary['FDecodeParms'][$id] = array();

                if ($this->_dictionary->FDecodeParms !== null ) {
                    if ($this->_dictionary->FDecodeParms->items[$id] !== null &&
                        $this->_dictionary->FDecodeParms->items[$id]->value !== null) {
                        foreach ($this->_dictionary->FDecodeParms->items[$id]->getKeys() as $paramKey) {
                            $this->_originalDictionary['FDecodeParms'][$id][$paramKey] =
                                  $this->_dictionary->FDecodeParms->items[$id]->items[$paramKey]->value;
                        }
                    }
                }
            }
        } else {
            $this->_originalDictionary['FFilter'][0]      = $this->_dictionary->FFilter->value;
            $this->_originalDictionary['FDecodeParms'][0] = array();
            if ($this->_dictionary->FDecodeParms !== null ) {
                foreach ($this->_dictionary->FDecodeParms->getKeys() as $paramKey) {
                    $this->_originalDictionary['FDecodeParms'][0][$paramKey] =
                          $this->_dictionary->FDecodeParms->items[$paramKey]->value;
                }
            }
        }
    }


    /**
     * Decode data encoded by ASCIIHexDecode filter
     *
     * @param string $input
     * @return string
     * @throws Zend_Pdf_Exception
     */
    private static function _ASCIIHexDecode(&$input)
    {
        $output  = '';
        $oddCode = true;
        $commentMode = false;

        for ($count = 0; $count < strlen($input)  &&  $input{$count} != '>'; $count++) {
            $charCode = ord($input{$count});

            if ($commentMode) {
                if ($charCode == 0x0A  || $charCode == 0x0D ) {
                    $commentMode = false;
                }

                continue;
            }

            switch ($charCode) {
                //Skip white space
                case 0x00: // null character
                    // fall through to next case
                case 0x09: // Tab
                    // fall through to next case
                case 0x0A: // Line feed
                    // fall through to next case
                case 0x0C: // Form Feed
                    // fall through to next case
                case 0x0D: // Carriage return
                    // fall through to next case
                case 0x20: // Space
                    // Do nothing
                    break;

                case 0x25: // '%'
                    // Switch to comment mode
                    $commentMode = true;
                    break;

                default:
                    if ($charCode >= 0x30 /*'0'*/ && $charCode <= 0x39 /*'9'*/) {
                        $code = $charCode - 0x30;
                    } else if ($charCode >= 0x41 /*'A'*/ && $charCode <= 0x46 /*'F'*/) {
                        $code = $charCode - 0x37/*0x41 - 0x0A*/;
                    } else if ($charCode >= 0x61 /*'a'*/ && $charCode <= 0x66 /*'f'*/) {
                        $code = $charCode - 0x57/*0x61 - 0x0A*/;
                    } else {
                        throw new Zend_Pdf_Exception('Wrong character in a encoded stream');
                    }

                    if ($oddCode) {
                        $hexCodeHigh = $code;
                    } else {
                        $output .= chr($hexCodeHigh*16 + $code);
                    }

                    $oddCode = !$oddCode;

                    break;
            }
        }

        /* Check that stream is terminated by End Of Data marker */
        if ($input{$count} != '>') {
            throw new Zend_Pdf_Exception('Wrong character in a encoded stream');
        }

        /* Last '0' character is omitted */
        if (!$oddCode) {
            $output .= chr($hexCodeHigh*16);
        }

        return $output;
    }

    /**
     * Encode data by ASCIIHexDecode filter
     *
     * @param string $input
     * @return string
     * @throws Zend_Pdf_Exception
     */
    private static function _ASCIIHexEncode(&$input)
    {
        return bin2hex($input) . '>';
    }



    /**
     * Decode data encoded by ASCII85Decode filter
     *
     * @param string $input
     * @return string
     * @throws Zend_Pdf_Exception
     */
    private static function _ASCII85Decode(&$input)
    {
        throw new Zend_Pdf_Exception('Not implemented yet');
    }

    /**
     * Encode data by ASCII85Decode filter
     *
     * @param string $input
     * @return string
     * @throws Zend_Pdf_Exception
     */
    private static function _ASCII85Encode(&$input)
    {
        throw new Zend_Pdf_Exception('Not implemented yet');
    }


    /**
     * Convert stream data according to the filter param set.
     *
     * @param array $params
     * @param boolean $decode
     */
    private function _applyParams($params, $decode) {
        if ($decode) {
            if (isset($params['Predictor'])) {
                if ($params['Predictor'] == 2) {
                    ;
                }
            }
        }
    }

    /**
     * Decode data encoded by FlateDecode filter
     *
     * @param string $input
     * @return string
     * @throws Zend_Pdf_Exception
     */
    private static function _FlateDecode(&$input)
    {
        if (extension_loaded('zlib')) {
            return gzuncompress($input);
        } else {
            throw new Zend_Pdf_Exception('Not implemented yet');
        }
    }

    /**
     * Encode data by FlateDecode filter
     *
     * @param string $input
     * @return string
     * @throws Zend_Pdf_Exception
     */
    private static function _FlateEncode(&$input)
    {
        throw new Zend_Pdf_Exception('Not implemented yet');
    }

    /**
     * Decode stream
     *
     * @throws Zend_Pdf_Exception
     */
    private function _decodeStream()
    {
        if ($this->_originalDictionary === null) {
            $this->_storeOriginalDictionary();
        }

        /**
         * All applied stream filters must be processed to decode stream.
         * If we don't recognize any of applied filetrs an exception should be thrown here
         */
        if (isset($this->_originalDictionary['F'])) {
            /** @todo Check, how external files can be processed. */
            throw new Zend_Pdf_Exception('External filters are not supported now.');
        }

        foreach ($this->_originalDictionary['Filter'] as $id => $filterName ) {
            switch ($filterName) {
                case 'ASCIIHexDecode':
                    $this->_value->value = self::_ASCIIHexDecode($this->_value->value);
                    break;

                case 'ASCII85Decode':
                    $this->_value->value = self::_ASCII85Decode($this->_value->value);
                    break;

                case 'FlateDecode':
                    $this->_value->value = self::_FlateDecode($this->_value->value);
                    if (count($this->_originalDictionary['DecodeParms'][$id]) != 0) {
                        $this->_applyParams($this->_originalDictionary['DecodeParms'][$id], true);
                    }
                    break;

                default:
                    throw new Zend_Pdf_Exception('Unknown stream filter: \'' . $filterName . '\'.');
            }
        }

        $this->_streamDecoded = true;
    }

    /**
     * Encode stream
     *
     * @throws Zend_Pdf_Exception
     */
    private function _encodeStream()
    {
        /**
         * All applied stream filters must be processed to encode stream.
         * If we don't recognize any of applied filetrs an exception should be thrown here
         */
        if (isset($this->_originalDictionary['F'])) {
            /** @todo Check, how external files can be processed. */
            throw new Zend_Pdf_Exception('External filters are not supported now.');
        }

        $filters = array_reverse($this->_originalDictionary['Filter'], true);

        foreach ($filters as $id => $filterName ) {
            switch ($filterName) {
                case 'ASCIIHexDecode':
                    $this->_value->value = self::_ASCIIHexEncode($this->_value->value);
                    break;

                case 'ASCII85Decode':
                    $this->_value->value = self::_ASCII85Encode($this->_value->value);
                    break;

                case 'FlateDecode':
                    if (count($this->_originalDictionary['DecodeParms'][$id]) != 0) {
                        $this->_applyParams($this->_originalDictionary['DecodeParms'][$id], false);
                    }
                    $this->_value->value = self::_FlateEncode($this->_value->value);
                    break;

                default:
                    throw new Zend_Pdf_Exception('Unknown stream filter: \'' . $filterName . '\'.');
            }
        }

        $this->_streamDecoded = true;
    }

    /**
     * Get handler
     *
     * @param string $property
     * @return mixed
     * @throws Zend_Pdf_Exception
     */
    public function __get($property)
    {
        if ($property == 'dictionary') {
            /**
             * If dtream is note decoded yet, then store original decoding options (do it only once).
             */
            if (( !$this->_streamDecoded ) && ($this->_originalDictionary === null)) {
                $this->_storeOriginalDictionary();
            }

            return $this->_dictionary;
        }

        if ($property == 'value') {
            if (!$this->_streamDecoded) {
                $this->_decodeStream();
            }

            return $this->_value->value;
        }

        throw new Zend_Pdf_Exception('Unknown stream object property requested.');
    }


    /**
     * Set handler
     *
     * @param string $property
     * @param  mixed $value
     */
    public function __set($property, $value)
    {
        if ($property == 'value') {
            $this->_value->value  = $value;
            $this->_streamDecoded = true;

            return;
        }

        throw new Zend_Pdf_Exception('Unknown stream object property: \'' . $property . '\'.');
    }


    /**
     * Treat stream data as already encoded
     */
    public function skipFilters()
    {
        $this->_streamDecoded = false;
    }


    /**
     * Call handler
     *
     * @param string $method
     * @param array  $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (!$this->_streamDecoded) {
            $this->_decodeStream();
        }

        switch (count($args)) {
            case 0:
                return $this->_value->$method();
            case 1:
                return $this->_value->$method($args[0]);
            default:
                throw new Zend_Pdf_Exception('Unsupported number of arguments');
        }
    }

    /**
     * Dump object to a string to save within PDF file
     *
     * $factory parameter defines operation context.
     *
     * @param Zend_Pdf_ElementFactory $factory
     * @return string
     */
    public function dump(Zend_Pdf_ElementFactory $factory)
    {
        $shift = $factory->getEnumerationShift($this->_factory);

        if ($this->_streamDecoded) {
            $this->_storeOriginalDictionary();
            $this->_encodeStream();
        } else if ($this->_originalDictionary != null) {
            $startDictionary = $this->_originalDictionary;
            $this->_storeOriginalDictionary();
            $newDictionary = $this->_originalDictionary;

            if ($startDictionary !== $newDictionary) {
                $this->_originalDictionary = $startDictionary;
                $this->_decodeStream();

                $this->_originalDictionary = $newDictionary;
                $this->_encodeStream();
            }
        }

        // Update stream length
        $this->dictionary->Length->value = $this->_value->length();

        return  $this->_objNum + $shift . " " . $this->_genNum . " obj \n"
             .  $this->dictionary->toString($factory) . "\n"
             .  $this->_value->toString($factory) . "\n"
             . "endobj\n";
    }
}
