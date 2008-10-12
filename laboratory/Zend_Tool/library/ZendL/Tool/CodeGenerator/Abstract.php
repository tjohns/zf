<?php

abstract class ZendL_Tool_CodeGenerator_Abstract
{
    
    protected $_sourceContent = null;
    protected $_isSourceDirty = true;
    
    final public function __construct(Array $options = array())
    {
        $this->_init();
        if ($options) {
            $this->setOptions($options);
        }
        $this->_prepare();
    }
    
    public function setOptions(Array $options)
    {
        foreach ($options as $optionName => $optionValue) {
            $methodName = 'set' . $optionName;
            if (method_exists($this, $methodName)) {
                call_user_func(array($this, $methodName), $optionValue);
            }
        }
    }
    
    public function setSourceContent($sourceContent)
    {
        $this->_sourceContent = $sourceContent;
        return;
    }
    
    public function getSourceContent()
    {
        return $this->_sourceContent;
    }
    
    //abstract public function fromString();
    
    protected function _init() {}
    protected function _prepare() {}
    
    abstract public function generate();
    
    final public function __toString()
    {
        return $this->generate();
    }

}
