<?php

/** Zend_Dom_Query_Css2Xpath */
require_once 'Zend/Dom/Query/Css2Xpath.php';

/** Zend_Dom_Query_Result */
require_once 'Zend/Dom/Query/Result.php';

class Zend_Dom_Query
{
    /**#@+
     * @const string Document types
     */
    const DOC_XML   = 'docXml';
    const DOC_HTML  = 'docHtml';
    const DOC_XHTML = 'docXhtml';
    /**#@-*/

    /**
     * @var string
     */
    protected $_document;

    /**
     * Document type
     * @var string
     */
    protected $_docType;

    /**
     * Constructor
     * 
     * @param  null|string $document 
     * @return void
     */
    public function __construct($document = null)
    {
        if (null !== $document) {
            $this->setDocument($document);
        }
    }

    /**
     * Set document to query
     * 
     * @param  string $document 
     * @return Zend_Dom_Query
     */
    public function setDocument($document)
    {
        if ('<?xml' == substr(trim($document), 0, 5)) {
            return $this->setDocumentXml($document);
        }
        if (strstr($document, 'DTD XHTML')) {
            return $this->setDocumentXhtml($document);
        }
        return $this->setDocumentHtml($document);
    }

    /**
     * Register HTML document 
     * 
     * @param  string $document 
     * @return Zend_Dom_Query
     */
    public function setDocumentHtml($document)
    {
        $this->_document = (string) $document;
        $this->_docType  = self::DOC_HTML;
        return $this;
    }

    /**
     * Register XHTML document
     * 
     * @param  string $document 
     * @return Zend_Dom_Query
     */
    public function setDocumentXhtml($document)
    {
        $this->_document = (string) $document;
        $this->_docType  = self::DOC_XHTML;
        return $this;
    }

    public function setDocumentXml($document)
    {
        $this->_document = (string) $document;
        $this->_docType  = self::DOC_XML;
        return $this;
    }

    /**
     * Retrieve current document
     * 
     * @return string
     */
    public function getDocument()
    {
        return $this->_document;
    }

    /**
     * Get document type
     * 
     * @return string
     */
    public function getDocumentType()
    {
        return $this->_docType;
    }

    public function query($query)
    {
        if (null === ($document = $this->getDocument())) {
            require_once 'Zend/Dom/Exception.php';
            throw new Zend_Dom_Exception('Cannot query; no document registered');
        }

        $domDoc = new DOMDocument;
        $type   = $this->getDocumentType();
        switch ($type) {
            case self::DOC_XML:
                $success = @$domDoc->loadXML($document);
                break;
            case self::DOC_HTML:
            case self::DOC_XHTML:
            default:
                $success = @$domDoc->loadHTML($document);
                break;
        }

        if (!$success) {
            require_once 'Zend/Dom/Exception.php';
            throw new Zend_Dom_Exception('Error parsing document');
        }

        $xpathQuery = Zend_Dom_Query_Css2Xpath::transform($query);
        $nodeList   = $this->_getNodeList($domDoc, $xpathQuery);
        return new Zend_Dom_Query_Result($query, $xpathQuery, $domDoc, $nodeList);
    }

    /**
     * Prepare node list
     * 
     * @param  DOMDocument $document
     * @param  string|array $xpathQuery
     * @return array
     */
    protected function _getNodeList($document, $xpathQuery)
    {
        $xpath      = new DOMXPath($document);
        $xpathQuery = (string) $xpathQuery;
        if (strstr($xpathQuery, '[contains(@class')) {
            $nodes = $xpath->query('//*[@class]');
            foreach ($nodes as $node) {
                $class = $node->attributes->getNamedItem('class');
                $class->value = ' ' . $class->value . ' ';
            }
        }
        return $xpath->query($xpathQuery);
    }
}
