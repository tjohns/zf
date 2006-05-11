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
    public $filename;
    public $description;
    public $charset;
    protected $_content;


    public function __construct($content)
    {
        $this->_content = $content;
    }

    /**
     * @todo setters/getters
     * @todo error checking for setting $type
     * @todo error checking for setting $encoding
     */


    /**
     * Get the Content of the current Mail Part in the given encoding.
     *
     * @return String
     */
    public function getContent()
    {
        return Zend_Mime::encode($this->_content, $this->encoding);
    }


    /**
     * Return the headers for this part as a string
     *
     * @return String
     */
    public function getHeaders()
    {
        $res = 'Content-Type: '.$this->type;

        if ($this->charset) {
            $res.= '; charset="'.$this->charset.'"';
        }

        $res .= Zend_Mime::LINEEND
              . 'Content-Transfer-Encoding: ' . $this->encoding
              . Zend_Mime::LINEEND;

        if ($this->id) {
            $res.= 'Content-ID: <' .$this->id. '>' . Zend_Mime::LINEEND;
        }

        if ($this->disposition) {
            $res.= 'Content-Disposition: ' . $this->disposition;
            if ($this->filename) {
                $res .= '; filename="' .$this->filename. '"';
            }
            $res .= Zend_Mime::LINEEND;
        }

        if ($this->description) {
            $res .= 'Content-Description: ' . $this->description . Zend_Mime::LINEEND;
        }

        return $res;
    }
}
