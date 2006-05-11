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
 * @package    Zend_XmlRpc
 * @subpackage Value
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Zend_XmlRpc_Value_Collection
 */
require_once 'Zend/XmlRpc/Value/Collection.php';


/**
 * @category   Zend
 * @package    Zend_XmlRpc
 * @subpackage Value
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_XmlRpc_Value_Struct extends Zend_XmlRpc_Value_Collection
{
    /**
     * Set the value of an struct native type
     *
     * @param array $value
     */
    public function __construct($value)
    {
        $this->_type = self::XMLRPC_TYPE_STRUCT;
        parent::__construct($value);
    }


    /**
     * Return the XML code that represent struct native MXL-RPC value
     *
     * @return string
     */
    public function getAsXML()
    {
        if (!$this->_as_xml) {   // The XML code was not calculated yet
            $this->_as_xml = '<value>'  ."\n"
                           . '<struct>' ."\n";

            if (is_array($this->_value)) {
                foreach ($this->_value as $name => $value) {
                    /* @var $value Zend_XmlRpc_Value */

                    $this->_as_xml .= '<member>'                 ."\n"
                                    . '<name>'. $name .'</name>' ."\n"
                                    . $value->getAsXML()         ."\n"
                                    . '</member>'                ."\n";
                }
            }

            $this->_as_xml .= '</struct>' ."\n"
                            . '</value>';
        }

        return $this->_as_xml;
    }
}

