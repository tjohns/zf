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
 * @package    ZForm
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */


require_once('ZForm/ZFormElement.php');
require_once('ZValidator/ZValidator.php');


/**
 * @package    ZForm
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
class ZFormElementValidator {

    const GENERIC_ERROR_MSG = 'Error occured in validation!';
    const CLIENT = 1;
    const SERVER = 2;
    const BOTH   = 3;

    protected $_errorMessage;
    protected $_element;
    protected $_runat;
    protected $_name;
    protected $_options;
    protected $_validator;

    public function __construct(ZFormElement $element,
				$serverValidator = null,
				$serverValidatorOptions = null,
				$errorMsg = ZFormElementValidator::GENERIC_ERROR_MSG,
				$runat = ZFormElementValidator::SERVER) {

	$this->_errorMessage =  $errorMsg;
	$this->_element = $element;
	$this->_runat = $runat;
	$this->_options = $serverValidatorOptions;
	$this->_validator = $serverValidator;
	if ($this->_element) {
	    $this->_element->addValidator($this);
	}
    }

    public function getName() {
	return($this->_name);
    }

    public function setName($name) {
	$this->_name = $name;
    }

    public function getErrorMessage() {
	return($this->_errorMessage);
    }


    public function getElement() {
	return($this->_element);
    }

    public function getRunat() {
	return($this->_runat);
    }


    public function performValidation(ZFormElement $target) {
	if ($this->_validator && ($this->_runat & self::SERVER) != 0) {
	    $validator = new ZValidator();
	    if ($this->_options) {
		$args = array_pad($this->_options, (count($this->_options) * -1) -1, $target->getValue());
		return(call_user_func_array(array($validator, $this->_validator),
					    $args));
	    } else {
		return($validator->{$this->_validator}($target->getValue()));
	    }
	}
	return(false);
    }
    public function emitClientValidator(ZFormElement $target) {
	// VOID implementation
    }
}
?>
