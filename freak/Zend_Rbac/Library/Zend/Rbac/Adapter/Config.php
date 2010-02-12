<?php
class Zend_Rbac_Adapter_Config extends Zend_Rbac_Adapter_Abstract {
	public static function setup(array $options) {
		return new Zend_Rbac($options);
	}
}
