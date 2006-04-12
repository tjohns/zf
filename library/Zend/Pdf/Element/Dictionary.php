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


/** Zend_Pdf_Element */
require_once 'Zend/Pdf/Element.php';


/**
 * PDF file 'dictionary' element implementation
 *
 * @package    Zend_Pdf
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
class Zend_Pdf_Element_Dictionary extends Zend_Pdf_Element
{
    /**
     * Dictionary elements
     * Array of Zend_Pdf_Element objects ('name' => Zend_Pdf_Element)
     *
     * @var array
     */
    public $_items = array();


    /**
     * Object constructor
     *
     * @param array $val   - array of Zend_Pdf_Element objects
     * @throws Zend_Pdf_Exception
     */
    public function __construct($val = null)
    {
        if ($val === null) {
            return;
        } else if (!is_array($val)) {
            throw new Zend_Pdf_Exception('Argument must be an array');
        }

        foreach ($val as $name => $element) {
            if (!$element instanceof Zend_Pdf_Element) {
                throw new Zend_Pdf_Exception('Array elements must be Zend_Pdf_Element objects');
            }
            if (!is_string($name)) {
                throw new Zend_Pdf_Exception('Array keys must be strings');
            }
            $this->_items[$name] = $element;
        }
    }


    /**
     * Add element to an array
     *
     * @name Zend_Pdf_Element_Name $name
     * @param Zend_Pdf_Element $val   - Zend_Pdf_Element object
     * @throws Zend_Pdf_Exception
     */
    public function add(Zend_Pdf_Element_Name $name, Zend_Pdf_Element $val)
    {
        $this->_items[$name->value] = $val;
    }

    /**
     * Return dictionary keys
     *
     * @return array
     */
    public function getKeys()
    {
        return array_keys($this->_items);
    }


    /**
     * Get handler
     *
     * @param string $property
     * @return Zend_Pdf_Element | null
     */
    public function __get($item)
    {
        if (isset($this->_items[$item])) {
            return $this->_items[$item];
        } else {
            return null;
        }
    }

    /**
     * Set handler
     *
     * @param string $property
     * @param  mixed $value
     */
    public function __set($item, $value)
    {
        if ($value === null) {
            unset($this->_items[$item]);
        } else {
            $this->_items[$item] = $value;
        }
    }

    /**
     * Return type of the element.
     *
     * @return integer
     */
    public function getType()
    {
        return Zend_Pdf_Element::TYPE_DICTIONARY;
    }


    /**
     * Return object as string
     *
     * @param Zend_Pdf_Factory $factory
     * @return string
     */
    public function toString($factory = null)
    {
        $outStr = '<<';
        $lastNL = 0;

        foreach ($this->_items as $name => $element) {
            if (!is_object($element)) {
                throw new Zend_Pdf_Exception('Wrong data');
            }

            if (strlen($outStr) - $lastNL > 128)  {
                $outStr .= "\n";
                $lastNL = strlen($outStr);
            }

            $nameObj = new Zend_Pdf_Element_Name($name);
            $outStr .= $nameObj->toString($factory) . ' ' . $element->toString($factory) . ' ';
        }
        $outStr .= '>>';

        return $outStr;
    }
}
