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
 * @package    Zend_Gdata
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc.
 * (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @see Zend_Gdata_Atom_Extension_Category
 */
require_once 'Zend/Gdata/Atom/Extension/Category.php';

/**
 * Describes a books category
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc.
 * (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_Books_Extension_BooksCategory extends
    Zend_Gdata_App_Extension_Category
{

    /**
     * Constructor for Zend_Gdata_Books_Extension_BooksCategory which
     * Describes a books category
     *
     * @param DOMElement $element (optional) DOMElement from which this
     *          object should be constructed.
     */
    public function __construct($label = null, $scheme = null, $term = null,
        $value = null)
    {
        foreach (Zend_Gdata_Books::$namespaces as $nsPrefix => $nsUri) {
            $this->registerNamespace($nsPrefix, $nsUri);
        }
        parent::__construct();
        $this->_label = $label;
        $this->_scheme = $scheme;
        $this->_term = $term;
        $this->_text = $value;
    }


}

