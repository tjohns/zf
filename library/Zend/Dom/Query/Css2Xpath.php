<?php
/**
 * Transform CSS selectors to XPath 
 * 
 * @package    Zend_Dom
 * @subpackage Query
 * @copyright  Copyright (C) 2007 - Present, Zend Technologies, Inc.
 * @license    New BSD {@link http://framework.zend.com/license/new-bsd}
 * @version    $Id$
 */
class Zend_Dom_Query_Css2Xpath
{
    /**
     * Transform CSS expression to XPath
     * 
     * @param  string $path 
     * @return string
     */
    public static function transform($path)
    {
        $path = (string) $path;
        if (strstr($path, ',')) {
            $paths       = explode(',', $path);
            $expressions = array();
            foreach ($paths as $path) {
                $xpath = self::transform(trim($path));
                if (is_string($xpath)) {
                    $expressions[] = $xpath;
                } elseif (is_array($xpath)) {
                    $expressions = array_merge($expressions, $xpath);
                }
            }
            return $expressions;
        }

        $paths    = array('//');
        $segments = preg_split('/\s+/', $path);
        foreach ($segments as $key => $segment) {
            $pathSegment = self::_tokenize($segment);
            if (0 == $key) {
                if (0 === strpos($pathSegment, '[contains(@class')) {
                    $paths[0] .= '*' . $pathSegment;
                } else {
                    $paths[0] .= $pathSegment;
                }
                continue;
            }
            if (0 === strpos($pathSegment, '[contains(@class')) {
                foreach ($paths as $key => $xpath) {
                    $paths[$key] .= '//*' . $pathSegment;
                    $paths[]      = $xpath . $pathSegment;
                }
            } else {
                foreach ($paths as $key => $xpath) {
                    $paths[$key] .= '//' . $pathSegment;
                }
            }
        }

        if (1 == count($paths)) {
            return $paths[0];
        }
        return implode(' | ', $paths);
    }

    /**
     * Tokenize CSS expressions to XPath
     * 
     * @param  string $expression 
     * @return string
     */
    protected static function _tokenize($expression)
    {
        // Child selectors
        $expression = str_replace('>', '/', $expression);

        // IDs
        $expression = preg_replace('|#([a-z][a-z0-9_-]*)|i', '[@id=\'$1\']', $expression);
        $expression = preg_replace('|(?<![a-z0-9_-])(\[@id=)|i', '*$1', $expression);

        // arbitrary attribute strict equality
        if (preg_match('|([a-z]+)\[([a-z0-9_-]+)=[\'"]([^\'"]+)[\'"]\]|i', $expression)) {
            $expression = preg_replace_callback(
                '|([a-z]+)\[([a-z0-9_-]+)=[\'"]([^\'"]+)[\'"]\]|i', 
                create_function(
                    '$matches',
                    'return $matches[1] . "[@" . strtolower($matches[2]) . "=\'" . $matches[3] . "\']";'
                ),
                $expression
            );
        }

        // arbitrary attribute contains full word
        if (preg_match('|([a-z]+)\[([a-z0-9_-]+)~=[\'"]([^\'"]+)[\'"]\]|i', $expression)) {
            $expression = preg_replace_callback(
                '|([a-z]+)\[([a-z0-9_-]+)~=[\'"]([^\'"]+)[\'"]\]|i', 
                create_function(
                    '$matches',
                    'return $matches[1] . "[contains(@" . strtolower($matches[2]) . ", \' $matches[3] \')]";'
                ),
                $expression
            );
        }

        // arbitrary attribute contains specified content
        if (preg_match('|([a-z]+)\[([a-z0-9_-]+)\*=[\'"]([^\'"]+)[\'"]\]|i', $expression)) {
            $expression = preg_replace_callback(
                '|([a-z]+)\[([a-z0-9_-]+)\*=[\'"]([^\'"]+)[\'"]\]|i', 
                create_function(
                    '$matches',
                    'return $matches[1] . "[contains(@" . strtolower($matches[2]) . ", \'" . $matches[3] . "\')]";'
                ),
                $expression
            );
        }

        // Classes
        $expression = preg_replace('|\.([a-z][a-z0-9_-]*)|i', "[contains(@class, ' \$1 ')]", $expression);

        return $expression;
    }
}
