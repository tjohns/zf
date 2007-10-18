<?php

class Zend_View_Helper_Doctype
{
    
    const XHTML1_STRICT       = 'XHTML1_STRICT';
    const XHTML1_TRANSITIONAL = 'XHTML1_TRANSITIONAL';
    const XHTML1_FRAMESET     = 'XHTML1_FRAMESET';
    const HTML4_STRICT        = 'HTML4_STRICT';
    const HTML4_LOOSE         = 'HTML4_LOOSE';
    const HTML4_FRAMESET      = 'HTML4_FRAMESET';
    const CUSTOM_XHTML        = 'CUSTOM_XHTML';
    const CUSTOM              = 'CUSTOM';
    
    protected $_doctype = self::HTML4_LOOSE;
    
    public function doctype($doctype)
    {
        switch ($doctype) {
            
            case self::XHTML1_STRICT:
                $this->_doctype = self::XHTML1_STRICT;
                return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
            case self::XHTML1_TRANSITIONAL:
                $this->_doctype = self::XHTML1_TRANSITIONAL;
                return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
            case self::XHTML1_FRAMESET:
                $this->_doctype = self::XHTML1_FRAMESET;
                return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">';
            case self::HTML4_STRICT:
                $this->_doctype = self::HTML4_STRICT;
                return '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">';
            case self::HTML4_LOOSE:
                $this->_doctype = self::HTML4_LOOSE;
                return '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
            case self::HTML4_FRAMESET:
                $this->_doctype = self::HTML4_FRAMESET;
                return '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">';
            default:
                if (substr($doctype, 0, 7) !== '<!DOCTYPE') {
                    throw new Zend_View_Exception('The specified doctype is malformed.');
                }
                if (stristr($doctype, 'xhtml')) {
                    $this->_doctype = self::CUSTOM_XHTML;
                } else {
                    $this->_doctype = self::CUSTOM;
                }
                return $doctype;
        }
    }
    
    public function getDoctype()
    {
        return $this->_doctype;
    }
    
    public function isXhtml()
    {
        return (stristr($this->_doctype, 'xhtml') ? true : false);
    }
}