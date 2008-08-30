<?php

require_once 'ZendL/Tool/CodeGenerator/Php/Abstract.php';
require_once 'ZendL/Tool/CodeGenerator/Php/Docblock/Tag.php';

class ZendL_Tool_CodeGenerator_Php_Docblock extends ZendL_Tool_CodeGenerator_Php_Abstract
{
    
    protected $_shortDescription = null;
    protected $_longDescription = null;
    protected $_tags = array();
    
    public static function fromReflection(ZendL_Reflection_Docblock $reflectionDocblock)
    {
        $docblock = new self();
        
        $docblock->setSourceContent($reflectionDocblock->getContents());
        $docblock->setSourceDirty(false);
        
        $docblock->setShortDescription($reflectionDocblock->getShortDescription());
        $docblock->setLongDescription($reflectionDocblock->getLongDescription());
        
        foreach ($reflectionDocblock->getTags() as $tag) {
            $docblock->setTag(ZendL_Tool_CodeGenerator_Php_Docblock_Tag::fromReflection($tag));
        }
        
        return $docblock;
    }
    
    public function setShortDescription($shortDescription)
    {
        $this->_shortDescription = $shortDescription;
        return $this;
    }
    
    public function getShortDescription()
    {
        return $this->_shortDescription;
    }
    
    public function setLongDescription($longDescription)
    {
        $this->_longDescription = $longDescription;
        return $this;
    }
    
    public function getLongDescription()
    {
        return $this->_longDescription;
    }
    
    public function setTags(Array $tags)
    {
        foreach ($tags as $tag) {
            $this->setTag($tag);
        }
        
        return $this;
    }
    
    public function setTag($tag)
    {
        if (is_array($tag)) {
            $tag = new ZendL_Tool_CodeGenerator_Php_Docblock_Tag($tag);
        } elseif ($tag instanceof ZendL_Tool_CodeGenerator_Php_Docblock_Tag) {
            // anything here?
        } else {
            require_once 'ZendL/Tool/CodeGenerator/Php/Exception.php';
            throw new ZendL_Tool_CodeGenerator_Php_Exception('setTag() expects either an array of method options or an instance of ZendL_Tool_CodeGenerator_Php_Docblock_Tag');
        }
        
        $this->_tags[] = $tag;
        return $this;
    }
    
    public function getTags()
    {
        return $this->_tags;
    }
    
    public function generate()
    {
        if (!$this->isSourceDirty()) {
            return $this->_docCommentize($this->getSourceContent());
        }
        
        $output = $this->getShortDescription() . PHP_EOL . PHP_EOL;
        $output .= $this->getShortDescription() . PHP_EOL . PHP_EOL;
        foreach ($this->getTags() as $tag) {
            $output .= $tag->generate();
        }
        
        return $this->_docCommentize($output);
    }
    
    protected function _docCommentize($content)
    {
        $output = '/**' . PHP_EOL;
        $content = wordwrap($content, 80, "\n");
        $lines = explode("\n", $content);
        foreach ($lines as $line) {
            $output .= ' * ' . $line . PHP_EOL;
        }
        $output .= ' */' . PHP_EOL;
        return $output;
    }
    
}