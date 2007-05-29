<?php

class Confluence_Chapter
{
    /**
     * The chapter prefix, this can for instance be a chapter id
     *
     * @var string
     */
    protected $_prefix;

    /**
     * The SimpleXML documents
     *
     * @var SimpleXML
     */
    protected $_chapter;

    /**
     * Class constructor
     *
     * @param string $chapter - SimpleXML data for chapter
     * @param string $prefix  - OPTIONAL prefix for chapter title
     */
    public function __construct($chapter, $prefix = null)
    {
        $this->_prefix  = $prefix;
        $this->_chapter = $chapter;
    }

    /**
     * Return the title for chapter with or without prefix
     *
     * @param  boolean $prefix - Prefix the chapter with prefix or not
     * @return string
     */
    public function getTitle($prefix = true)
    {
        $title = str_replace(':', '', $this->_chapter->title);
        if ($prefix) {
            return $this->_prefix . $title;
        }
        return $title;
    }

    /**
     * Return the XML for this chapter
     *
     * @return string
     */
    public function getXml()
    {
        return $this->_chapter->asXml();
    }

    /**
     * Return the wiki output for this chapter by appling the stylesheet
     *
     * @param string $stylesheet - The stylesheet for this chapter
     */
    public function getWiki($stylesheet)
    {
        if (!$this->_chapter) {
            return;
        }

        $chapter = $this->getXml();

        $xml  = new DOMDocument;
        $xsl  = new DOMDocument;
        $xslt = new XSLTProcessor;

        $xml->loadXML('<chapter>' . $chapter . '</chapter>');
        $xsl->load($stylesheet);
        $xslt->importStyleSheet($xsl);

        $wiki = $xslt->transformToXML($xml);

        return $wiki;
    }

}
