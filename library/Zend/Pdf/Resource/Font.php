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

/** Zend_Pdf_ElementFactory */
require_once 'Zend/Pdf/ElementFactory.php';

/** Zend_Pdf_Element_Object */
require_once 'Zend/Pdf/Element/Object.php';

/** Zend_Pdf_Element_Dictionary */
require_once 'Zend/Pdf/Element/Dictionary.php';

/** Zend_Pdf_Resource */
require_once 'Zend/Pdf/Resource.php';


/**
 * Font abstraction.
 *
 * Class is named not in accordance to the name convention.
 * It's "end-user" class, but its ancestor is not.
 * Thus part of the common class name is removed.
 *
 * @package    Zend_Pdf
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
abstract class Zend_Pdf_Font extends Zend_Pdf_Resource
{
    /**
     * Object constructor.
     */
    public function __construct()
    {
        parent::__construct(new Zend_Pdf_Element_Dictionary());

        $this->_resource->Type = new Zend_Pdf_Element_Name('Font');
    }

    /**
     * Convert string encoding from current locale to font encoding
     *
     * @param string $in
     * @return string
     */
    abstract public function applyEncoding($in);
}

