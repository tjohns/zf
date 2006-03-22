<?php
/**
 *  +----------------------------------------------------------------------+
 *  | Zend Framework                                                       |
 *  +----------------------------------------------------------------------+
 *  | Copyright (c) 2005-2006 Zend Technologies Inc. (http://www.zend.com) |
 *  +----------------------------------------------------------------------+
 *  | This source file is subject to version 1.0 of the Zend Framework     |
 *  | license, that is bundled with this package in the file LICENSE, and  |
 *  | is available through the world-wide-web at the following url:        |
 *  | http://www.zend.com/license/framework/1_0.txt.                       |
 *  | If you did not receive a copy of the Zend license and are unable to  |
 *  | obtain it through the world-wide-web, please send a note to          |
 *  | license@zend.com so we can mail you a copy immediately.              |
 *  +----------------------------------------------------------------------+
 *
 * @package    Zend_Db
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2006 Zend Technologies Inc. (http://www.zend.com)
 * @license    Zend Framework License version 1.0
 */


/** Zend_Db_Adapter_Pdo_Abstract */
require_once 'Zend/Db/Adapter/Pdo/Abstract.php';


/**
 * Class for connecting to MySQL databases and performing common operations.
 *
 * @package    Zend_Db
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2006 Zend Technologies Inc. (http://www.zend.com)
 * @license    Zend Framework License version 1.0
 */
class Zend_Db_Adapter_Pdo_Mssql extends Zend_Db_Adapter_Pdo_Abstract
{
    /**
     * PDO type.
     *
     * @var string
     */
    protected $_pdoType = 'mssql';


    /**
     * Quotes an identifier.
     *
     * @param string $ident The identifier.
     * @return string The quoted identifier.
     */
    public function quoteIdentifier($ident)
    {
        return '[' . $this->quote($ident) . ']';
    }


    /**
     * Returns a list of the tables in the database.
     *
     * @return array
     */
    public function listTables()
    {
        $sql = "SELECT name FROM sysobjects WHERE type = 'U' ORDER BY name";
        return $this->fetchCol($sql);
    }


    /**
     * Returns the column descriptions for a table.
     *
     * @return array
     */
    public function describeTable($table)
    {
        $sql = "SELECT TOP 1 * FROM $table";
        return $this->_describeTable($sql);
    }


    /**
     * Adds an adapter-specific LIMIT clause to the SELECT statement.
     *
     * @todo this doesn't work currently due to the $parts[] array not being
     *       available.  Zend_Db_Select probably needs to be subclassed
     *       for this one.
     *
     * @link http://lists.bestpractical.com/pipermail/rt-devel/2005-June/007339.html
     * @return string
     */
    public function limit($sql, $count, $offset)
    {
        if ($count) {

            // we need the starting SELECT clause for later
            $select = 'SELECT '
                    . ($this->_parts['distinct'])  ? 'DISTINCT '   : ''
                    . ($this->_parts['forUpdate']) ? 'FOR UPDATE ' : '';

            // we need the length for substr() later
            $selectLen = strlen($select);

            // is there an offset?
            if (! $offset) {
                // no offset, it's a simple TOP count
                return "$select TOP $count" . substr($sql, $selectLen);
            }

            // the total of the count **and** the offset, combined.
            // this will be used in the "internal" portion of the
            // hacked-up statement.
            $total = $count + $offset;

            // build the "real" order for the external portion.
            $order = implode(',', $parts['order']);

            // build a "reverse" order for the internal portion.
            $reverse = $order;
            $reverse = str_ireplace(" ASC",  " \xFF", $reverse);
            $reverse = str_ireplace(" DESC", " ASC",  $reverse);
            $reverse = str_ireplace(" \xFF", " DESC", $reverse);

            // create a main statement that replaces the SELECT
            // with a SELECT TOP
            $main = "\n$select TOP $total" . substr($sql, $selectLen) . "\n";

            // build the hacked-up statement.
            // do we really need the "as" aliases here?
            $sql = "SELECT * FROM ("
                 . "SELECT TOP $count * FROM ($main) AS select_limit_rev ORDER BY $reverse"
                 . ") AS select_limit ORDER BY $order";

        }

        return $sql;
    }
}
