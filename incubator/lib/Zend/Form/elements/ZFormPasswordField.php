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
class ZFormPasswordField extends ZFormInputElement
{

    /**
     * Class constructor. Sets up the ZForm element, types it as a 'PASSWORD' element.
     * This is the base class for all the INPUT types supported by ZForm.
     *
     * @param string id The optional identifier for the newly created ZFormPassword
     * @param ZFormWebElement The optional parent of the newly create ZFormPassword
     * The default value is null which means the ZFormPassword is a root element.
     * @return     void
     */
    public function __construct($id = null, $parentNode = null)
    {
        parent::__construct($id, $parentNode, 'PASSWORD');
    }
}