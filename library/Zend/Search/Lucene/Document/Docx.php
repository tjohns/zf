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

if (class_exists('ZipArchive')) {

/**
 * Docx document.
 *
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage Document
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Search_Lucene_Document_Docx extends Zend_Search_Lucene_Document
{
    /**
     * Xml Schema - Relationships
     *
     * @var string
     */
    const SCHEMA_RELATIONSHIP = 'http://schemas.openxmlformats.org/package/2006/relationships';

    /**
     * Xml Schema - Office document
     *
     * @var string
     */
    const SCHEMA_OFFICEDOCUMENT = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument';

    /**
     * Xml Schema - WordprocessingML
     *
     * @var string
     */
    const SCHEMA_WORDPROCESSINGML = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';

    /**
     * Xml Schema - Core properties
     *
     * @var string
     */
    const SCHEMA_COREPROPERTIES = 'http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties';

    /**
     * Xml Schema - Dublin Core
     *
     * @var string
     */
    const SCHEMA_DUBLINCORE = 'http://purl.org/dc/elements/1.1/';

    /**
     * Xml Schema - Dublin Core Terms
     *
     * @var string
     */
    const SCHEMA_DUBLINCORETERMS = 'http://purl.org/dc/terms/';

    /**
     * Object constructor
     *
     * @param string  $fileName
     * @param boolean $storeContent
     */
    private function __construct($fileName, $storeContent)
    {
        // Document data holders
        $documentBody = '';
        $coreProperties = array();

        // Open OpenXML package
        $package = new ZipArchive();
        $package->open($fileName);

        // Read relations and search for officeDocument or core properties
        $relations = simplexml_load_string($package->getFromName("_rels/.rels"));
        foreach ($relations->Relationship as $rel) {
            if ($rel["Type"] == Zend_Search_Lucene_Document_Docx::SCHEMA_OFFICEDOCUMENT) {
                // Found office document! Read in contents...
                $contents = simplexml_load_string(
                    $package->getFromName(dirname($rel["Target"]) . "/" . basename($rel["Target"]))
                );

                $contents->registerXPathNamespace("w", Zend_Search_Lucene_Document_Docx::SCHEMA_WORDPROCESSINGML);
                $paragraphs = $contents->xpath('//w:body/w:p');

                foreach($paragraphs as $paragraph) {
                    $runs = $paragraph->xpath('//w:r/w:t');
                    foreach ($runs as $run) {
                        $documentBody .= (string)$run;
                    }
                }
            }

            if ($rel["Type"] == Zend_Search_Lucene_Document_Docx::SCHEMA_COREPROPERTIES) {
                // Found core properties! Read in contents...
                $contents = simplexml_load_string(
                    $package->getFromName(dirname($rel["Target"]) . "/" . basename($rel["Target"]))
                );

                foreach ($contents->children(Zend_Search_Lucene_Document_Docx::SCHEMA_DUBLINCORE) as $child) {
                    $coreProperties[$child->getName()] = (string)$child;
                }
                foreach ($contents->children(Zend_Search_Lucene_Document_Docx::SCHEMA_COREPROPERTIES) as $child) {
                    $coreProperties[$child->getName()] = (string)$child;
                }
                foreach ($contents->children(Zend_Search_Lucene_Document_Docx::SCHEMA_DUBLINCORETERMS) as $child) {
                    $coreProperties[$child->getName()] = (string)$child;
                }
            }
        }

        // Close file
        $package->close();

        // Store filename
        $this->addField(Zend_Search_Lucene_Field::Text('filename', $fileName));

        // Store contents
        if ($storeContent) {
            $this->addField(Zend_Search_Lucene_Field::Text('body', $documentBody));
        } else {
            $this->addField(Zend_Search_Lucene_Field::UnStored('body', $documentBody));
        }

        // Store meta data properties
        foreach ($coreProperties as $key => $value)
        {
            $this->addField(Zend_Search_Lucene_Field::Text($key, $value));
        }

        // Store title (if not present in meta data)
        if (!isset($coreProperties['title']))
        {
            $this->addField(Zend_Search_Lucene_Field::Text('title', $fileName));
        }
    }

    /**
     * Load Docx document from a file
     *
     * @param string  $fileName
     * @param boolean $storeContent
     * @return Zend_Search_Lucene_Document_Docx
     */
    public static function loadDocxFile($fileName, $storeContent = false)
    {
        return new Zend_Search_Lucene_Document_Docx($fileName, $storeContent);
    }
}

} // end if (class_exists('ZipArchive'))
