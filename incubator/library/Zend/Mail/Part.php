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
 * @package    Zend_Mail
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */


/**
 * Zend_Mime_Decode
 */
require_once 'Zend/Mime/Decode.php';

/**
 * Zend_Mail_Exception
 */
require_once 'Zend/Mail/Exception.php';

/**
 * @package    Zend_Mail
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
class Zend_Mail_Part implements RecursiveIterator
{
    /**
     * headers of part as array
     */
    protected $_headers;

    /**
     * raw part body
     */
    protected $_content;

    protected $_topLines;

    protected $_parts = array();

    protected $_countParts;

    protected $_iterationPos = 1;

    /**
     * mail handler
     */
    protected $_mail;

    /**
     * message number for mail handler
     */
    protected $_messageNum;

    /**
     * Public constructor
     *
     * @param string $rawMessage  full message with or without headers
     */
    public function __construct(array $params)
    {
        if (isset($params['handler'])) {
            if (!$params['handler'] instanceof Zend_Mail_Storage_Abstract) {
                throw new Zend_Mail_Exception('handler is not a valid mail handler');
            }
            if (!isset($params['id'])) {
                throw new Zend_Mail_Exception('need a message id with a handler');
            }

            $this->_mail       = $params['handler'];
            $this->_messageNum = $params['id'];
        }

        if (isset($params['raw'])) {
            Zend_Mime_Decode::splitMessage($params['raw'], $this->_headers, $this->_content);
        } else if (isset($params['headers'])) {
            if (is_array($params['headers'])) {
                $this->_headers = $params['headers'];
            } else {
                if (!empty($params['noToplines'])) {
                    Zend_Mime_Decode::splitMessage($params['headers'], $this->_headers, $null);
                } else {
                    Zend_Mime_Decode::splitMessage($params['headers'], $this->_headers, $this->_topLines);
                }
            }
            if (isset($params['content'])) {
                $this->_content = $params['content'];
            }
        }
    }

    public function isMultipart()
    {
        try {
            return strpos($this->contentType, 'multipart/') === 0;
        } catch(Zend_Mail_Exception $e) {
            return false;
        }
    }


    /**
     * Body of message
     *
     * @return string body
     */
    public function getContent()
    {
        if ($this->_content !== null) {
            return $this->_content;
        }

        if ($this->_mail) {
            return $this->_mail->getRawContent($this->_messageNum);
        } else {
            throw new Zend_Mail_Exception('no content');
        }
    }

    public function _cacheContent()
    {
        // caching content if we can't fetch parts
        if ($this->_content === null) {
            $this->_content = $this->_mail->getRawContent($this->_messageNum);
        }

        if (!$this->isMultipart()) {
            return;
        }

        // split content in parts
        $boundary = Zend_Mime_Decode::splitContentType($this->contentType, 'boundary');
        if (!$boundary) {
            throw new Zend_Mail_Exception('no boundary found in content type to split message');
        }
        $parts = Zend_Mime_Decode::splitMessageStruct($this->_content, $boundary);
        $counter = 1;
        foreach ($parts as $part) {
            $this->_parts[$counter++] = new self(array('headers' => $part['header'], 'content' => $part['body']));
        }
    }

    public function getPart($num)
    {
        if (isset($this->_parts[$num])) {
            return $this->_parts[$num];
        }

        if (!$this->_mail && $this->_content === null) {
            throw new Zend_Mail_Exception('part not found');
        }

        if ($this->_mail && $this->_mail->hasFetchPart) {
            // TODO: fetch part
            // return
        }

        $this->_cacheContent();

        if (!isset($this->_parts[$num])) {
            throw new Zend_Mail_Exception('part not found');
        }

        return $this->_parts[$num];
    }

    public function countParts()
    {
        if ($this->_countParts) {
            return $this->_countParts;
        }

        $this->_countParts = count($this->_parts);
        if ($this->_countParts) {
            return $this->_countParts;
        }

        if ($this->_mail && $this->_mail->hasFetchPart) {
            // TODO: fetch part
            // return
        }

        $this->_cacheContent();

        $this->_countParts = count($this->_parts);
        return $this->_countParts;
    }


    /**
     * Get all headers
     *
     * @return array headers
     */
    public function getHeaders()
    {
        if ($this->_headers === null) {
            if (!$this->_mail) {
                $this->_headers = array();
            } else {
                $part = $this->_mail->getRawHeader($this->_messageNum);
                Zend_Mime_Decode::splitMessage($part, $this->_headers, $null);
            }
        }

        return $this->_headers;
    }


    public function getHeader($name, $format = null)
    {
        if ($this->_headers === null) {
            $this->getHeaders();
        }

        $lowerName = strtolower($name);

        if (!isset($this->_headers[$lowerName])) {
            $lowerName = strtolower(preg_replace('%([a-z])([A-Z])%', '\1-\2', $name));
            if (!isset($this->_headers[$lowerName])) {
                throw new Zend_Mail_Exception("no Header with Name $name found");
            }
        }
        $name = $lowerName;

        $header = $this->_headers[$name];

        switch ($format) {
            case 'string':
                if (is_array($header)) {
                    $header = implode(Zend_Mime::LINEEND, $header);
                }
                break;
            case 'array':
                $header = (array)$header;
            default:
                // do nothing
        }

        return $header;
    }


    /**
     * Getter for mail headers - name is matched in lowercase
     *
     * @param  string $name         header name
     * @throws Zend_Mail_Exception
     * @return string|array         header line or array of headers if header exists more than once
     */
    public function __get($name)
    {
        return $this->getHeader($name, 'string');
    }

    public function __toString()
    {
        return $this->getContent();
    }

    /**
     * implements RecursiveIterator::hasChildren()
     *
     * @return bool current element has children
     */
    public function hasChildren()
    {
        $current = $this->current();
        return $current && $current instanceof Zend_Mail_Part && $current->isMultipart();
    }

    /**
     * implements RecursiveIterator::getChildren()
     *
     * @return Zend_Mail_Storage_Folder same as self::current()
     */
    public function getChildren()
    {
        return $this->current();
    }

    /**
     * implements Iterator::valid()
     *
     * @return bool check if there's a current element
     */
    public function valid()
    {
        if ($this->_countParts === null) {
            $this->countParts();
        }
        return $this->_iterationPos && $this->_iterationPos <= $this->_countParts;
    }

    /**
     * implements Iterator::next()
     * @return null
     */
    public function next()
    {
        ++$this->_iterationPos;
    }

    /**
     * implements Iterator::key()
     *
     * @return string key/local name of current element
     */
    public function key()
    {
        return $this->_iterationPos;
    }

    /**
     * implements Iterator::current()
     *
     * @return Zend_Mail_Storage_Folder current folder
     */
    public function current()
    {
        return $this->getPart($this->_iterationPos);
    }

    /**
     * implements Iterator::rewind()
     * @return null
     */
    public function rewind()
    {
        $this->countParts();
        $this->_iterationPos = 1;
    }
}
