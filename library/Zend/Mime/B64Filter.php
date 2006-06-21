<?php
/**
 * PHP Stream Filter to encode and decode streams with Base64-encoding
 *
 * usage:
 *  Zend::loadClass('Zend_Mime_B64Filter');
 *  stream_filter_register('base64.*', 'Zend_Mime_B64Filter');
 *  stream_filter_append($fp, 'base64.encode', STREAM_FILTER_READ);
 * or
 *  stream_filter_append($fp, 'base64.decode', STREAM_FILTER_READ);
 */
class Zend_Mime_B64Filter extends php_user_filter
{
    const MODE_ENCODE = 1;
    const MODE_DECODE = 2;
    protected $mode = null;
    protected $backlog = '';
    protected $lastline = '';

    function filter($in, $out, &$consumed, $closing)
    {
        while ($bucket = stream_bucket_make_writeable($in)) {
            $chunk = '';
            if ($this->mode == self::MODE_ENCODE) {
                if ($bucket->datalen > 0) {
                    // first check that we only encode chunks that
                    // have a length that can be divided by 3
                    $trailing = (strlen($this->backlog) . $bucket->datalen) % 3;
                    // cut the trailing characters to that the
                    // remainder is divisible by 3 if it is not.
                    if ($trailing > 0) {
                        $backlog = substr($bucket->data, (0 - $trailing));
                        $chunk = substr($bucket->data, 0, (0 - $trailing));
                    } else {
                        $backlog = '';
                        $chunk = $bucket->data;
                    }
                    $chunk = base64_encode($this->backlog . $chunk);
                    $chunk = chunk_split($this->lastline . $chunk, Zend_Mime::LINELENGTH, Zend_Mime::LINEEND);
                    $chunk = rtrim($chunk);
                    $this->backlog = $backlog; // save backlog for next chunk...
                    // now cut the last line off, because this might
                    // not be LINEEND characters. We prepend this to
                    // the next chunk...
                    $p = strrpos($chunk, "\n"); // cut off last line
                    if ($p !== false) {
                        $this->lastline = substr($chunk, $p + 1); // everything after the last lineend
                        $chunk = substr($chunk, 0, $p + 1); // includes Lineend-Chars
                    }
                } else {
                    if ($this->lastline . $this->backlog == '') {
                        return PSFS_FLAG_FLUSH_CLOSE;
                    }
                    $chunk = $this->lastline . base64_encode($this->backlog);
                    $this->lastline = '';
                    $this->backlog = '';
                }
            } else { // DECODE
                if ($bucket->datalen > 0) {
                    $chunk = strtr($bucket->data, array("\n" => '', "\r" => ''));
                    $trailing = (strlen($this->backlog) + strlen($chunk)) % 4;
                    // cut the trailing characters to that the
                    // remainder is dividable by 3 if it is not.
                    if ($trailing > 0) {
                        $backlog = substr($chunk, (0 - $trailing));
                        $chunk = substr($chunk, 0, (0 - $trailing));
                    } else {
                        $backlog = '';
                    }
                } else {
                    // flush backlog
                    $backlog = '';
                }
                $chunk = base64_decode($this->backlog . $chunk);
                $this->backlog = $backlog;
            }
            $bucket->data = $chunk;
            $consumed += $bucket->datalen;
            $bucket->datalen = strlen($chunk);
            stream_bucket_append($out, $bucket);
        }
        return PSFS_PASS_ON;
    }

    function onCreate()
    {
        if (strpos($this->filtername, 'encode')) {
            $this->mode = self::MODE_ENCODE;
        } else {
            $this->mode = self::MODE_DECODE;
        }
    }

}
