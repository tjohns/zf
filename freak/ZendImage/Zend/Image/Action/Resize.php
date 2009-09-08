<?php

class Zend_Image_Action_Resize extends Zend_Image_Action_Abstract {

    const NAME  = 'Resize';

    const TYPE_ABSOLUTE = 0;
    const TYPE_PERCENTAGE = 1;

    protected $_xAmount;
    protected $_yAmount;

    protected $_xType;
    protected $_yType;

    protected $_constrainProportions = true;

    public function  __construct(array $options = array()) {

        foreach($options as $key => $value) {
            switch($key) {
                case 'xAmount':
                    $this->setXAmount($value);
                    break;
                case 'yAmount':
                    $this->setYAmount($value);
                    break;
                case 'xType':
                    $this->setXType($value);
                    break;
                case 'yType':
                    $this->setYType($value);
                    break;
                case 'filter':
                    $this->setFilter($value);
                    break;
            }
        }

        if (!isset($this->_xType)){
            $this->setXType(self::TYPE_ABSOLUTE);
        }

        if (!isset($this->_yType)){
            $this->setYType(self::TYPE_ABSOLUTE);
        }

    }

    public function setFilter($filter){
        $this->_filter = $filter;
    }

    public function getFilter(){
        return $this->_filter;
    }

    public function setXAmount($value){
        $this->_xAmount = $value;
    }

    public function setYAmount($value){
        $this->_yAmount = $value;
    }

    public function getXAmount(){
        return $this->_xAmount;
    }

    public function getYAmount(){
        return $this->_yAmount;
    }

    public function setXType($value){
        $this->_xType = $value;
    }

    public function setYType($value){
        $this->_yType = $value;
    }

    public function getName() {
        return 'Resize';
    }

}