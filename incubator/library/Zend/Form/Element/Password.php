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
 * Password form element
 * 
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Element
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */
class Zend_Form_Element_Password extends Zend_Form_Element_Xhtml
{
    /**
     * Use formPassword view helper by default
     * @var string
     */
    public $helper = 'formPassword';

    /**
     * Override getMessages()
     *
     * Mask value in error messages
     * 
     * @return array
     */
    public function getMessages()
    {
        $value = '';

        $validators = $this->getValidators();
        if (!empty($validators)) {
            foreach ($validators as $validator) {
                $value = $validator->value;
                break;
            }
        }

        $masked = str_repeat('*', strlen($value));
        foreach ($this->_messages as $code => $message) {
            $this->_messages[$code] = str_replace($value, $masked, $message);
        }
        return $this->_messages;
    }
}
