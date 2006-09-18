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
 * @subpackage Index
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/** Zend_Search_Lucene_Exception */
require_once 'Zend/Search/Lucene/Exception.php';

/** Zend_Search_Lucene_Analysis_Analyzer */
require_once 'Zend/Search/Lucene/Analysis/Analyzer.php';


/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage Index
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Search_Lucene_Index_SegmentWriter_DocumentWriter extends Zend_Search_Lucene_Index_SegmentWriter
{
    /**
     * Number of docs in a segment
     *
     * @var integer
     */
    protected $_docCount;

    /**
     * Term Dictionary
     * Array of the Zend_Search_Lucene_Index_Term objects
     * Corresponding Zend_Search_Lucene_Index_TermInfo object stored in the $_termDictionaryInfos
     *
     * @var array
     */
    protected $_termDictionary;

    /**
     * Documents, which contain the term
     *
     * @var array
     */
    protected $_termDocs;

    /**
     * Object constructor.
     *
     * @param Zend_Search_Lucene_Storage_Directory $directory
     * @param string $name
     */
    public function __construct(Zend_Search_Lucene_Storage_Directory $directory, $name)
    {
        parent::__construct($directory, $name);

        $this->_docCount  = 0;
        $this->_termDocs       = array();
        $this->_termDictionary = array();
    }


    /**
     * Adds a document to this segment.
     *
     * @param Zend_Search_Lucene_Document $document
     * @throws Zend_Search_Lucene_Exception
     */
    public function addDocument(Zend_Search_Lucene_Document $document)
    {
        $storedFields = array();

        foreach ($document->getFieldNames() as $fieldName) {
            $field = $document->getField($fieldName);
            $this->addField($field);

            if ($field->storeTermVector) {
                /**
                 * @todo term vector storing support
                 */
                throw new Zend_Search_Lucene_Exception('Store term vector functionality is not supported yet.');
            }

            if ($field->isIndexed) {
                if ($field->isTokenized) {
                    $tokenList = Zend_Search_Lucene_Analysis_Analyzer::getDefault()->tokenize($field->stringValue);
                } else {
                    $tokenList = array();
                    $tokenList[] = new Zend_Search_Lucene_Analysis_Token($field->stringValue, 0, strlen($field->stringValue));
                }
                $this->_fieldLengths[$field->name][$this->_docCount] = count($tokenList);

                $position = 0;
                foreach ($tokenList as $token) {
                    $term = new Zend_Search_Lucene_Index_Term($token->getTermText(), $field->name);
                    $termKey = $term->key();

                    if (!isset($this->_termDictionary[$termKey])) {
                        // New term
                        $this->_termDictionary[$termKey] = $term;
                        $this->_termDocs[$termKey] = array();
                        $this->_termDocs[$termKey][$this->_docCount] = array();
                    } else if (!isset($this->_termDocs[$termKey][$this->_docCount])) {
                        // Existing term, but new term entry
                        $this->_termDocs[$termKey][$this->_docCount] = array();
                    }
                    $position += $token->getPositionIncrement();
                    $this->_termDocs[$termKey][$this->_docCount][] = $position;
                }
            }

            if ($field->isStored) {
                $storedFields[] = $field;
            }
        }

        $this->addStoredFields($storedFields);

        $this->_docCount++;
    }


    /**
     * Dump Term Dictionary (.tis) and Term Dictionary Index (.tii) segment files
     */
    protected function _dumpDictionary()
    {
        $termKeys = array_keys($this->_termDictionary);
        sort($termKeys, SORT_STRING);

        $tisFile = $this->_directory->createFile($this->_name . '.tis');
        $tisFile->writeInt((int)0xFFFFFFFE);
        $tisFile->writeLong(count($termKeys));
        $tisFile->writeInt(self::$indexInterval);
        $tisFile->writeInt(self::$skipInterval);

        $tiiFile = $this->_directory->createFile($this->_name . '.tii');
        $tiiFile->writeInt((int)0xFFFFFFFE);
        $tiiFile->writeLong(ceil((count($termKeys) + 2)/self::$indexInterval));
        $tiiFile->writeInt(self::$indexInterval);
        $tiiFile->writeInt(self::$skipInterval);

        /** Dump dictionary header */
        $tiiFile->writeVInt(0);                    // preffix length
        $tiiFile->writeString('');                 // suffix
        $tiiFile->writeInt((int)0xFFFFFFFF);       // field number
        $tiiFile->writeByte((int)0x0F);
        $tiiFile->writeVInt(0);                    // DocFreq
        $tiiFile->writeVInt(0);                    // FreqDelta
        $tiiFile->writeVInt(0);                    // ProxDelta
        $tiiFile->writeVInt(20);                   // IndexDelta

        $frqFile = $this->_directory->createFile($this->_name . '.frq');
        $prxFile = $this->_directory->createFile($this->_name . '.prx');

        $termCount = 1;

        $prevTerm     = null;
        $prevTermInfo = null;
        $prevIndexTerm     = null;
        $prevIndexTermInfo = null;
        $prevIndexPosition = 20;

        foreach ($termKeys as $termId) {
            $freqPointer = $frqFile->tell();
            $proxPointer = $prxFile->tell();

            $prevDoc = 0;
            foreach ($this->_termDocs[$termId] as $docId => $termPositions) {
                $docDelta = ($docId - $prevDoc)*2;
                $prevDoc = $docId;
                if (count($termPositions) > 1) {
                    $frqFile->writeVInt($docDelta);
                    $frqFile->writeVInt(count($termPositions));
                } else {
                    $frqFile->writeVInt($docDelta + 1);
                }

                $prevPosition = 0;
                foreach ($termPositions as $position) {
                    $prxFile->writeVInt($position - $prevPosition);
                    $prevPosition = $position;
                }
            }

            if (count($this->_termDocs[$termId]) >= self::$skipInterval) {
                /**
                 * @todo Write Skip Data to a freq file.
                 * It's not used now, but make index more optimal
                 */
                $skipOffset = $frqFile->tell() - $freqPointer;
            } else {
                $skipOffset = 0;
            }

            $term = new Zend_Search_Lucene_Index_Term($this->_termDictionary[$termId]->text,
                                                      $this->_fields[$this->_termDictionary[$termId]->field]->number);
            $termInfo = new Zend_Search_Lucene_Index_TermInfo(count($this->_termDocs[$termId]),
                                            $freqPointer, $proxPointer, $skipOffset);

            $this->_dumpTermDictEntry($tisFile, $prevTerm, $term, $prevTermInfo, $termInfo);

            if ($termCount % self::$indexInterval == 0) {
                $this->_dumpTermDictEntry($tiiFile, $prevIndexTerm, $term, $prevIndexTermInfo, $termInfo);

                $indexPosition = $tisFile->tell();
                $tiiFile->writeVInt($indexPosition - $prevIndexPosition);
                $prevIndexPosition = $indexPosition;
            }
            $termCount++;
        }

        $this->_files[] = $this->_name . '.tis';
        $this->_files[] = $this->_name . '.tii';
        $this->_files[] = $this->_name . '.frq';
        $this->_files[] = $this->_name . '.prx';
    }


    /**
     * Close segment, write it to disk and return segment info
     *
     * @return Zend_Search_Lucene_Index_SegmentInfo
     */
    public function close()
    {
        if ($this->_docCount == 0) {
            return null;
        }

        $this->_dumpFNM();
        $this->_dumpDictionary();

        $this->_generateCFS();

        return new Zend_Search_Lucene_Index_SegmentInfo($this->_name,
                                                        $this->_docCount,
                                                        $this->_directory);
    }

}

