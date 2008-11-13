<?php

require_once 'ZendL/Reflection/Docblock/Tag.php';

class ZendL_Reflection_Docblock implements Reflector
{
    protected $_reflector = null;
    
    protected $_startLine = null;
    protected $_endLine = null;
    
    protected $_docComment = null;
    protected $_cleanDocComment = null;
    
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
    
    public function __construct($commentOrReflector)
    {
        if ($commentOrReflector instanceof Reflector) {
            $this->_reflector = $commentOrReflector;
            if (!method_exists($commentOrReflector, 'getDocComment')) {
                throw new ZendL_Reflection_Exception('Reflector must contain method "getDocComment"');
            }
            $docComment = $commentOrReflector->getDocComment();
            
            $lineCount = substr_count($docComment, "\n");
            
            $this->_startLine = $this->_reflector->getStartLine() - $lineCount - 1;
            $this->_endLine   = $this->_reflector->getStartLine() - 1;
            
        } elseif (is_string($commentOrReflector)) {
            $docComment = $commentOrReflector;
        } else {
            throw new ZendL_Reflection_Exception(get_class($this) . ' must have a (string) DocComment or a Reflector in the constructor.');
        }
        
        if ($docComment == '') {
            throw new ZendL_Reflection_Exception('DocComment cannot be empty.');
        }
        
        $this->_docComment = $docComment;
        $this->_parse();
    }
    
    public function getContents()
    {
        return $this->_cleanDocComment;
    }
    
    public function getStartLine()
    {
        return $this->_startLine;
    }
    
    public function getEndLine()
    {
        return $this->_endLine;
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
        
        return false;
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
        $docComment = ltrim($docComment, "\r\n"); // @todo should be changed to remove first and last empty line
        
        $this->_cleanDocComment = $docComment;
        
        // Next parse out the tags and descriptions
        $parsedDocComment = $docComment;
        $lineNumber = $firstBlandLineEncountered = 0;
        while (($newlinePos = strpos($parsedDocComment, "\n")) !== false) {
            $lineNumber++;
            $line = substr($parsedDocComment, 0, $newlinePos);
            
            if ((strpos($line, '@') === 0) && (preg_match('#^(@\w+.*?)(\n)(?:@|\r?\n|$)#s', $parsedDocComment, $matches))) {
                $this->_tags[] = ZendL_Reflection_Docblock_Tag::factory($matches[1]);
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
