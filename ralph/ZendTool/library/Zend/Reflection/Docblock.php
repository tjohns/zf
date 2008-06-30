<?php

require_once 'Zend/Reflection/Docblock/Tag.php';

class Zend_Reflection_Docblock implements Reflector
{
    
    protected $_docComment = null;
    
    protected $_longDescription = null;
    protected $_shortDescription = null;
    protected $_tags = array();
    
    public static function export()
    {
        /* huh? */
    }
    
    public function __toString()
    {
        /* wha? */
    }
    
    public function __construct($docComment)
    {
        if ($docComment == '') {
            throw new Zend_Reflection_Exception('DocComment is empty.');
        }
        
        $this->_docComment = $docComment;
        $this->_parse();
    }
    
    public function getShortDescription()
    {
        return $this->_shortDescription;
    }
    
    public function getLongDescription()
    {
        return $this->_longDescription;
    }
    
    public function hasTag($name)
    {
        foreach ($this->_tags as $tag) {
            if ($tag->getName() == $name) {
                return true;
            }
        }
        return false;
    }
    
    public function getTag($name)
    {
        foreach ($this->_tags as $tag) {
            if ($tag->getName() == $name) {
                return $tag;
            }
        }
    }
    
    public function getTags($filter = null)
    {
        if ($filter === null || !is_string($filter)) {
            return $this->_tags;
        }
        
        $returnTags = array();
        foreach ($this->_tags as $tag) {
            if ($tag->getName() == $filter) {
                $returnTags[] = $tag;
            }
        }
        return $returnTags;
    }
    
    protected function _parse()
    {
        $docComment = $this->_docComment;
        
        // First remove doc block line starters
        $docComment = preg_replace('#[ \t]*(?:\/\*\*|\*\/|\*)?[ ]{0,1}(.*)?#', '$1', $docComment);
        $docComment = ltrim($docComment, "\r\n");
        
        // Next parse out the tags and descriptions
        $parsedDocComment = $docComment;
        $lineNumber = $firstBlandLineEncountered = 0;
        while (($newlinePos = strpos($parsedDocComment, "\n")) !== false) {
            $lineNumber++;
            $line = substr($parsedDocComment, 0, $newlinePos);
            
            if ((strpos($line, '@') === 0) && (preg_match('#^(@\w+.*?)(\n)(?:@|\r?\n|$)#s', $parsedDocComment, $matches))) {
                $this->_tags[] = Zend_Reflection_Docblock_Tag::factory($matches[1]);
                $parsedDocComment = str_replace($matches[1] . $matches[2], '', $parsedDocComment);
            } else {
                if ($lineNumber < 3 && !$firstBlandLineEncountered) {
                    $this->_shortDescription .= $line . "\n";
                } else {
                    $this->_longDescription .= $line . "\n";
                }
                
                if ($line == '') {
                    $firstBlandLineEncountered = true;
                }
                
                $parsedDocComment = substr($parsedDocComment, $newlinePos + 1);
            }
            
        }

        $this->_shortDescription = rtrim($this->_shortDescription);
        $this->_longDescription = rtrim($this->_longDescription);
    }
    
    
}
