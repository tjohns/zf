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
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
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
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
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
     * Object constructor
     *
     * @param string  $data
     * @param boolean $isFile
     * @param boolean $storeContent
     */
    private function __construct($data, $isFile, $storeContent)
    {
        $doc = new DOMDocument();
        $doc->substituteEntities = true;

        if ($isFile) {
            @$doc->loadHTMLFile($data);
        } else{
            @$doc->loadHTML($data);
        }

        $xpath = new DOMXPath($doc);

        $docTitle = '';
        $titleNodes = $xpath->query('/html/head/title');
        foreach ($titleNodes as $titleNode) {
            // title should always have only one entry, but we process all nodeset entries
            $docTitle .= $titleNode->nodeValue . ' ';
        }
        $this->addField(Zend_Search_Lucene_Field::Text('title', $docTitle, $doc->actualEncoding));

        $metaNodes = $xpath->query('/html/head/meta[@name]');
        foreach ($metaNodes as $metaNode) {
            $this->addField(Zend_Search_Lucene_Field::Text($metaNode->getAttribute('name'),
                                                           $metaNode->getAttribute('content'),
                                                           $doc->actualEncoding));
        }

        $docBody = '';
        $bodyNodes = $xpath->query('/html/body');
        foreach ($bodyNodes as $bodyNode) {
            // body should always have only one entry, but we process all nodeset entries
            $docBody .= $bodyNode->textContent;
        }
        if ($storeContent) {
            $this->addField(Zend_Search_Lucene_Field::Text('body', $docBody, $doc->actualEncoding));
        } else {
            $this->addField(Zend_Search_Lucene_Field::UnStored('body', $docBody, $doc->actualEncoding));
        }

        $linkNodes = $doc->getElementsByTagName('a');
        foreach ($linkNodes as $linkNode) {
            if (($href = $linkNode->getAttribute('href')) != '') {
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
     * @param string $data
     * @param boolean $storeContent
     * @return Zend_Search_Lucene_Document_Html
     */
    public static function loadHTML($data, $storeContent = false)
    {
        return new Zend_Search_Lucene_Document_Html($data, false, $storeContent);
    }

    /**
     * Load HTML document from a file
     *
     * @param string $file
     * @param boolean $storeContent
     * @return Zend_Search_Lucene_Document_Html
     */
    public static function loadHTMLFile($file, $storeContent = false)
    {
        return new Zend_Search_Lucene_Document_Html($file, true, $storeContent);
    }
}
