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


// @todo fix request when supported in full framework
require_once('ZRequest/ZRequest.php');

$FRAMEWORK_URI = '/framework/lib';

/**
 * @package ZForm
 */

require_once('Zend.php');
require_once('ZForm/ZFormElement.php');
require_once('ZForm/ZFormDynamicElement.php');
require_once('ZForm/ZFormElementEvent.php');
require_once('ZForm/ZFormElementBehavior.php');
require_once('ZForm/ZFormElementValidator.php');


/**
 * @package    ZForm
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
class ZFormFactory {

    /**
     * Constants used to specify the locations of the various directory lists
     * used to locate and load the element/validators/behaviors from
     */
    public static  $ELEMENT_DIR_NAMES = 'elementDirs';
    public static  $VALIDATOR_DIR_NAMES = 'validatorDirs';
    public static  $BEHAVIOR_DIR_NAMES = 'behaviorDirs';

    private static $ELEMENT_FACTORY_DIRS   = 'ZForm/elements';
    private static $VALIDATOR_FACTORY_DIRS = 'ZForm/validators';
    private static $BEHAVIOR_FACTORY_DIRS  = 'ZForm/behaviors';

    /**
     * The factory needs to be initialize so that is may contribute the
     * framework javascript files to the output. @todo fix this
     */
    private static $INITED = false;

    public static function init($initObject = null) {
	global $FRAMEWORK_URI;
	if (! self::$INITED) {
	    // @todo fix this
	    echo "<SCRIPT SRC='$FRAMEWORK_URI/ZForm/javascript/class.js'>" .
		"</SCRIPT>\n";
	    echo "<SCRIPT SRC='$FRAMEWORK_URI/ZForm/javascript/ZAjax.js'>" .
		"</SCRIPT>\n";
	    self::$INITED = true;
	}
	if ($initObject) {
	    if (isset($initObject[self::$ELEMENT_DIR_NAMES])) {
		self::$ELEMENT_FACTORY_DIRS  =  $initObject[self::$ELEMENT_DIR_NAMES];
	    }
	    if (isset($initObject[self::$VALIDATOR_DIR_NAMES])) {
		self::$VALIDATOR_FACTORY_DIRS  =  $initObject[self::$VALIDATOR_DIR_NAMES];
	    }
	    if (isset($initObject[self::$BEHAVIOR_DIR_NAMES])) {
		self::$BEHAVIOR_FACTORY_DIRS  =  $initObject[self::$BEHAVIOR_DIR_NAMES];
	    }
	}
    }
    public static function loadElement($elementClassName,
				       $id,
				       $parentNode = null,
				       $wrapExisting = false,
				       $dynamic = false) {

	Zend::loadClass($elementClassName, self::$ELEMENT_FACTORY_DIRS);
	$newObject = new $elementClassName($id, $parentNode);
	if (! ($newObject instanceof ZFormElement)) {
	    throw new ZFormElementException("$elementClassName is not an " .
					    'instance of ZFormElement');
	}
	$newObject->setWrapExisting($wrapExisting);
	return($dynamic ? new ZFormDynamicElement($newObject) : $newObject);
    }

    public static function wrapElement($elementClassName,
				       $id,
				       $parentNode = null,
				       $dynamic = false) {
	return(self::loadElement($elementClassName, $id, $parentNode, true, $dynamic));
    }
    public static function loadValidator($validatorClassName, ZFormElement $target) {


	Zend::loadClass($validatorClassName, self::$VALIDATOR_FACTORY_DIRS);

	$newObject = new $validatorClassName($target);
	if (! ($newObject instanceof ZFormElementValidator)) {
	    throw new ZFormElementException("$validatorClassName is not an " .
					   'instance of ZFormElementValidator');
	}
	return($newObject);
    }

    public static function loadBehavior($behaviorClassName, $targetElement) {

	Zend::loadClass($behaviorClassName, self::$BEHAVIOR_FACTORY_DIRS);
	$newObject = new $behaviorClassName($targetElement);
	if (! ($newObject instanceof ZFormElementBehavior)) {
	    throw new ZFormElementException("$behaviorClassName is not an " .
					    'instance of ZFormElementBehavior');
	}
	return($newObject);
    }
}

?>
