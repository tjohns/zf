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
 * @package    Zend_Form
 * @subpackage Element
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Form_Element_Xhtml */
require_once 'Zend/Form/Element/Xhtml.php';

/**
 * Checkbox form element
 * 
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Element
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */
class Zend_Form_Element_Checkbox extends Zend_Form_Element_Xhtml
{
    /**
     * Is the checkbox checked?
     * @var bool
     */
    public $checked = false;

    /**
     * Use formCheckbox view helper by default
     * @var string
     */
    public $helper = 'formCheckbox';

    /**
     * Current value
     * @var int 0 or 1
     */
    protected $_value = 0;

    /**
     * Set value
     *
     * If non-null, sets checked flag to true
     * 
     * @param mixed $value 
     * @return void
     */
    public function setValue($value)
    {
        $value = ($value === null) ? 0 : 1;
        $this->checked = ($value === 0) ? false : true;
        return parent::setValue($value);
    }
}
