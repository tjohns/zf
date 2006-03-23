<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to version 1.0 of the Zend Framework
 * license, that is bundled with this package in the file LICENSE, and
 * is available through the world-wide-web at the following URL:
 * http://www.zend.com/license/framework/1_0.txt. If you did not receive
 * a copy of the Zend Framework license and are unable to obtain it
 * through the world-wide-web, please send a note to license@zend.com
 * so we can mail you a copy immediately.
 *
 * @package    Zend_Mime
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */


/**
 * Zend_Mime
 */
require_once 'Zend/Mime.php';

/**
 * Zend_Mime_Part
 */
require_once 'Zend/Mime/Part.php';


/**
 * @package    Zend_Mime
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
class Zend_Mime_Message {

    protected $_parts = array();

    protected $_mime = null;


    /**
     * Returns the list of all Zend_Mime_Parts in this Mail
     * as an Array.
     *
     * @return array of Zend_Mime_Part
     */
    public function getParts()
    {
        return $this->_parts;
    }


    /**
     * sets the given Array of Zend_Mime_Parts as the Array
     * for this eMail.
     *
     * @param array $parts
     */
    public function setParts($parts)
    {
        $this->_parts = $parts;
    }


    /**
     * Append a new Zend_Mime_Part to the current eMail
     *
     * @param Zend_Mime_Part $part
     */
    public function addPart(Zend_Mime_Part $part)
    {
        /**
         * @todo check for duplicate object handle
         */
        $this->_parts[] = $part;
    }

    /**
     * check if message needs to be sent as multipart
     * mime message of if it has only one part.
     *
     * @return bool
     */
    public function isMultiPart()
    {
        return (count($this->_parts) > 1);
    }

    /**
     * set mime Object for this Message. This can be used
     * to set the Boundary specifically or to use a SubClass
     * of Zend_Mime for generating the boundary.
     *
     * @param Zend_Mime $mime
     */
    public function setMime(Zend_Mime $mime)
    {
        $this->_mime = $mime;
    }

    /**
     * returns the Zend_Mime object in use by this Message.
     * If the object was not present, it is created and
     * returned. Can be used to determine the boundary
     * used in this message.
     *
     * @return Zend_Mime
     */
    public function getMime()
    {
        if ($this->_mime === null) $this->_mime = new Zend_Mime();
        return $this->_mime;
    }

    /**
     * Generate Mime Compliant Message from the current configuration
     * This can be a multipart message if more than one mimeParts were
     * added. If only one Part is present, the content of this part
     * is returned. If no part had been added, an empty string is returned.
     * Parts are seperated by the mime boundary as defined in Zend_Mime. If
     * setMime has been called before this method, the Zend_Mime object set
     * by this call will be used. Otherwise, a new Zend_Mime Object is generated
     * and used.
     *
     * @return String
     */
    public function generateMessage()
    {
        if (! $this->isMultiPart()) {
            if (! array_key_exists(0, $this->_parts)) {
                $body = '';
            } else {
                $body = $this->_parts[0]->getContent();
            }
        } else {
            $mime = $this->getMime();
            $boundaryLine = $mime->boundaryLine();
            $body = 'This is a message in Mime Format.  If you see this, '
                  . "your mail reader does not support this format." . Zend_Mime::LINEEND;

            for ($p=0; $p < count($this->_parts); $p++) {
                $body .= $boundaryLine . $this->getPartHeaders($p)
                      . Zend_Mime::LINEEND . $this->getPartContent($p);
            }

            $body .= $mime->mimeEnd();
        }
        return $body;
    }

    /**
     * get the headers of a given part as a string
     *
     * @param int $partnum
     * @return string
     */
    public function getPartHeaders($partnum)
    {
        return $this->_parts[$partnum]->getHeaders();
    }

    /**
     * get the (encoded) content of a given part as
     * a string
     *
     * @param int $partnum
     * @return string
     */
    public function getPartContent($partnum)
    {
        return $this->_parts[$partnum]->getContent();
    }

    /**
     * explode mime multipart string into seperate parts
     * the parts consist of the header and the body of the
     * mime part.
     *
     * @param string $body
     * @param string $boundary
     * @return array
     */
    protected function _disassembleMime($body, $boundary)
    {
        $start = 0;
        $res = array();
        // find every mime part limiter and cut out the
        // string before it.
        // the part before the first boundary string is discarded:
        $p = strpos($body, '--'.$boundary."\n", $start);
        if ($p === false) {
            // no parts found!
            return array();  
        }
        
        // position after first boundary line
        $start = $p + 3 + strlen($boundary); 

        while (($p = strpos($body, '--' . $boundary . "\n", $start)) !== false) {
            $res[] = substr($body, $start, $p-$start);
            $start = $p + 3 + strlen($boundary);
        }
        
        // no more parts, find end boundary
        $p = strpos($body, '--' . $boundary . '--', $start);
        if ($p===false) {
            throw new Zend_Exception('Not a valid Mime Message: End Missing');
        }
        
        // the remaining part also needs to be parsed:
        $res[] = substr($body, $start, $p-$start);
        return $res;
    }

    /**
     * decodes a mime encoded String and returns a MimeMessage
     * object with all the mime parts set according to the given
     * string
     *
     * @param string $message
     * @param string $boundary
     * @return Zend_Mime_Message
     */
    public static function createFromMessage($message, $boundary)
    {
        $partsStr = self::_disassembleMime($message, $boundary);
        if (count($partsStr)<=0) return null;

        $res = new Zend_Mime_Message();
        foreach($partsStr as $part) {
            // separate header and body
            $header = true;  // expecting header lines first
            $headersfound = array();
            $body = '';
            $lastheader = '';
            $lines = explode("\n", $part);
            
            // read line by line
            foreach ($lines as $line) {
                $line = trim($line);
                if ($header) {
                    if ($line == '') {
                        $header=false;
                    } elseif (strpos($line, ':')) {
                        list($key, $value) = explode(':', $line, 2);
                        $headersfound[trim($key)] = trim($value);
                        $lastheader = trim($key);
                    } else {
                        if ($lastheader!='') {
                            $headersfound[$lastheader] .= ' '.trim($line);
                        } else {
                            // headers do not start with an ordinary header line?
                            // then assume no headers at all
                            $header = false; 
                        }
                    }
                } else {
                    $body .= $line . Zend_Mime::LINEEND;
                }
            }

            // now we build a new MimePart for the current Message Part:
            $newPart = new Zend_Mime_Part($body);
            foreach ($headersfound as $key => $value) {
                /**
                 * @todo check for characterset and filename
                 */
                switch($key) {
                    case 'Content-Type':
                        $newPart->type = $value;
                        break;
                    case 'Content-Transfer-Encoding':
                        $newPart->encoding = $value;
                        break;
                    case 'Content-ID':
                        $newPart->id = trim($value,'<>');
                        break;
                    case 'Content-Disposition':
                        $newPart->disposition = $value;
                        break;
                    case 'Content-Description':
                        $newPart->description = $value;
                        break;
                    default:
                        throw new Zend_Exception('Unknown header ignored for MimePart:'.$key);
                }
            }
            $res->addPart($newPart);
        }
        return $res;
    }
}
