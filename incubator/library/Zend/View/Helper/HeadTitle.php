<?php

require_once 'Zend/View/Helper/Placeholder/Container/Abstract.php';

class Zend_View_Helper_HeadTitle extends Zend_View_Helper_Placeholder_Container_Abstract
{
    /**
     * Enter description here...
     *
     * @param string $title
     * @param string $prefix
     * @param string $postfix
     * @param string $separator
     * @return Zend_View_Helper_HeadTitle
     */
    public function headTitle($title = null, $prefix = null, $postfix = null, $separator = null, $setType = Zend_View_Helper_Placeholder_Container_Abstract::APPEND)
    {
        if ($title) {
            if ($setType == Zend_View_Helper_Placeholder_Container_Abstract::SET) {
                $this->set($title);
            } else {
                $this->append($title);
            }
        }
        
        if ($prefix) {
            $this->setPrefix($prefix);
        }
        
        if ($postfix) {
            $this->setPostfix($postfix);
        }
        
        if ($separator) {
            $this->setSeparator($separator);
        }
        
        return $this;
    }
    
    /**
     * Render the placeholder
     *
     * @return string
     */
    public function toString($indent = null)
    {
        $return = parent::toString($indent);
        return '<title>' . $return . '</title>';
    }

    
    /**
     * __toString()
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }
    
}