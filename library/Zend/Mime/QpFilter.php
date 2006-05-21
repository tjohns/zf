<?php
/**
 * PHP Stream Filter to quotedPrintable encode a stream
 * seems to work for single bucket streams (<8192 bytes)
 * open issues:
 * - looks like empty lines are completely ignored by Zend_Mime (therefore the test never succeeds)
 * - also, Zend_Mime cuts \r\n from the end of the string, which is not a good
 *   idea when the last character in a chunk is CRLF and the stream contiues afterwards...
 */
class Zend_Mime_QpFilter extends php_user_filter {
  const MODE_ENCODE = 1;
  const MODE_DECODE = 2;
  protected $mode = null;
  protected $lastline = "";
  
  function filter($in, $out, &$consumed, $closing)
  {
   while ($bucket = stream_bucket_make_writeable($in)) {
     //echo "<pre>BUCKET-------------------\n>>".$this->lastline."\n$bucket->data\n</pre>";
     if($this->mode == self::MODE_ENCODE) {
       // the previous bucket might have ended with a half line
       // that will be continued in the next bucket. therefore we
       // cut the last line off the result and save it for prepending
       // to the next bucket.
       $p = strrpos($bucket->data, "\n");
       if($p!==false) {
         $lastline= substr($bucket->data, $p+1); // everything after the last lineend
         $chunk = substr($bucket->data, 0, $p+1); // includes Lineend-Chars
       }
       else {
         $lastline = "";
         $chunk = $bucket->data;
       }
       // now encode the current chunk of data...
       $chunk = Zend_Mime::encodeQuotedPrintable($this->lastline.$chunk);
       $this->lastline = $lastline; // store last line for next call
     }
     else {
       $chunk = quoted_printable_decode($bucket->data);
     }
     $bucket->data = $chunk;
     $consumed += $bucket->datalen;
     $bucket->datalen = strlen($chunk);
     stream_bucket_append($out, $bucket);
   }
   return PSFS_PASS_ON;
  }

  function onCreate() {
    if(strpos($this->filtername, 'encode')) $this->mode= self::MODE_ENCODE;
    else $this->mode = self::MODE_DECODE;
  }

}
?>