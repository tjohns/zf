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

/** Zend_Search_Lucene_Index_SegmentInfo */
require_once 'Zend/Search/Lucene/Index/SegmentInfo.php';

/** Zend_Search_Lucene_Index_SegmentWriter_DocumentWriter */
require_once 'Zend/Search/Lucene/Index/SegmentWriter/DocumentWriter.php';

/** Zend_Search_Lucene_Index_SegmentWriter_StreamWriter */
require_once 'Zend/Search/Lucene/Index/SegmentWriter/StreamWriter.php';


/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage Index
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Search_Lucene_Index_SegmentWriter
{
    /**
     * Expert: The fraction of terms in the "dictionary" which should be stored
     * in RAM.  Smaller values use more memory, but make searching slightly
     * faster, while larger values use less memory and make searching slightly
     * slower.  Searching is typically not dominated by dictionary lookup, so
     * tweaking this is rarely useful.
     *
     * @var integer
     */
    static public $indexInterval = 128;

    /** Expert: The fraction of TermDocs entries stored in skip tables.
     * Larger values result in smaller indexes, greater acceleration, but fewer
     * accelerable cases, while smaller values result in bigger indexes,
     * less acceleration and more
     * accelerable cases. More detailed experiments would be useful here.
     *
     * 0x0x7FFFFFFF indicates that we don't use skip data
     * Default value is 16
     *
     * @var integer
     */
    static public $skipInterval = 0x7FFFFFFF;

    /**
     * Number of docs in a segment
     *
     * @var integer
     */
    protected $_docCount;

    /**
     * Segment name
     *
     * @var string
     */
    protected $_name;

    /**
     * File system adapter.
     *
     * @var Zend_Search_Lucene_Storage_Directory
     */
    protected $_directory;

    /**
     * List of the index files.
     * Used for automatic compound file generation
     *
     * @var unknown_type
     */
    protected $_files;

    /**
     * Segment fields. Array of Zend_Search_Lucene_Index_FieldInfo objects for this segment
     *
     * @var array
     */
    protected $_fields;


    /**
     * Object constructor.
     *
     * @param Zend_Search_Lucene_Storage_Directory $directory
     * @param string $name
     */
    public function __construct(Zend_Search_Lucene_Storage_Directory $directory, $name)
    {
        $this->_directory = $directory;
        $this->_name      = $name;

        $this->_fields         = array();
        $this->_files          = array();
        $this->_norms          = array();
    }


    /**
     * Add field to the segment
     *
     * Returns actual field number
     *
     * @param Zend_Search_Lucene_Field $field
     * @return integer
     */
    public function addField(Zend_Search_Lucene_Field $field)
    {
        if (!isset($this->_fields[$field->name])) {
            $fieldNumber = count($this->_fields);
            $this->_fields[$field->name] =
                                new Zend_Search_Lucene_Index_FieldInfo($field->name,
                                                                       $field->isIndexed,
                                                                       $fieldNumber,
                                                                       $field->storeTermVector);

            return $fieldNumber;
        } else {
            $this->_fields[$field->name]->isIndexed       |= $field->isIndexed;
            $this->_fields[$field->name]->storeTermVector |= $field->storeTermVector;

            return $this->_fields[$field->name]->number;
        }
    }

    /**
     * Add fieldInfo to the segment
     *
     * Returns actual field number
     *
     * @param Zend_Search_Lucene_Index_FieldInfo $fieldInfo
     * @return integer
     */
    public function addFieldInfo(Zend_Search_Lucene_Index_FieldInfo $fieldInfo)
    {
        if (!isset($this->_fields[$field->name])) {
            $fieldNumber = count($this->_fields);
            $this->_fields[$field->name] =
                                new Zend_Search_Lucene_Index_FieldInfo($fieldInfo->name,
                                                                       $fieldInfo->isIndexed,
                                                                       $fieldNumber,
                                                                       $fieldInfo->storeTermVector);

            return $fieldNumber;
        } else {
            $this->_fields[$field->name]->isIndexed       |= $fieldInfo->isIndexed;
            $this->_fields[$field->name]->storeTermVector |= $fieldInfo->storeTermVector;

            return $this->_fields[$field->name]->number;
        }
    }


    /**
     * Dump Field Info (.fnm) segment file
     */
    protected function _dumpFNM()
    {
        $fnmFile = $this->_directory->createFile($this->_name . '.fnm');
        $fnmFile->writeVInt(count($this->_fields));

        foreach ($this->_fields as $field) {
            $fnmFile->writeString($field->name);
            $fnmFile->writeByte(($field->isIndexed       ? 0x01 : 0x00) |
                                ($field->storeTermVector ? 0x02 : 0x00)
// not supported yet            0x04 /* term positions are stored with the term vectors */ |
// not supported yet            0x08 /* term offsets are stored with the term vectors */   |
                               );

            if ($field->isIndexed) {
                $fieldNum   = $this->_fields[$field->name]->number;
                $fieldName  = $field->name;
                $similarity = Zend_Search_Lucene_Search_Similarity::getDefault();
                $norm       = '';

                for ($count = 0; $count < $this->_docCount; $count++) {
                    $numTokens = isset($this->_fieldLengths[$fieldName][$count]) ?
                                      $this->_fieldLengths[$fieldName][$count] : 0;
                    $norm .= chr($similarity->encodeNorm($similarity->lengthNorm($fieldName, $numTokens)));
                }

                $normFileName = $this->_name . '.f' . $fieldNum;
                $fFile = $this->_directory->createFile($normFileName);
                $fFile->writeBytes($norm);
                $this->_files[] = $normFileName;
            }
        }

        $this->_files[] = $this->_name . '.fnm';
    }


    /**
     * Dump Term Dictionary segment file entry.
     * Used to write entry to .tis or .tii files
     *
     * @param Zend_Search_Lucene_Storage_File $dicFile
     * @param Zend_Search_Lucene_Index_Term $prevTerm
     * @param Zend_Search_Lucene_Index_Term $term
     * @param Zend_Search_Lucene_Index_TermInfo $prevTermInfo
     * @param Zend_Search_Lucene_Index_TermInfo $termInfo
     */
    protected function _dumpTermDictEntry(Zend_Search_Lucene_Storage_File $dicFile,
                                        &$prevTerm,     Zend_Search_Lucene_Index_Term     $term,
                                        &$prevTermInfo, Zend_Search_Lucene_Index_TermInfo $termInfo)
    {
        if (isset($prevTerm) && $prevTerm->field == $term->field) {
            $prefixLength = 0;
            while ($prefixLength < strlen($prevTerm->text) &&
                   $prefixLength < strlen($term->text) &&
                   $prevTerm->text{$prefixLength} == $term->text{$prefixLength}
                  ) {
                $prefixLength++;
            }
            // Write preffix length
            $dicFile->writeVInt($prefixLength);
            // Write suffix
            $dicFile->writeString( substr($term->text, $prefixLength) );
        } else {
            // Write preffix length
            $dicFile->writeVInt(0);
            // Write suffix
            $dicFile->writeString($term->text);
        }
        // Write field number
        $dicFile->writeVInt($term->field);
        // DocFreq (the count of documents which contain the term)
        $dicFile->writeVInt($termInfo->docFreq);

        $prevTerm = $term;

        if (!isset($prevTermInfo)) {
            // Write FreqDelta
            $dicFile->writeVInt($termInfo->freqPointer);
            // Write ProxDelta
            $dicFile->writeVInt($termInfo->proxPointer);
        } else {
            // Write FreqDelta
            $dicFile->writeVInt($termInfo->freqPointer - $prevTermInfo->freqPointer);
            // Write ProxDelta
            $dicFile->writeVInt($termInfo->proxPointer - $prevTermInfo->proxPointer);
        }
        // Write SkipOffset - it's not 0 when $termInfo->docFreq > self::$skipInterval
        if ($termInfo->skipOffset != 0) {
            $dicFile->writeVInt($termInfo->skipOffset);
        }

        $prevTermInfo = $termInfo;
    }


    /**
     * Generate compound index file
     */
    protected function _generateCFS()
    {
        $cfsFile = $this->_directory->createFile($this->_name . '.cfs');
        $cfsFile->writeVInt(count($this->_files));

        $dataOffsetPointers = array();
        foreach ($this->_files as $fileName) {
            $dataOffsetPointers[$fileName] = $cfsFile->tell();
            $cfsFile->writeLong(0); // write dummy data
            $cfsFile->writeString($fileName);
        }

        foreach ($this->_files as $fileName) {
            // Get actual data offset
            $dataOffset = $cfsFile->tell();
            // Seek to the data offset pointer
            $cfsFile->seek($dataOffsetPointers[$fileName]);
            // Write actual data offset value
            $cfsFile->writeLong($dataOffset);
            // Seek back to the end of file
            $cfsFile->seek($dataOffset);

            $dataFile = $this->_directory->getFileObject($fileName);
            $data = $dataFile->readBytes($this->_directory->fileLength($fileName));
            $cfsFile->writeBytes($data);

            $this->_directory->deleteFile($fileName);
        }
    }


    /**
     * Close segment, write it to disk and return segment info
     *
     * @return Zend_Search_Lucene_Index_SegmentInfo
     */
    abstract public function close();
}

