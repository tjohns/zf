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
 * @package    Zend_Mime
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Zend_Mime
 */
require_once 'Zend/Mime.php';


/**
 * Class representing a MIME part.
 *
 * @category   Zend
 * @package    Zend_Mime
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Mime_Part {
    
    public $type = Zend_Mime::TYPE_OCTETSTREAM;
    public $encoding = Zend_Mime::ENCODING_8BIT;
    public $id;
    public $disposition;
    public $fileName;
    public $description;
    public $charset;
    protected $_isStream = false;


    /**
     * create a new Mime Part.
     * The (unencoded) content of the Part as passed
     * as a string or stream
     *
     * @param mixed $content  String or Stream containing the content
     */
    public function __construct($content)
    {
        $this->_content = $content;
        if(is_resource($content)) {
          $this->_isStream = true;
        }
    }

    /**
     * @todo setters/getters
     * @todo error checking for setting $type
     * @todo error checking for setting $encoding
     */

    /**
     * check if this part can be read as a stream.
     * if true, getEncodedStream can be called, otherwise
     * only getContent can be used to fetch the encoded
     * content of the part
     *
     * @return bool
     */
    public function isStream() {
      return $this->_isStream;
    }
    
    /**
     * if this was created with a stream, return a filtered stream for
     * reading the content. very useful for large file attachments.
     *
     * @return Stream
     */
    public function getEncodedStream() {
      if(!$this->_isStream) throw new Zend_Mail_Exception('attempt to get a stream from a string part');
      //stream_filter_remove(); // ??? is that right?
      switch($this->encoding) {
        case Zend_Mime::ENCODING_QUOTEDPRINTABLE:
          Zend::loadClass('Zend_Mime_QpFilter');
          if(!stream_filter_register("qp.*", "Zend_Mime_QpFilter")) 
              throw new Zend_Mail_Exception("Failed to register filter");
          stream_filter_append($this->_content, "qp.encode",STREAM_FILTER_READ);
          break;
        case Zend_Mime::ENCODING_BASE64 :
          Zend::loadClass('Zend_Mime_B64Filter');
          if(!stream_filter_register("b64.*", "Zend_Mime_B64Filter")) 
              throw new Zend_Mail_Exception("Failed to register filter");
          stream_filter_append($this->_content, "b64.encode",STREAM_FILTER_READ);
          break;
        default:
      }
      return $this->_content;   
    }
    
    /**
     * Get the Content of the current Mail Part in the given encoding.
     *
     * @return String
     */
    public function getContent()
    {
        if($this->_isStream) {
          return stream_get_contents($this->getEncodedStream());
        }
        else {
          return Zend_Mime::encode($this->_content, $this->encoding);
        }
    }

    /**
     * Create and return the array of headers for this MIME part
     * 
     * @access public
     * @return array
     */
    public function getHeadersArray()
    {
        $headers = array();

        $contentType = $this->type;
        if ($this->charset) {
            $contentType .= '; charset="' . $this->charset . '"';
        }
        $headers[] = array('Content-Type', $contentType);
        $headers[] = array('Content-Transfer-Encoding', $this->encoding);

        if ($this->id) {
            $headers[]  = array('Content-ID', '<' . $this->id . '>');
        }

        if ($this->disposition) {
            $disposition = $this->disposition;
            if ($this->fileName) {
                $disposition .= '; filename="' . $this->fileName . '"';
            }
            $headers[] = array('Content-Disposition', $disposition);
        }

        if ($this->description) {
            $headers[] = array('Content-Description', $this->description);
        }

        return $headers;
    }

    /**
     * Return the headers for this part as a string
     *
     * @return String
     */
    public function getHeaders()
    {
        $res = '';
        foreach ($this->getHeadersArray() as $header) {
            $res .= $header[0] . ': ' . $header[1] . Zend_Mime::LINEEND;
        }

        return $res;
    }
}
