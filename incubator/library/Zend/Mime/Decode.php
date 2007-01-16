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
    private static $qpFrom = array('=20', '=20', '=21', '=21', '=22', '=22', '=23', '=23', '=24',
                    '=24', '=25', '=25', '=26', '=26', '=27', '=27', '=28', '=28', '=29',
                    '=29', '=2a', '=2A', '=2b', '=2B', '=2c', '=2C', '=2d', '=2D', '=2e',
                    '=2E', '=2f', '=2F', '=30', '=30', '=31', '=31', '=32', '=32', '=33',
                    '=33', '=34', '=34', '=35', '=35', '=36', '=36', '=37', '=37', '=38',
                    '=38', '=39', '=39', '=3a', '=3A', '=3b', '=3B', '=3c', '=3C', '=3e',
                    '=3E', '=3f', '=3F', '=40', '=40', '=41', '=41', '=42', '=42', '=43',
                    '=43', '=44', '=44', '=45', '=45', '=46', '=46', '=47', '=47', '=48',
                    '=48', '=49', '=49', '=4a', '=4A', '=4b', '=4B', '=4c', '=4C', '=4d',
                    '=4D', '=4e', '=4E', '=4f', '=4F', '=50', '=50', '=51', '=51', '=52',
                    '=52', '=53', '=53', '=54', '=54', '=55', '=55', '=56', '=56', '=57',
                    '=57', '=58', '=58', '=59', '=59', '=5a', '=5A', '=5b', '=5B', '=5c',
                    '=5C', '=5d', '=5D', '=5e', '=5E', '=5f', '=5F', '=60', '=60', '=61',
                    '=61', '=62', '=62', '=63', '=63', '=64', '=64', '=65', '=65', '=66',
                    '=66', '=67', '=67', '=68', '=68', '=69', '=69', '=6a', '=6A', '=6b',
                    '=6B', '=6c', '=6C', '=6d', '=6D', '=6e', '=6E', '=6f', '=6F', '=70',
                    '=70', '=71', '=71', '=72', '=72', '=73', '=73', '=74', '=74', '=75',
                    '=75', '=76', '=76', '=77', '=77', '=78', '=78', '=79', '=79', '=7a',
                    '=7A', '=7b', '=7B', '=7c', '=7C', '=7d', '=7D', '=7e', '=7E', '=7f',
                    '=7F', '=80', '=80', '=81', '=81', '=82', '=82', '=83', '=83', '=84',
                    '=84', '=85', '=85', '=86', '=86', '=87', '=87', '=88', '=88', '=89',
                    '=89', '=8a', '=8A', '=8b', '=8B', '=8c', '=8C', '=8d', '=8D', '=8e',
                    '=8E', '=8f', '=8F', '=90', '=90', '=91', '=91', '=92', '=92', '=93',
                    '=93', '=94', '=94', '=95', '=95', '=96', '=96', '=97', '=97', '=98',
                    '=98', '=99', '=99', '=9a', '=9A', '=9b', '=9B', '=9c', '=9C', '=9d',
                    '=9D', '=9e', '=9E', '=9f', '=9F', '=a0', '=A0', '=a1', '=A1', '=a2',
                    '=A2', '=a3', '=A3', '=a4', '=A4', '=a5', '=A5', '=a6', '=A6', '=a7',
                    '=A7', '=a8', '=A8', '=a9', '=A9', '=aa', '=AA', '=ab', '=AB', '=ac',
                    '=AC', '=ad', '=AD', '=ae', '=AE', '=af', '=AF', '=b0', '=B0', '=b1',
                    '=B1', '=b2', '=B2', '=b3', '=B3', '=b4', '=B4', '=b5', '=B5', '=b6',
                    '=B6', '=b7', '=B7', '=b8', '=B8', '=b9', '=B9', '=ba', '=BA', '=bb',
                    '=BB', '=bc', '=BC', '=bd', '=BD', '=be', '=BE', '=bf', '=BF', '=c0',
                    '=C0', '=c1', '=C1', '=c2', '=C2', '=c3', '=C3', '=c4', '=C4', '=c5',
                    '=C5', '=c6', '=C6', '=c7', '=C7', '=c8', '=C8', '=c9', '=C9', '=ca',
                    '=CA', '=cb', '=CB', '=cc', '=CC', '=cd', '=CD', '=ce', '=CE', '=cf',
                    '=CF', '=d0', '=D0', '=d1', '=D1', '=d2', '=D2', '=d3', '=D3', '=d4',
                    '=D4', '=d5', '=D5', '=d6', '=D6', '=d7', '=D7', '=d8', '=D8', '=d9',
                    '=D9', '=da', '=DA', '=db', '=DB', '=dc', '=DC', '=dd', '=DD', '=de',
                    '=DE', '=df', '=DF', '=e0', '=E0', '=e1', '=E1', '=e2', '=E2', '=e3',
                    '=E3', '=e4', '=E4', '=e5', '=E5', '=e6', '=E6', '=e7', '=E7', '=e8',
                    '=E8', '=e9', '=E9', '=ea', '=EA', '=eb', '=EB', '=ec', '=EC', '=ed',
                    '=ED', '=ee', '=EE', '=ef', '=EF', '=f0', '=F0', '=f1', '=F1', '=f2',
                    '=F2', '=f3', '=F3', '=f4', '=F4', '=f5', '=F5', '=f6', '=F6', '=f7',
                    '=F7', '=f8', '=F8', '=f9', '=F9', '=fa', '=FA', '=fb', '=FB', '=fc',
                    '=FC', '=fd', '=FD', '=fe', '=FE', '=ff', '=FF');

    private static $qpTo = array("\x20", "\x20", "\x21", "\x21", "\x22", "\x22", "\x23", "\x23",
                  "\x24", "\x24", "\x25", "\x25", "\x26", "\x26", "\x27", "\x27",
                  "\x28", "\x28", "\x29", "\x29", "\x2A", "\x2A", "\x2B", "\x2B",
                  "\x2C", "\x2C", "\x2D", "\x2D", "\x2E", "\x2E", "\x2F", "\x2F",
                  "\x30", "\x30", "\x31", "\x31", "\x32", "\x32", "\x33", "\x33",
                  "\x34", "\x34", "\x35", "\x35", "\x36", "\x36", "\x37", "\x37",
                  "\x38", "\x38", "\x39", "\x39", "\x3A", "\x3A", "\x3B", "\x3B",
                  "\x3C", "\x3C", "\x3E", "\x3E", "\x3F", "\x3F", "\x40", "\x40",
                  "\x41", "\x41", "\x42", "\x42", "\x43", "\x43", "\x44", "\x44",
                  "\x45", "\x45", "\x46", "\x46", "\x47", "\x47", "\x48", "\x48",
                  "\x49", "\x49", "\x4A", "\x4A", "\x4B", "\x4B", "\x4C", "\x4C",
                  "\x4D", "\x4D", "\x4E", "\x4E", "\x4F", "\x4F", "\x50", "\x50",
                  "\x51", "\x51", "\x52", "\x52", "\x53", "\x53", "\x54", "\x54",
                  "\x55", "\x55", "\x56", "\x56", "\x57", "\x57", "\x58", "\x58",
                  "\x59", "\x59", "\x5A", "\x5A", "\x5B", "\x5B", "\x5C", "\x5C",
                  "\x5D", "\x5D", "\x5E", "\x5E", "\x5F", "\x5F", "\x60", "\x60",
                  "\x61", "\x61", "\x62", "\x62", "\x63", "\x63", "\x64", "\x64",
                  "\x65", "\x65", "\x66", "\x66", "\x67", "\x67", "\x68", "\x68",
                  "\x69", "\x69", "\x6A", "\x6A", "\x6B", "\x6B", "\x6C", "\x6C",
                  "\x6D", "\x6D", "\x6E", "\x6E", "\x6F", "\x6F", "\x70", "\x70",
                  "\x71", "\x71", "\x72", "\x72", "\x73", "\x73", "\x74", "\x74",
                  "\x75", "\x75", "\x76", "\x76", "\x77", "\x77", "\x78", "\x78",
                  "\x79", "\x79", "\x7A", "\x7A", "\x7B", "\x7B", "\x7C", "\x7C",
                  "\x7D", "\x7D", "\x7E", "\x7E", "\x7F", "\x7F", "\x80", "\x80",
                  "\x81", "\x81", "\x82", "\x82", "\x83", "\x83", "\x84", "\x84",
                  "\x85", "\x85", "\x86", "\x86", "\x87", "\x87", "\x88", "\x88",
                  "\x89", "\x89", "\x8A", "\x8A", "\x8B", "\x8B", "\x8C", "\x8C",
                  "\x8D", "\x8D", "\x8E", "\x8E", "\x8F", "\x8F", "\x90", "\x90",
                  "\x91", "\x91", "\x92", "\x92", "\x93", "\x93", "\x94", "\x94",
                  "\x95", "\x95", "\x96", "\x96", "\x97", "\x97", "\x98", "\x98",
                  "\x99", "\x99", "\x9A", "\x9A", "\x9B", "\x9B", "\x9C", "\x9C",
                  "\x9D", "\x9D", "\x9E", "\x9E", "\x9F", "\x9F", "\xA0", "\xA0",
                  "\xA1", "\xA1", "\xA2", "\xA2", "\xA3", "\xA3", "\xA4", "\xA4",
                  "\xA5", "\xA5", "\xA6", "\xA6", "\xA7", "\xA7", "\xA8", "\xA8",
                  "\xA9", "\xA9", "\xAA", "\xAA", "\xAB", "\xAB", "\xAC", "\xAC",
                  "\xAD", "\xAD", "\xAE", "\xAE", "\xAF", "\xAF", "\xB0", "\xB0",
                  "\xB1", "\xB1", "\xB2", "\xB2", "\xB3", "\xB3", "\xB4", "\xB4",
                  "\xB5", "\xB5", "\xB6", "\xB6", "\xB7", "\xB7", "\xB8", "\xB8",
                  "\xB9", "\xB9", "\xBA", "\xBA", "\xBB", "\xBB", "\xBC", "\xBC",
                  "\xBD", "\xBD", "\xBE", "\xBE", "\xBF", "\xBF", "\xC0", "\xC0",
                  "\xC1", "\xC1", "\xC2", "\xC2", "\xC3", "\xC3", "\xC4", "\xC4",
                  "\xC5", "\xC5", "\xC6", "\xC6", "\xC7", "\xC7", "\xC8", "\xC8",
                  "\xC9", "\xC9", "\xCA", "\xCA", "\xCB", "\xCB", "\xCC", "\xCC",
                  "\xCD", "\xCD", "\xCE", "\xCE", "\xCF", "\xCF", "\xD0", "\xD0",
                  "\xD1", "\xD1", "\xD2", "\xD2", "\xD3", "\xD3", "\xD4", "\xD4",
                  "\xD5", "\xD5", "\xD6", "\xD6", "\xD7", "\xD7", "\xD8", "\xD8",
                  "\xD9", "\xD9", "\xDA", "\xDA", "\xDB", "\xDB", "\xDC", "\xDC",
                  "\xDD", "\xDD", "\xDE", "\xDE", "\xDF", "\xDF", "\xE0", "\xE0",
                  "\xE1", "\xE1", "\xE2", "\xE2", "\xE3", "\xE3", "\xE4", "\xE4",
                  "\xE5", "\xE5", "\xE6", "\xE6", "\xE7", "\xE7", "\xE8", "\xE8",
                  "\xE9", "\xE9", "\xEA", "\xEA", "\xEB", "\xEB", "\xEC", "\xEC",
                  "\xED", "\xED", "\xEE", "\xEE", "\xEF", "\xEF", "\xF0", "\xF0",
                  "\xF1", "\xF1", "\xF2", "\xF2", "\xF3", "\xF3", "\xF4", "\xF4",
                  "\xF5", "\xF5", "\xF6", "\xF6", "\xF7", "\xF7", "\xF8", "\xF8",
                  "\xF9", "\xF9", "\xFA", "\xFA", "\xFB", "\xFB", "\xFC", "\xFC",
                  "\xFD", "\xFD", "\xFE", "\xFE", "\xFF", "\xFF");

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
        // separate header and body
        $inHeader = true;  // expecting header lines first
        $headers = array();
        $body = '';
        $lastheader = ''; // TODO: need to make that a direct reference to last header, doesn't work with multiple multiline headers
        $lines = explode("\n", $message);

        // read line by line
        foreach ($lines as $line) {
            if (!$inHeader) {
                $body .= $line . $EOL;
            } else if (!trim($line)) {
                $inHeader = false;
            } else if ($line[0] === ' ' || $line[0] === "\t") {
                if($lastheader == '') {
                    // headers do not start with an ordinary header line?
                    // then assume no headers at all
                    $inHeader = false;
                } else {
                    $headers[$lastheader] .= $EOL . trim($line);
                }
            } else if(!strpos($line, ':')) {
                // headers do not start with an ordinary header line?
                // then assume no headers at all
                $inHeader = false;
            } else {
                list($key, $value) = explode(':', $line, 2);
                $key = strtolower(trim($key));
                if(isset($headers[$key])) {
                    if(is_array($headers[$key])) {
                        $headers[$key][] = trim($value);
                    } else {
                        $headers[$key] = array($headers[$key], trim($value));
                    }
                } else {
                    $headers[$key] = trim($value);
                }
                $lastheader = $key;
            }
        }

        foreach($headers as $name => $value) {
            if(is_array($value)) {
                foreach($value as $key => $subvalue) {
                    $headers[$name][$key] = self::decodeQuotedPrintable($subvalue);
                }
            } else {
                $headers[$name] = self::decodeQuotedPrintable($value);
            }
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
        // avoid calling expensive regex
        if(strpos($string, '=') === false) {
            return $string;
        }
        if(!preg_match_all('%=\?([^?]+)\?(?:[Qq]\?)?(.*?)\?=\s*%', $string, $matches)) {
            return $string;
        }

        foreach($matches[0] as $key => $wholeMatch) {
            $replace = str_replace(self::$qpFrom, self::$qpTo, $matches[2][$key]);
            // TODO: charset conversion - charset is matches in $matches[1]
            $string = str_replace($wholeMatch, $replace, $string);
        }

        return str_replace('=3D', '=', $string);
    }
}
