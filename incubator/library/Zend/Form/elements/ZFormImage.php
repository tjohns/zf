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
 * ZFormImage
 */
require_once 'ZForm/elements/ZFormInputElement.php';


/**
 * @package    ZForm
 * @subpackage Elements
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
class ZFormImage extends ZFormInputElement
{

    protected $_x;

    protected $_y;


    /**
     * Class constructor. Sets up the ZForm element, types it as a 'IMAGE' element
     *
     * @param string id The optional identifier for the newly created ZFormImage
     * @param ZFormWebElement The optional parent of the newly create ZFormentryField.
     * The default value is null which means the ZFormImage is a root element.
     * @return     void
     */
    public function __construct($id = null, $parentNode = null)
    {
    	parent::__construct($id, $parentNode, 'IMAGE');
    }


    /**
     * The methods will loaded the x & y position of the image click into the
     * ZFormImage component.
     *
     * @return void
     */
    public function loadRequestData()
    {
    	/**
    	 *
    	 * @todo I don't like getting from both get and post,
    	 * Options include getting parent until a form is identified
    	 * Potentially pass bucket in as well
    	 * I don't like searching up, because for other controls you may not
    	 *  be contained within a form
    	 */
    	$id = $this->getIDPath();
    	$x  = ZRequest::get($id . "_x");
    	if (! $x) {
    	    $x = ZRequest::post($id ."_x");
    	}
    	$y  = ZRequest::get($id . "_y");
    	if (! $y) {
    	    $y = ZRequest::post($id ."_y");
    	}
    	if ($x && $y) {
    	    $this->_x = $x;
    	    $this->_y = $y;
    	}
    	return(parent::loadRequestData());
    }


    /**
     * Create a memento which saves the state of the FormImage for
     * storage in the session.
     *
     * @return array 
     */
    public function getMemento()
    {
    	if ($this->_x && $this->_y)
    	    return(array($this->_x, $this->_y));
    	return(null);
    }


    /**
     * Restores the state of the ZFormImage from its memento which was
     * returned by getMemento.
     *
     * @param array  restored state memento from original call to 
     * getMeneto
     * @return void
     */
   public function setMemento($memento)
    {
    	if ($memento && count($memento) > 1) {
    	    $this->_x = $memento[0];
    	    $this->_y = $memento[1];
    	}
    }

}