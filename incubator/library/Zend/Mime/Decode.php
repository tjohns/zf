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
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Zend_Mime
 */
require_once 'Zend/Mime.php';

/**
 * @category   Zend
 * @package    Zend_Mime
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Mime_Decode
{
    /**
     * Explode MIME multipart string into seperate parts
     *
     * Parts consist of the header and the body of each MIME part.
     *
     * @param string $body
     * @param string $boundary
     * @return array
     */
    public static function splitMime($body, $boundary)
    {
        // TODO: we're ignoring \r for now - is this function fast enough and is it safe to asume noone needs \r?
        $body = str_replace("\r", '', $body);

        $start = 0;
        $res = array();
        // find every mime part limiter and cut out the
        // string before it.
        // the part before the first boundary string is discarded:
        $p = strpos($body, '--' . $boundary . "\n", $start);
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
     * decodes a mime encoded String and returns a
     * struct of parts with header and body
     *
     * @param string $message
     * @param string $boundary
     * @param string $EOL EOL string; defaults to {@link Zend_Mime::LINEEND}
     * @return array
     */
    public static function splitMessageStruct($message, $boundary, $EOL = Zend_Mime::LINEEND)
    {
        $parts = self::splitMime($message, $boundary);
        if (count($parts) <= 0) {
            return null;
        }
        $result = array();
        foreach($parts as $part) {
            self::splitMessage($part, $headers, $body, $EOL);
            $result[] = array('header' => $headers,
                              'body'   => $body    );
        }
        return $result;
    }

    /**
     * split a message in header and body part, if no header or an
     * invalid header is found $headers is empty
     *
     * @param string $message
     * @param mixed $headers, output param, out type is array
     * @param mixed $body, output param, out type is string
     * @param string $EOL EOL string; defaults to {@link Zend_Mime::LINEEND}
     */
    public static function splitMessage($message, &$headers, &$body, $EOL = Zend_Mime::LINEEND)
    {
		// check for valid header at first line
		$firstline = strtok($message, "\n");
		if(!preg_match('%^[^\s]+[^:]*:%', $firstline)) {
			$headers = array();
			$body = str_replace(array("\r", "\n"), array('', $EOL), $message);
			return;
		}
		
		// find an empty line between headers and body
		// default is set new line
		if(strpos($message, $EOL . $EOL)) {
	    	list($headers, $body) = explode($EOL . $EOL, $message, 2);
		// next is the standard new line
		} else if($EOL != "\r\n" && strpos($message, "\r\n\r\n")) {
	    	list($headers, $body) = explode("\r\n\r\n", $message, 2);
		// next is the other "standard" new line
		} else if($EOL != "\n" && strpos($message, "\n\n")) {
	    	list($headers, $body) = explode("\n\n", $message, 2);
	    // at last resort find anything that looks like a new line
		} else {
			@list($headers, $body) = @preg_split("%([\r\n]+)\\1%U", $message, 2);
		}
		
		$headers = iconv_mime_decode_headers($headers, ICONV_MIME_DECODE_CONTINUE_ON_ERROR);
		
		// normalize header names
		foreach($headers as $name => $header) {
			$lower = strtolower($name);
			if($lower == $name) {
				continue;
			}
			unset($headers[$name]);
			if(!isset($headers[$lower])) {
				$headers[$lower] = $header;
				continue;
			}
			if(is_array($headers[$lower])) {
				$headers[$lower][] = $header;
				continue;
			}
			$headers[$lower] = array($headers[$lower], $header);
		}
    }

    /**
     * split a content type in its different parts - maybe that could
     * get a more generic name and code as many fields use this format
     *
     * @param string content-type
     * @param string the wanted part, else an array with all parts is returned
     *
     * @return string|array wanted part or all parts
     */
    public static function splitContentType($type, $wantedPart = null)
    {
        return self::splitHeaderField($type, $wantedPart, 'type');
    }

    /**
     * split a header field like content type in its different parts
     *
     * @param string header field
     * @param string the wanted part, else an array with all parts is returned
     * @param string key name for the first field
     *
     * @return string|array wanted part or all parts
     */
    public static function splitHeaderField($type, $wantedPart = null, $firstName = 0)
    {
        $split = array();
        $type = explode(';', $type);
        // this is already prepared for a
        $split[$firstName] = array_shift($type);
        foreach($type as $part) {
            $part = trim($part);
            list($key, $value) = explode('=', $part);
            if($value[0] == '"') {
                $value = substr($value, 1, -1);
            }
            $split[$key] = $value;
        }

        if($wantedPart) {
            return isset($split[$wantedPart]) ? $split[$wantedPart] : null;
        }
        return $split;
    }

    /**
     *
     * decode a quoted printable encoded string
     *
     * @param string encoded string
     * @return string decoded string
     */
    public static function decodeQuotedPrintable($string)
    {
		return iconv_mime_decode($string, ICONV_MIME_DECODE_CONTINUE_ON_ERROR);
    }
}
