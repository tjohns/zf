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
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Form_Element_Xhtml */
require_once 'Zend/Form/Element/Xhtml.php';

/**
 * Submit form element
 * 
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Element
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */
class Zend_Form_Element_Submit extends Zend_Form_Element_Xhtml
{
    /**
     * Use formSubmit view helper by default
     * @var string
     */
    protected $_defaultHelper = 'formSubmit';

    /**
     * Constructor
     * 
     * @param  string|array|Zend_Config $spec Element name or configuration
     * @param  string|array|Zend_Config $options Element value or configuration
     * @return void
     */
    public function __construct($spec, $options = null)
    {
        if (is_string($spec) && ((null !== $options) && is_string($options))) {
            $options = array('value' => $options);
        }

        parent::__construct($spec, $options);

        if (null === $this->getValue()) {
            require_once 'Zend/Form/Exception.php';
            throw new Zend_Form_Exception('Submit elements require a value; no value provided');
        }
    }

    /**
     * Return value
     *
     * If a translator is present, returns the translated value. Otherwise, 
     * returns the filtered value.
     * 
     * @return string
     */
    public function getValue()
    {
        if (null !== ($translator = $this->getTranslator())) {
            return $translator->translate($this->_value);
        }

        return parent::getValue();
    }
}
