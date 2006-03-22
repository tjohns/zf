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
    public $dictionary;


    /**
     * Object constructor
     *
     * @param mixed $val
     * @param integer $objNum
     * @param integer $genNum
     * @param Zend_Pdf_ElementFactory $factory
     * @throws Zend_Pdf_Exception
     */
    public function __construct($val, $objNum, $genNum, Zend_Pdf_ElementFactory $factory)
    {
        parent::__construct(new Zend_Pdf_Element_Stream($val), $objNum, $genNum, $factory);

        $this->dictionary = new Zend_Pdf_Element_Dictionary();
        $this->dictionary->Length = new Zend_Pdf_Element_Numeric($this->length());
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

        // Update stream length
        $this->dictionary->Length->value = $this->length();

        return  $this->_objNum + $shift . " " . $this->_genNum . " obj \n"
             .  $this->dictionary->toString($factory) . "\n"
             .  $this->_value->toString($factory) . "\n"
             . "endobj\n";
    }
}
