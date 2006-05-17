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
 * @subpackage Elements
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */


/**
 * ZFormInputElement
 */
require_once 'ZForm/elements/ZFormInputElement.php';


/**
 * @package    ZForm
 * @subpackage Elements
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
class ZFormRadioButton extends ZFormInputElement
{

    protected $_checked = false;

    /**
     * Class constructor. Sets up the ZForm element, types it as a 'RADIO' element.
     * This is the base class for all the INPUT types supported by ZForm.
     *
     * @param string id The optional identifier for the newly created ZFormRadio
     * @param ZFormWebElement The optional parent of the newly create ZFormRadio
     * The default value is null which means the ZFormRadio is a root element.
     * @return     void
     */
    public function __construct($id = null, $parentNode = null)
    {
        parent::__construct($id, $parentNode, 'RADIO');
    }


    /**
     * Retrieves the data associated with this element from the ZRequest object.
     *
     * @returns void
	 * @todo I don't like getting from both get and post,
	 * Options include getting parent until a form is identified
	 * Potentially pass bucket in as well
	 * I don't like searching up, because for other controls you may not
	 * be contained withing a form
	 */
    public function loadRequestData() 
    {
	$id = $this->getIDPath();
	$value  = ZRequest::get($id);

	if (! $value) {
	    $value = ZRequest::post($id);
	}
	if ($value) {
	    $this->setValue($value);
	} else {	
	    $id = $this->name;
	    $value  = ZRequest::get($id);
	    if (! $value) {
		$value = ZRequest::post($id);
	    }
	    if ($value && $value == $this->value) {
		$this->checked = true;
	    } else {
		unset($this->_attributes['checked']);
	    }
	}
	return(true);
    }


    /**
     * Default implementation of retriving the memento associated with the element
     * that will be used during persistent (@see persist())
     * The default implementation does not persist anything, we implement it here
     * so subclasses are not required to
     *
     * @return mixed null for the default implementation, subclasses should
     * override.
     */
    public function getMemento() {
	return(array($this->checked, parent::getMemento()));
    }


    /**
     * The bookend implementation to @see getMemento(). This function is a void
     * implementation of the protocol to simplify the task of subclassing
     *
     */
    public function setMemento($memento) {
	if (is_array($memento) && count($memento) > 1) {
	    $this->setChecked($memento[0]);
	}
    }


    /**
     * Overridden implementation of setValue which sets the 'value' of the 
     * attribute for the ZFormWebElement
     *
     * @param string $value
     * @return void
     */
    public function setValue($value) {
	parent::setValue($value);
	if ($value) {
	    $this->checked = true;
	} else {
	    unset($this->_attributes['checked']);
	}
    }

}