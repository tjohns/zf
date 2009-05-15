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
 * @package    Zend_Db
 * @subpackage Table
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Zend_Db_Table_Abstract
 */
require_once 'Zend/Db/Table/Abstract.php';

/**
 * Class for SQL table interface.
 *
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Table
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_Table extends Zend_Db_Table_Abstract
{
    
    /**
     * __construct() - For concrete implementation of Zend_Db_Table
     *
     * @param string|array $config string can reference a Zend_Registry key for a db adapter
     *                             OR it can refernece the name of a table
     * @param unknown_type $definition 
     */
    public function __construct($config = array(), $definition = null)
    {
        if (is_string($config)) {
            if (Zend_Registry::isRegistered($config)) {
                $config = array(self::ADAPTER => $config);
            } else {
                $config = array(self::NAME => $config);
            }
        }
        
        if ($definition instanceof Zend_Db_Table_Definition) {
            $config[self::DEFINITION] = $definition;
        }
        
        parent::__construct($config);
    }
    
}
