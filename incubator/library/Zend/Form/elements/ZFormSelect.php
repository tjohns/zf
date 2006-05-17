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
 * ZFormWebElement
 */
require_once 'ZForm/elements/ZFormWebElement.php';


/**
 * @package    ZForm
 * @subpackage Elements
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
class ZFormSelect extends ZFormWebElement
{


    /**
     * Class constructor. Sets up the ZForm element, types it as a 'SELECT' element.
     * This is the base class for all the INPUT types supported by ZForm.
     *
     * @param string id The optional identifier for the newly created ZFormSelect
     * @param ZFormWebElement The optional parent of the newly create ZFormSelect
     * The default value is null which means the ZFormSelect is a root element.
     * @return     void
     */
    public function __construct($id = null, $parentNode = null)
    {
        parent::__construct($id, $parentNode, 'SELECT');
    }


    /**
     * Abstract implementation that interates of the children of the element
     * invoking loadRequestData. During this phase of the processing cycle
     * elements should retrieve input data from the ZRequest object
     *
     * @return boolean true if all children were processed, false otherwise
     */
    public function loadRequestData() {
	$id = $this->getID();
	if ($this->multiple) {
	    $id = substr($id, 0, strlen($id) - 2);
	}
	$value  = ZRequest::get($id);
	if (! $value) {
	    $value = ZRequest::post($id);
	}
	if ($value) {
	    $this->setValue($value);
	    if (!is_array($value)) {	
		$value = array($value);
	    }
	    foreach ($this->_childNodes as $child) {
		foreach ($value as $setting) {
		    if ($setting == $child->getValue()) {
			$child->selected = true;
		    }
		}
	    }
	}
	return(true);
    }
}
?>