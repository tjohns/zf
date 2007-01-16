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
 * ZFormWebElement
 */
require_once 'ZForm/elements/ZFormWebElement.php';


/**
 * @package    ZForm
 * @subpackage Elements
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
class ZFormInputElement extends ZFormWebElement
{

    /**
     * Class constructor. Sets up the ZForm element, types it as a 'INPUT' element.
     * This is the base class for all the INPUT types supported by ZForm.
     *
     * @param string id The optional identifier for the newly created ZFormInputElement
     * @param ZFormWebElement The optional parent of the newly create ZFormInputElement
     * The default value is null which means the ZFormInputElement is a root element.
     * @param string $type The HTML type of the input element, provided by the subclasses
     * @return     void
     */
    public function __construct($id = null, $parentNode = null, $type = null)
    {
        parent::__construct($id, $parentNode, 'INPUT');

        if ($type) {
            $this->type = $type;
        }
    }


    /**
     * Retrieve the memento which will be stored with the session data representing the
     * state of the input field. The default implemention is to simply ask the input 
     * element for its value
     *
     * @return voie
     */
    public function getMemento()
    {
        return($this->getValue());
    }


    /**
     * Restores the state of the input element from its memento which was returned by 
     * getMemento above. The default implementation is to set the value of the input
     * element to the memento.
     */
    public function setMemento($memento)
    {
        $this->setValue($memento);
    }
}
?>
