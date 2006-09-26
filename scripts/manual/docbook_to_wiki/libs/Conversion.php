<?php 

class Conversion
{
    protected $filename;
    protected $stylesheet;
    
    protected function stripSpaces($haystack)
    {
        return preg_replace('/\s+/u', ' ', $haystack[0]);
    }
    
    protected function wikiLink($matches)
    {
        /**
         * @todo create links to real wiki pages
         **/
        return str_replace('.', '_', $matches[0]);
    }
    
    protected function processIncludes($haystack)
    {
        $path = dirname($this->filename);
        preg_match_all('/&module_specs.([a-zA-Z_]*);/', $haystack, $matches);
        
        if (count($matches)) {
            for ($i = 0; $i < count($matches[0]); $i++) {
                $haystack = str_replace($matches[0][$i], '', $haystack);
            }
        }
        
        return $haystack;
    }
    
    protected function parse($xml)
    {
        $data = '<chapter>';
        $data.= $xml;
        $data.= '</chapter>';
        
        $xml = new DOMDocument;
        $xsl = new DOMDocument();
        
        $xml->loadXML($data);
        $xsl->load($this->stylesheet);
        
        $proc = new XSLTProcessor;
        $proc->importStyleSheet($xsl);
        
        $this->filename = NULL;
        
        return html_entity_decode($proc->transformToXML($xml));
    }
    
    public function convert($filename, $stylesheet)
    {
        if (!is_readable($filename)) {
            throw new Exception("docbook file '$filename' could not be read");
        }
        
        if (!is_readable($stylesheet)) {
            throw new Exception("xsl stylesheet '$stylesheet' could not be read");
        }
        
        $this->filename   = $filename;
        $this->stylesheet = $stylesheet; 
        
        $tmp  = file_get_contents($filename);
        $tmp  = $this->processIncludes($tmp);
        
        $tmp = preg_replace_callback('/(?:<para>\s*)+(.+?)<\/para>/si',  array($this, 'stripSpaces'), $tmp);
        $tmp = preg_replace_callback('/(?:<note>\s*)+(.+?)<\/note>/si',  array($this, 'stripSpaces'), $tmp);
        $tmp = preg_replace_callback('/(?:<tip>\s*)+(.+?)<\/tip>/si',    array($this, 'stripSpaces'), $tmp);
        $tmp = preg_replace_callback('/(?:<row>\s*)+(.+?)<\/row>/si',    array($this, 'stripSpaces'), $tmp);
        
        $tmp = preg_replace_callback('/(?:<link linkend="([a-zA-Z0-9.#]+)">\s*).+<\/link>/si',  array($this, 'wikiLink'), $tmp);
        
        return $this->parse($tmp);
    }
}


?>