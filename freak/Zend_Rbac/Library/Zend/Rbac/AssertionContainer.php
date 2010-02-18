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
 * @package    Zend_Rbac
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

class Zend_Rbac_AssertionContainer extends ArrayObject {
    
    /**
     * @see ArrayAccess::offsetSet()
     */
    public function offsetSet ($key, $value)
    {
    	if(is_string($value)) {
    		if(!Zend_Loader_Autoloader::autoload($value)) {
    			throw new Zend_Rbac_Exception(
                    'Could not load the class specified'
    			);
    		}

            $value = new $value();
    	}

    	if(!is_object($value) || !$value instanceof Zend_Rbac_Assert_Interface) {
    		throw new Zend_Rbac_Exception(
    		  'Given value is no object or does not implement Zend_Rbac_Assert_Interface'
    	   );
    	}
    	
    	$key = get_class($value);
    	if(isset($this[$key])) {
    		throw new Zend_Rbac_Exception(
    		  'Assertion was already registered to this object. Cannot register twice.'
    		);
    	}
    	
    	return parent::offsetSet(get_class($value), $value);
    }
}
