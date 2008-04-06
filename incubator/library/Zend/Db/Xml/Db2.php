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
 * @subpackage Xml
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * @see Zend_Db_Xml_XmlContentStore
 */
require_once('Zend/Db/Xml/XmlContentStore.php');

/**
 * @see Zend_Db_Adapter_Db2
 */
require_once('Zend/Db/Adapter/Db2.php');

/**
 * The Zend_Db_Xml_XmlContentStore_Db2 represents a DB2 repository for XML Documents.
 *
 * Xrnf_Xml_XmlContentStore abstracts database persistence via convenience methods.
 * XML Data is represented by Zend_Db_Xml_XmlContent objects.  Activities to and from
 * the persistence layer are operated on these objects.
 *
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Xml
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_Xml_XmlContentStore_Db2 extends Zend_Db_Xml_XmlContentStore
{
    // only when using XMLSERIALIZE
    const XML_SIZE = '64K';
    const BLOB_SIZE = '100m';

    /**
     * Table name
     *
     * user-defined.
     * If it doesn't exist, create it
     * 
     * @var string
     */
    protected $_table;

    /**
     * Zend_Db_Xml_XmlContentStore_Db2 constructor
     * 
     * @param Zend_Db_Adapter_Db2
     */
    public function __construct($conn, $name = null)
    {
        if(!is_null($name)) {
            $this->_table = $name;
        } else {
            $this->_table = 'xmldata';
        }

        $this->_conn = $conn;
    }

    public function reset()
    {
        $sql = "drop table $this->_table";

        try {
            $this->_conn->query($sql);
        } catch (Zend_Db_Statement_Db2_Exception $e) {
            $sqlcode = 'SQL0204N';
            $pos = strpos($e->getMessage(), $sqlcode);

            if ($pos === false) {
                throw $e;
            } 
        }
        $this->createTable($this->_table);
    }

    /**
     * Create the table if it does not exist
     *
     * @param string $name
     */
    protected function createTable($name)
    {
        $sql = "create table $name(" .
               "id bigint not null generated always as identity primary key," .
               "data xml," .
               "attachment blob(" . self::BLOB_SIZE . ")," .
               "about xml)";

        $this->_conn->query($sql);

        $indexsql = array();

        $indexsql[]  = "create index datat" . time() . " on $name(data) generate key using " .
                    "xmlpattern '//*' as sql varchar hashed";
        $indexsql[] = "create index dataa" . time() . " on $name(data) generate key using " .
                    "xmlpattern '//@*' as sql varchar hashed";
        $indexsql[] = "create index aboutt" . time() . " on $name(about) generate key using " .
                    "xmlpattern '//*' as sql varchar hashed";
        $indexsql[] = "create index abouta" . time() . " on $name(about) generate key using " .
                    "xmlpattern '//@*' as sql varchar hashed";

        foreach ($indexsql as $curr) {
            $this->_conn->query($curr);
        }
    }

    /**
     * Helper function
     *
     * Check the existence of the table.
     * If the SQLCode == SQL0204N, this is a manageable error.
     * Create the table and try again.
     * 
     * @param Zend_Db_Statement_Db2_Exception
     * 
     * @return void
     */
    protected function checkTable($e)
    {
        $sqlcode = 'SQL0204N';
        $pos = strpos($e->getMessage(), $sqlcode);

        if ($pos === false) {
            throw $e;
        } else {
            $this->createTable($this->_table);
        }
    }

    public function insert($doc)
    {
        if (!is_array($doc)) {
            $docs = array($doc);
        } else {
            $docs = $doc;
        }
        $ids = 0;

        foreach($docs as $doc) {
            $xmlDoc = Zend_Db_Xml_XmlUtil::getDOM($doc, Zend_Db_Xml_XmlUtil::DATA);
            $paramArray = array();

            if (!is_null($xmlDoc)) {
                $paramArray['data'] = $xmlDoc->saveXML();
            }

            if (!is_null($doc->getAttachment())) {
                $paramArray['attachment'] = $doc->getAttachment();
            }

            if (!is_null($doc->getAbout())) {
                $paramArray['about'] = $doc->getAbout()->saveXML();
            }

            while (true) {
                try {
                    $this->_conn->insert($this->_table, $paramArray );
                    break;
                }
                catch (Zend_Db_Statement_Db2_Exception $e) {
                    $this->checkTable($e);
                }
            }

            $id = $this->_conn->lastInsertId($this->_table);
            $doc->setId($id);
            $ids++;
        }
        return $ids;
    }

    public function update($doc)
    {
        if (!is_array($doc)){
            $docs = array($doc);
        } else {
            $docs = $doc;
        }

        $numUpdated = 0;

        foreach($docs as $doc) {

            $xmlDoc = Zend_Db_Xml_XmlUtil::getDOM($doc, Zend_Db_Xml_XmlUtil::DATA);

            $paramArray = array();

            if (!is_null($xmlDoc)) {
                $paramArray['data'] = $xmlDoc->saveXML();
            }

            if (!is_null($doc->getAttachment())) {
                $paramArray['attachment'] = $doc->getAttachment();
            }

            if (!is_null($doc->getAbout())) {
                $paramArray['about'] = $doc->getAbout()->saveXML();
            }

            $where = "id=$doc->id";

            while (true) {
                try {
                    $numUpdated += $this->_conn->update($this->_table, $paramArray, $where);
                    break;
                } catch (Zend_Db_Statement_Db2_Exception $e) {
                    $this->checkTable($e);
                }
            }
        }

        return $numUpdated;
    }

    public function delete($doc)
    {
        if (!is_array($doc)) {
            $docs =  array($doc);
        } else {
            $docs = $doc;
        }

        $numDeleted = 0;

        foreach ($docs as $doc) {
            $numDeleted += $this->deleteById($doc->id);
        }

        return $numDeleted;
    }

    public function deleteById($param)
    {
        while (true) {
            try {
                return $this->_conn->delete($this->_table, "id=$param");
            } catch (Zend_Db_Statement_Db2_Exception $e) {
                $this->checkTable($e);
            }
        }
    }

    public function selectAll()
    {
        while (true) {
            try {

                $selectStr = 'SELECT id,XMLSERIALIZE(data AS CLOB(' . self::XML_SIZE . ')) AS DATA,' .
                             'XMLSERIALIZE(about AS CLOB(' . self::XML_SIZE . ')) AS ABOUT,' .
                             'attachment FROM ' . $this->_table . ' ORDER BY id';

                $dbresults = $this->_conn->fetchAll($selectStr);
                $results = $this->processList($dbresults);
                return $results;

            } catch (Zend_Db_Statement_Db2_Exception $e) {
                $this->checkTable($e);
            }
        }
    }

    /**
     * Helper function to return list
     * of documents
     *
     * @param array $dbresults of rows
     * @return Zend_Db_Xml_XmlIterator
     */
    private function processList($dbresults)
    {
        $results = new Zend_Db_Xml_XmlIterator();

        foreach($dbresults as $currRow) {
            $curr = Zend_Db_Xml_XmlUtil::getXmlResult($currRow);
            $results->add($curr);
        }

        return $results;
    }

    public function findAnywhere($strToFind, $where, $caseSensitive)
    {
        if ($where != Zend_Db_Xml_XmlUtil::ABOUT && $where != Zend_Db_Xml_XmlUtil::DATA ) {
            throw new Zend_Db_Xml_XmlException("Must Specify DATA or ABOUT XML for search");
        }

        $searchString = "SELECT id, XMLSERIALIZE(data AS CLOB(" . self::XML_SIZE . ")) as DATA," .
                        "XMLSERIALIZE(about AS CLOB(" . self::XML_SIZE . ")) as ABOUT," .
                        "attachment FROM $this->_table ";

        if ($caseSensitive) {
            $searchString .= "WHERE XMLEXISTS('\$x//*[fn:matches(text(),\$s) or " .
                             "fn:matches(attribute(),\$s)]'";
        } else {
            $searchString .= "WHERE XMLEXISTS('\$x//*[fn:matches(text(),\$s,\"i\") or " .
                             "fn:matches(attribute(),\$s,\"i\")]'";
        }
        $searchString .= " passing $where as \"x\",cast(? as varchar(50)) as \"s\")" .
                         " ORDER BY id";

        $searchParam = array();
        $searchParam[] = $strToFind;

        while (true) {
            try {
                $dbresults = $this->_conn->fetchAssoc($searchString, $searchParam);

                $results = $this->processList($dbresults);
                return $results;
            } catch (Zend_Db_Statement_Db2_Exception $e) {
                $this->checkTable($e);
            }
        }
    }

    public function find($searchParam, $where, $options)
    {
        if ($where != Zend_Db_Xml_XmlUtil::ABOUT && $where != Zend_Db_Xml_XmlUtil::DATA ) {
            throw new Zend_Db_Xml_XmlException("Must Specify DATA or ABOUT XML for search");
        }

        $elementArr = array();
        $valueArr = array();

        foreach ($searchParam as $key => $value) {
            $elementArr[] = $key;
            $valueArr[] = $value;
        }

        $searchParam = $valueArr;
        $searchString = $this->generateSearchString($elementArr, $where, $options);

        while (true) {
            try {
                $dbresults = $this->_conn->fetchAssoc($searchString, $searchParam);

                $results = $this->processList($dbresults);
                return $results;
            } catch (Zend_Db_Statement_Db2_Exception $e) {
                $this->checkTable($e);
            }
        }
    }

    /**
     * helper function
     */
    protected function generateSearchString($elementArr, $where, $options)
    {
        $caseSensitive = $options['caseSensitive'];
        $match = $options['match'];
        $logic = strtolower($options['logic']);

        $elements = "\$i//*[";
        $dataItems = "";
        $size = count($elementArr);
        for ($i = 0; $i < $size; $i++) {

            if ($match && !$caseSensitive) {
                $elements .= "fn:lower-case(" . $elementArr[$i] . ")=fn:lower-case(\$value" . $i . ")";
            } else if ($match && $caseSensitive) {
                $elements .= $elementArr[$i] . "=\$value" . $i;
            } else if (!$match && $caseSensitive) {
                $elements .= "fn:matches(" . $elementArr[$i] . "/text(),\$value" . $i . ")";
            } else {
                $elements .= "fn:matches(" . $elementArr[$i] . "/text(),\$value" . $i . ",\"i\")";
            }

            if ($i != ($size - 1 )) {
                $elements .= " $logic ";
            } else {
                $elements .= "]' passing ";
            }

            $dataItems .= "cast(? as varchar(50)) as \"value" . $i . "\"";
            if ($i != ($size - 1)) {
                $dataItems .= ",";
            } else {
                $dataItems .= ",$where as \"i\")";
            }
        }

        $searchString = "SELECT id, XMLSERIALIZE(data AS CLOB(" . self::XML_SIZE . ")) as DATA, " .
                        "XMLSERIALIZE(about AS CLOB(" . self::XML_SIZE . ")) as ABOUT," .
                        "attachment FROM $this->_table " .
                        "WHERE XMLEXISTS('" . $elements . $dataItems .
                        " ORDER BY id";

        return $searchString;
    }

    public function findById($id)
    {
        $selectStr = 'SELECT id,XMLSERIALIZE(data AS CLOB(' . self::XML_SIZE . ')) as DATA,' .
                     'XMLSERIALIZE(about AS CLOB(' . self::XML_SIZE . ')) as ABOUT,' .
                     'attachment FROM ' . $this->_table . ' WHERE id=?';

        if (is_array($id)) {
            for ( $i = 0; $i < (count($id) - 1); $i++) {
                $selectStr .= ' OR id=?';
            }
            $paramArray = $id;
        } else {
            $paramArray = array($id);
        }

        while (true) {
            try {
                $dbresults = $this->_conn->fetchAssoc($selectStr, $paramArray);
                $results = $this->processList($dbresults);

                return $results;
            } catch (Zend_Db_Statement_Db2_Exception $e) {
                $this->checkTable($e);
            }
        }
    }

    public function executeSQLPredicateQuery($where, $param=null)
    {
        $sql = 'SELECT id,XMLSERIALIZE(data AS CLOB(' . self::XML_SIZE . ')) as DATA,' .
               'XMLSERIALIZE(about AS CLOB(' . self::XML_SIZE . ')) as ABOUT,' .
               'attachment FROM ' . $this->_table . ' WHERE ' . $where . ' ORDER BY id';

        while (true) {
            try {
                $dbresults = $this->_conn->fetchAssoc($sql, $param);
                $results = $this->processList($dbresults);
                return $results;
            } catch (Zend_Db_Statement_Db2_Exception $e) {
                $this->checkTable($e);
            }
        }
    }

    /**
     * Execute a simple search using an XPath expression
     *
     * @param string $xpath, 
     * @param string $xmlLoc, DATA or ABOUT
     * @param array $param, associative array.
     * NOTE: array keys must match xquery variables used in $xpath expression.
     * 		
     * @return Zend_Db_Xml_XmlIterator
     */
    public function executeXPathPredicateQuery($xpath, $xmlLoc, $param=null)
    {
        if ($xmlLoc != Zend_Db_Xml_XmlUtil::ABOUT && $xmlLoc != Zend_Db_Xml_XmlUtil::DATA ) {
            throw new Zend_Db_Xml_XmlException("Must Specify DATA or ABOUT XML for search");
        }

        $dataItems = '';
        $size = count($param);
        $bind = null;
        if($size == 0) {
            $dataItems = " $xmlLoc as \"i\")"; 
        } else {
            $bind = array();
            $curr = 0;
            foreach ($param as $key => $value) { 
                $dataItems .= "cast(? as varchar(50)) as \"$key\"";
                $bind[] = $value;
                if ($curr != ($size - 1)) {
                    $dataItems .= ",";
                } else {
                    $dataItems .= ",$xmlLoc as \"i\")";
                }
                $curr++;
            }
        }

        $sql = 'SELECT id,XMLSERIALIZE(data AS CLOB(' . self::XML_SIZE . ')) as DATA,' .
               'XMLSERIALIZE(about AS CLOB(' . self::XML_SIZE . ')) as ABOUT,' .
               "attachment FROM $this->_table WHERE XMLEXISTS('\$i" . 
               $xpath . "' passing $dataItems ORDER BY id";

        while (true) {
            try {
                $dbresults = $this->_conn->fetchAssoc($sql, $bind);
                $results = $this->processList($dbresults);
                return $results;
            } catch (Zend_Db_Statement_Db2_Exception $e) {
                $this->checkTable($e);
            }
        }
    }
}