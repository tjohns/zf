<?php

class Zend_View_Helper_HeadScript extends Zend_View_Helper_Placeholder_Container_Abstract 
{

    const FILE = 'FILE';
    const SCRIPT = 'SCRIPT';
    
    protected $_captureTypeOrAttrs = null;
    
    protected $_view = null;

    /**
     * headScript is required by the View harness, view's access this object via this method.
     *
     * @return Zend_View_Helper_HeadScript
     */
    public function headScript()
    {
        return $this;
    }
    
    public function setView($view)
    {
        $this->_view = $view;
    }
    
    public function append($sourceFile, $type = 'text/javascript')
    {
        $this->_offsetSet($this->nextIndex(), $script, $type, self::FILE);
        return $this;
    }
    
    public function appendScript($script, $type = 'text/javascript')
    {
        $this->_offsetSet($this->nextIndex(), $script, $type, self::SCRIPT);
        return $this;
    }
    
    public function offsetSet($index, $sourceFile, $typeOrAttrs = 'text/javascript')
    {
        $this->_offsetSet($index, $script, $typeOrAttrs, self::FILE);
        return $this;
    }
    
    public function offsetSetScript($index, $script, $typeOrAttrs = 'text/javascript')
    {
        $this->_offsetSet($index, $sourceFile, $typeOrAttrs, self::SCRIPT);
        return $this;
    }
    
    protected function _offsetSet($index, $scriptOrSourceFile, $typeOrAttrs = 'text/javascript', $mode = Zend_View_Helper_HeadScript::FILE)
    {
        $valueArray = array(
            'content' => $scriptOrSourceFile,
            'mode'    => $mode
            );
        
        if (is_array($type)) {
            $valueArray = array_merge($typeOrAttrs, $valueArray);
        } else {
            $valueArray['type'] = (string) $typeOrAttrs;
        }
            
        parent::offsetSet($index, $valueArray);
        return $this;
    }
    
    public function captureStart($captureType = Zend_View_Helper_Placeholder_Container_Abstract::APPEND, $typeOrAttrs = 'text/javascript')
    {
        $this->_captureTypeOrAttrs = $typeOrAttrs;
        return parent::captureStart($captureType);
    }
    
    public function captureEnd()
    {
        $content = ob_get_clean();
        $typeOrAttrs = $this->_captureTypeOrAttrs;
        $this->_captureTypeOrAttrs = null;
        
        $this->_captureLock = false;
        switch ($this->_captureType) {
            case self::SET:
                $this->exchangeArray(array($valueArray));
                break;
            case self::APPEND:
            default:
                $this->_offsetSet($this->nextIndex(), $content, $this->_captureTypeOrAttrs, self::SCRIPT);
                break;
        }
    }
    
    // make sure this method of this object is not publically available.
    protected function exchangeArray($input)
    {
        return parent::exchangeArray($input);
    }
    
    
    public function toString()
    {
        $useCdata = false;
        
        if (array_key_exists('doctype', $this->_view->getHelpers())) {
            $useCdata = (stristr($this->_view->getHelper('doctype')->getDoctype(), 'xhtml')) ? true : false;
        }
        
        $output = '';
        
        foreach ($this->_scripts as $script) {
            
            $output .= '<script ';
            
            $content = null;
            if (isset($script['content'])) {
                $content = $script['content'];
            }
            unset($script['content']);
            
            $mode = $script['mode'];
            unset($script['mode']);
            
            foreach ($script as $name => $attr) {
                $output .= $name . '="' . $attr . '" ';
            }
            
            $output = rtrim($output) . '>';
            
            if ($content) {
                if ($useCdata) {
                    $output .= PHP_EOL . $this->_indent . '//<![CDATA[' . PHP_EOL . $this->_indent . $content . PHP_EOL . $this->_indent . '//]]>' . PHP_EOL . $this->_indent;
                } else {
                    $output .= $content;
                }
            }
            
            $output .= '</script>' . PHP_EOL . $this->_indent;
            
        }
        
        return $output;
    }

}