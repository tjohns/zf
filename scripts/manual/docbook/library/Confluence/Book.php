<?php

class Confluence_Book
{
    const XINCLUDE_PATTERN_REGEX = '/(<xi:include href=")(.+)"( \/>)/';

    /**
     * Holds the SimpleXml
     *
     * @var SimpleXml
     */
    protected $_xml;

    /**
     * The path to the manual
     *
     * @var string
     */
    protected $_path;

    public function __construct($manual)
    {
        $this->_path = dirname($manual);

        $xml = file_get_contents($manual);

        // Apperently commens in manual.xml throw an exception, so get rid of them
        $xml = preg_replace('/(<!--.*-->)/isU', '', $xml);
        $xml = preg_replace('/(<!DOCTYPE.*]>)/isU', '', $xml);

        // Workaround for xinclude unexpected behaviour
        $xml = preg_replace_callback(self::XINCLUDE_PATTERN_REGEX, array($this, '_processChapters'), $xml);
        $this->_xml = new SimpleXMLElement($xml);
    }

    public function get()
    {
        return $this->_xml;
    }

    protected function _processChapters($matches)
    {
        $filename = $this->_path . '/' . trim($matches[2]);
        if (!is_file($filename)) {
            return;
        }

        $xml = file_get_contents($filename);
        // Workarounds
        $xml = preg_replace_callback(self::XINCLUDE_PATTERN_REGEX, array($this, '_processChapters'), $xml);

        return $xml;
    }
}
