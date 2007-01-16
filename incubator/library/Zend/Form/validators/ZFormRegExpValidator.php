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


/**
 * ZFormElementValidator
 */
require_once 'ZForm/ZFormElementValidator.php';


/**
 * @package    ZForm
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
class ZFormRegExpValidator extends ZFormElementValidator
{

    const VALIDATOR_SCRIPT = '__VALIDATOR_SCRIPT__';

    protected $_clientRegExp;
    protected $_serverRegExp;


    /**
     * @todo docblock
     */
    public function __construct(ZFormELement $target)
    {
        parent::__construct($target);
    }


    /**
     * @todo docblock
     */
    public function initialize($clientRegExp,
			       $errorMessage = null,
			       $serverRegExp = null,
			       $targetDisplay = null,
			       $runat = self::BOTH)
    {
    	$this->_clientRegExp = $clientRegExp;
    	$this->_serverRegExp = $serverRegExp;

    	// @todo globals are not permitted under any circumstances
    	global $FRAMEWORK_URI;

    	$script = "<SCRIPT SRC='$FRAMEWORK_URI/ZForm/validators/" .
    	    "validator.js'></SCRIPT>\n";

    	$this->_element->getRoot()->addScriptBlock($script,
    						   self::VALIDATOR_SCRIPT);

    	return($this);
    }


    /**
     * @todo docblock
     */
    public function regExpValidator($clientRegExp,
                				    $errorMessage = null,
                				    $serverRegExp = null,
                				    $targetDisplay = null,
                				    $runat = self::BOTH)
    {

    	return($this->initialize($clientRegExp,
                                 $errorMessage,
                                 $serverRegExp,
                                 $targetDisplay,
                                 $runat));
    }


    /**
     * @todo docblock
     */
    public function performValidation(ZFormElement $target)
    {}


    /**
     * @todo docblock
     */
    public function emitClientValidator(ZFormElement $target)
    {
    	$event =
<<<EOD
	<SCRIPT>
	    new ZAjaxEngine.Validator(element, 'blur',
				      function(event) {
					  target = event.target ? event.target : event.srcElement;
					  var exp = $this->_clientRegExp;
					  var value = target.value;
					  if (! value) {
					      value = "";
					  }
					  this.clearError(target,
							  ZAjaxEngine.VALIDATION_FAILURE,
							  "$this->_name");
					  if (! value.match(exp)) {
					      this.addError(target,
							    ZAjaxEngine.VALIDATION_FAILURE,
							    "$this->_name",
							    "$this->_errorMessage");
					  }
				      });
	</SCRIPT>

EOD;
    	echo $event;
    }
}
?>
