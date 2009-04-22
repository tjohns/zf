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
 * @package    Zend_Search_Lucene
 * @subpackage Document
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/** Zend_Search_Lucene_Document */
require_once 'Zend/Search/Lucene/Document.php';


/**
 * HTML document.
 *
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage Document
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Search_Lucene_Document_Html extends Zend_Search_Lucene_Document
{
    /**
     * List of document links
     *
     * @var array
     */
    private $_links = array();

    /**
     * List of document header links
     *
     * @var array
     */
    private $_headerLinks = array();

    /**
     * Stored DOM representation
     *
     * @var DOMDocument
     */
    private $_doc;

    /**
     * Exclud nofollow links flag
     *
     * If true then links with rel='nofollow' attribute are not included into
     * document links.
     *
     * @var boolean
     */
    private static $_excludeNoFollowLinks = false;

    /**
     * Object constructor
     *
     * @param string  $data         HTML string (may be HTML fragment, )
     * @param boolean $isFile
     * @param boolean $storeContent
     */
    private function __construct($data, $isFile, $storeContent/*, $encoding*/)
    {
        $this->_doc = new DOMDocument();
        $this->_doc->substituteEntities = true;

        if ($isFile) {
            $htmlData = file_get_contents($data);
        } else {
            $htmlData = $data;
        }
        @$this->_doc->loadHTML($htmlData);

//        // Set encoding if it's specified
//        if ($encoding !== null) {
//        	$this->_doc->encoding = $encoding;
//        }

        $xpath = new DOMXPath($this->_doc);

        $docTitle = '';
        $titleNodes = $xpath->query('/html/head/title');
        foreach ($titleNodes as $titleNode) {
            // title should always have only one entry, but we process all nodeset entries
            $docTitle .= $titleNode->nodeValue . ' ';
        }
        $this->addField(Zend_Search_Lucene_Field::Text('title', $docTitle, $this->_doc->encoding));

        $metaNodes = $xpath->query('/html/head/meta[@name]');
        foreach ($metaNodes as $metaNode) {
            $this->addField(Zend_Search_Lucene_Field::Text($metaNode->getAttribute('name'),
                                                           $metaNode->getAttribute('content'),
                                                           $this->_doc->encoding));
        }

        $docBody = '';
        $bodyNodes = $xpath->query('/html/body');
        foreach ($bodyNodes as $bodyNode) {
            // body should always have only one entry, but we process all nodeset entries
            $this->_retrieveNodeText($bodyNode, $docBody);
        }
        if ($storeContent) {
            $this->addField(Zend_Search_Lucene_Field::Text('body', $docBody, $this->_doc->encoding));
        } else {
            $this->addField(Zend_Search_Lucene_Field::UnStored('body', $docBody, $this->_doc->encoding));
        }

        $linkNodes = $this->_doc->getElementsByTagName('a');
        foreach ($linkNodes as $linkNode) {
            if (($href = $linkNode->getAttribute('href')) != '' &&
                (!self::$_excludeNoFollowLinks  ||  strtolower($linkNode->getAttribute('rel')) != 'nofollow' )
               ) {
                $this->_links[] = $href;
            }
        }
        $this->_links = array_unique($this->_links);

        $linkNodes = $xpath->query('/html/head/link');
        foreach ($linkNodes as $linkNode) {
            if (($href = $linkNode->getAttribute('href')) != '') {
                $this->_headerLinks[] = $href;
            }
        }
        $this->_headerLinks = array_unique($this->_headerLinks);
    }

    /**
     * Set exclude nofollow links flag
     *
     * @param boolean $newValue
     */
    public static function setExcludeNoFollowLinks($newValue)
    {
        self::$_excludeNoFollowLinks = $newValue;
    }

    /**
     * Get exclude nofollow links flag
     *
     * @return boolean
     */
    public static function getExcludeNoFollowLinks()
    {
        return self::$_excludeNoFollowLinks;
    }

    /**
     * Get node text
     *
     * We should exclude scripts, which may be not included into comment tags, CDATA sections,
     *
     * @param DOMNode $node
     * @param string &$text
     */
    private function _retrieveNodeText(DOMNode $node, &$text)
    {
        if ($node->nodeType == XML_TEXT_NODE) {
            $text .= $node->nodeValue ;
            $text .= ' ';
        } else if ($node->nodeType == XML_ELEMENT_NODE  &&  $node->nodeName != 'script') {
            foreach ($node->childNodes as $childNode) {
                $this->_retrieveNodeText($childNode, $text);
            }
        }
    }

//    /**
//     * Get encoding
//     *
//     * Document encoding is automatically recognized by DOMDocument::loadHTML() method,
//     * but it may be overriden overridden with setEncoding() method or additional
//     * constructor parameter.
//     *
//     * @return string
//     */
//    public function getEncoding()
//    {
//        return $this->_doc->encoding;
//    }
//
//    /**
//     * Set encoding
//     *
//     * Document encoding is automatically recognized by DOMDocument::loadHTML() method,
//     * but it may be overriden overridden with setEncoding() method or additional
//     * constructor parameter.
//     *
//     * @param string $encoding
//     */
//    public function setEncoding($encoding)
//    {
//        $this->_doc->encoding = $encoding;
//    }

    /**
     * Get document HREF links
     *
     * @return array
     */
    public function getLinks()
    {
        return $this->_links;
    }

    /**
     * Get document header links
     *
     * @return array
     */
    public function getHeaderLinks()
    {
        return $this->_headerLinks;
    }

    /**
     * Load HTML document from a string
     *
     * @param string  $data
     * @param boolean $storeContent
     * @return Zend_Search_Lucene_Document_Html
     */
    public static function loadHTML($data, $storeContent = false/*, $encoding = null*/)
    {
        return new Zend_Search_Lucene_Document_Html($data, false, $storeContent/*, $encoding*/);
    }

    /**
     * Load HTML document from a file
     *
     * @param string  $file
     * @param boolean $storeContent
     * @return Zend_Search_Lucene_Document_Html
     */
    public static function loadHTMLFile($file, $storeContent = false/*, $encoding = null*/)
    {
        return new Zend_Search_Lucene_Document_Html($file, true, $storeContent/*, $encoding*/);
    }


    /**
     * Highlight text in text node
     *
     * @param DOMText $node
     * @param array   $wordsToHighlight
     * @param callback $callback   Callback method, used to transform (highlighting) text.
     * @param array    $params     Array of additionall callback parameters (first non-optional parameter is a text to transform)
     * @throws Zend_Search_Lucene_Exception
     */
    protected function _highlightTextNode(DOMText $node, $wordsToHighlight, $callback, $params)
    {
        $analyzer = Zend_Search_Lucene_Analysis_Analyzer::getDefault();
        $analyzer->setInput($node->nodeValue, $this->_doc->encoding);

        $matchedTokens = array();

        while (($token = $analyzer->nextToken()) !== null) {
            if (isset($wordsToHighlight[$token->getTermText()])) {
                $matchedTokens[] = $token;
            }
        }

        if (count($matchedTokens) == 0) {
            return;
        }

        $matchedTokens = array_reverse($matchedTokens);

        foreach ($matchedTokens as $token) {
            // Cut text after matched token
            $node->splitText($token->getEndOffset());

            // Cut matched node
            $matchedWordNode = $node->splitText($token->getStartOffset());

            // Retrieve HTML string representation for highlihted word
            $fullCallbackparamsList = $params;
            array_unshift($fullCallbackparamsList, $matchedWordNode->nodeValue);
            $highlightedWordNodeSetHtml = call_user_func_array($callback, $fullCallbackparamsList);

            // Transform HTML string to a DOM representation and automatically transform retrieved string
            // into valid XHTML (It's automatically done by loadHTML() method)
            $highlightedWordNodeSetDomDocument = new DOMDocument('1.0', $this->_doc->encoding);
            if ($this->_doc->encoding !== null  && $this->_doc->encoding != '') {
            	$charSetMetaEquiv = '<meta http-equiv="Content-type" content="text/html; charset=' . $this->_doc->encoding . '"/>';
            } else {
            	$charSetMetaEquiv = '';
            }
            $success = @$highlightedWordNodeSetDomDocument->
                                loadHTML('<html>'
                                       .   '<head>' . $charSetMetaEquiv . '</head>'
                                       .   '<body>' . $highlightedWordNodeSetHtml . '</body>'
                                       . '</html>');
            if (!$success) {
            	require_once 'Zend/Search/Lucene/Exception.php';
            	throw new Zend_Search_Lucene_Exception("Error occured while loading highlighted text fragment: '$highlightedNodeHtml'.");
            }
            $highlightedWordNodeSetXpath = new DOMXPath($highlightedWordNodeSetDomDocument);
            $highlightedWordNodeSet      = $highlightedWordNodeSetXpath->query('/html/body')->item(0)->childNodes;

            for ($count = 0; $count < $highlightedWordNodeSet->length; $count++) {
            	$nodeToImport = $highlightedWordNodeSet->item($count);
            	$node->parentNode->insertBefore($this->_doc->importNode($nodeToImport, true /* deep copy */),
            	                                $matchedWordNode);
            }

            $node->parentNode->removeChild($matchedWordNode);
        }
    }


    /**
     * highlight words in content of the specified node
     *
     * @param DOMNode $contextNode
     * @param array $wordsToHighlight
     * @param callback $callback   Callback method, used to transform (highlighting) text.
     * @param array    $params     Array of additionall callback parameters (first non-optional parameter is a text to transform)
     */
    protected function _highlightNodeRecursive(DOMNode $contextNode, $wordsToHighlight, $callback, $params)
    {
        $textNodes = array();

        if (!$contextNode->hasChildNodes()) {
            return;
        }

        foreach ($contextNode->childNodes as $childNode) {
            if ($childNode->nodeType == XML_TEXT_NODE) {
                // process node later to leave childNodes structure untouched
                $textNodes[] = $childNode;
            } else {
                // Process node if it's not a script node
                if ($childNode->nodeName != 'script') {
                    $this->_highlightNodeRecursive($childNode, $wordsToHighlight, $callback, $params);
                }
            }
        }

        foreach ($textNodes as $textNode) {
            $this->_highlightTextNode($textNode, $wordsToHighlight, $callback, $params);
        }
    }

    /**
     * Standard callback method used to highlight words.
     *
     * @param  string  $stringToHighlight
     * @return string
     * @internal
     */
    public function applyColour($stringToHighlight, $colour)
    {
        return '<b style="color:black;background-color:' . $colour . '">' . $stringToHighlight . '</b>';
    }

    /**
     * Highlight text with specified color
     *
     * @param string|array $words
     * @param string $colour
     * @return string
     */
    public function highlight($words, $colour = '#66ffff')
    {
    	return $this->highlightExtended($words, array($this, 'applyColour'), array($colour));
    }



    /**
     * Highlight text using specified View helper or callback function.
     *
     * @param string|array $words  Words to highlight. Words could be organized using the array or string.
     * @param callback $callback   Callback method, used to transform (highlighting) text.
     * @param array    $params     Array of additionall callback parameters passed through into it
     *                             (first non-optional parameter is an HTML fragment for highlighting)
     * @return string
     * @throws Zend_Search_Lucene_Exception
     */
    public function highlightExtended($words, $callback, $params = array())
    {
        if (!is_array($words)) {
            $words = array($words);
        }

        $wordsToHighlightList = array();
        $analyzer = Zend_Search_Lucene_Analysis_Analyzer::getDefault();
        foreach ($words as $wordString) {
            $wordsToHighlightList[] = $analyzer->tokenize($wordString);
        }
        $wordsToHighlight = call_user_func_array('array_merge', $wordsToHighlightList);

        if (count($wordsToHighlight) == 0) {
            return $this->_doc->saveHTML();
        }

        $wordsToHighlightFlipped = array();
        foreach ($wordsToHighlight as $id => $token) {
            $wordsToHighlightFlipped[$token->getTermText()] = $id;
        }

        if (!is_callable($callback)) {
        	require_once 'Zend/Search/Lucene/Exception.php';
        	throw new Zend_Search_Lucene_Exception('$viewHelper parameter mast be a View Helper name, View Helper object or callback.');
        }

        $xpath = new DOMXPath($this->_doc);

        $matchedNodes = $xpath->query("/html/body");
        foreach ($matchedNodes as $matchedNode) {
            $this->_highlightNodeRecursive($matchedNode, $wordsToHighlightFlipped, $callback, $params);
        }
    }


    /**
     * Get HTML
     *
     * @return string
     */
    public function getHTML()
    {
        return $this->_doc->saveHTML();
    }

    /**
     * Get HTML body
     *
     * @return string
     */
    public function getHtmlBody()
    {
        $xpath = new DOMXPath($this->_doc);
        $bodyNodes = $xpath->query('/html/body')->item(0)->childNodes;

        $outputFragments = array();
        for ($count = 0; $count < $bodyNodes->length; $count++) {
        	$outputFragments[] = $this->_doc->saveXML($bodyNodes->item($count));
        }

        return implode($outputFragments);
    }
}

