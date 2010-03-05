<?php

namespace zend;

class Options
{
    public static function setOptions($object, array $options)
    {
        if (!is_object($object)) {
            return;
        }
        foreach ($options as $key => $value) {
            $method = 'set' . self::_normalizeKey($key);
//            if (method_exists($object, $method)) {
                $object->$method($value);
//            }
        }
    }

    public static function setConstructorOptions($object, $options)
    {
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        }
        if (is_array($options)) {
            self::setOptions($object, $options);
        }
    }

    protected static function _normalizeKey($key)
    {
        $option = str_replace('_', ' ', strtolower($key));
        $option = str_replace(' ', '', ucwords($option));
        return $option;
    }
}
