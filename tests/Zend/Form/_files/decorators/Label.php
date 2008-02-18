<?php
require_once 'PHPUnit/Util/Filter.php';
PHPUnit_Util_Filter::addFileToFilter(__FILE__);

require_once 'Zend/Form/Decorator/Abstract.php';

class My_Decorator_Label extends Zend_Form_Decorator_Abstract
{
    public function render($content)
    {
        return $content;
    }
}
