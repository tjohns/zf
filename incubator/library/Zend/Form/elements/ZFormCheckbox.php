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
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */


/**
 * ZFormInputElement
 */
require_once 'ZForm/elements/ZFormInputElement.php';


/**
 * @package    ZForm
 * @subpackage Elements
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
class ZFormCheckbox extends ZFormInputElement
{
    protected $_checked = false;


    /**
     * Class constructor. Sets up the ZForm element, types it as a 'CHECKBOX' element
     *
     * @param string id The optional identifier for the newly created ZFormButton
     * @param ZFormWebElement The optional parent of the newly create ZFormButton.
     * The default value is null which means the ZFormButton is a root element.
     * @return     void
     */
    public function __construct($id = null, $parentNode = null)
    {
    	parent::__construct($id, $parentNode, 'CHECKBOX');
    }


    /**
     * Sets the value of the HTML checked attribute
     *
     * @param $checked boolean
     * @return void
     */
    public function setChecked($checked)
    {
    	if (! $checked) {
    	    unset($this->_attributes['checked']);
    	} else {
    	    $this->checked = null;
    	}
    	$this->_checked = $checked;
    }


    /**
     * Returns the true if the CheckBox is checked 
     *
     * @return boolean true if checked, false otherwise
     */
    public function getChecked()
    {
    	return($this->_checked ? true : false);
    }


    /**
     * Create a memento which saves the state of the Checkbox for
     * storage in the session.
     *
     * @return array 
     */
    public function getMemento()
    {
    	return(array($this->getChecked(), parent::getMemento()));
    }


    /**
     * Restores the state of the checkbox from its memento which was
     * returned by getMemento.
     *
     * @param array  restored state memento from original call to 
     * getMemento
     * @return void
     */
    public function setMemento($memento)
    {
    	if (is_array($memento) && count($memento) > 1) {
    	    $this->setChecked($memento[0]);
    	}
    }


    /**
     * The method is called when a ZForm is being processed to give the
     * ZFormCheckbox and opportunity to initialize itself even when there
     * is not data being directly targed to the ZFormWebElement
     *
     * @return void
     */
    public function loadRequestData()
    {
       	$this->setChecked(false);
        parent::loadRequestData();
    }

    /**
     * Overrides setValue in the parent to also set the checked status of
     * the ZCheckBox.
     */
    public function setValue($value)
    {
        parent::setValue($value);

    	if ($value) {
    	    $this->setChecked(true);
    	} else {
    	    $this->setChecked(false);
    	}
    }

}
?>
