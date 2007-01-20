<?php

/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Environment
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Exception.php 2794 2007-01-16 01:29:51Z bkarwin $
 */


/**
 * Zend_View_Abstract
 */
require_once('Zend/View/Abstract.php');


/**
 * @category   Zend
 * @package    Zend_Environment
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Environment_View_Text extends Zend_View_Abstract
{
    protected $_width = 80;
    protected $_inset = 5;
    protected $_eol = "\n";
    protected $_underline = "=";
    protected $_delimiter = " => ";

    /**
     * Subclass the default Zend_View to provide defaults. Since this class has
     * embedded view logic no script name is needed. To render, simply call the
     * render() method with a null parameter
     *
     * E.g. return $view->render(null);
     *
     * Extra config settings:-
     * 'width'     : Width (in characters) of output
     * 'inset'     : Inset width (in characters) of section properties
     * 'eol'       : End of line character (defaults to LF)
     * 'underline' : kkk
     * 'delimiter' : The string used between the property and the value
     *
     * @param  array $config
     * @return void
     */
    public function __construct($config = array())
    {
        if (!isset($config['scriptPath'])) {
            $config['scriptPath'] = dirname(__FILE__);
        }
        
        if (isset($config['width'])) {
            $this->_width = $config['width'];
        }

        if (isset($config['inset'])) {
            $this->_inset = $config['inset'];
        }

        if (isset($config['eol'])) {
            $this->_eol = $config['eol'];
        }

        if (isset($config['underline'])) {
            $this->_underline = $config['underline'];
        }

        if (isset($config['delimiter'])) {
            $this->_delimiter = $config['delimiter'];
        }

        return parent::__construct($config);
    }

    /**
     * The default text template iterates through each section within the
     * environment and displays the default fields in a formatted matrix.
     *
     * It is set to display in an 80 character width but can be overridden by
     * either providing appropriate settings in the $config property or by
     * subclassing this component.
     *
     * @return string
     */
    protected function _run()
    {
        $width = $this->_width - 10 - $this->_inset;
        $padding = $this->_width - $width;
        
        foreach ($this->environment as $section) {

            echo $section->getType() . $this->_eol . str_repeat($this->_underline, $this->_width) . $this->_eol;

            foreach ($section as $field) {

                echo str_repeat(' ', $this->_inset) . sprintf("%10s %-{$width}s{$this->_eol}", 'Name:', $field->name);
                echo str_repeat(' ', $this->_inset) . sprintf("%10s %-{$width}s{$this->_eol}", 'Title:', $field->title);
                echo str_repeat(' ', $this->_inset) . sprintf("%10s %-{$width}s{$this->_eol}", 'Value:', $field->value);
                echo str_repeat(' ', $this->_inset) . sprintf("%10s %-{$width}s{$this->_eol}", 'Version:', $field->version);
                echo str_repeat(' ', $this->_inset) . sprintf("%10s %-{$width}s{$this->_eol}", 'Info:', $field->info);

                if ($field->info) {
                    foreach ($field->info as $key => $value) {
                        $indent = strlen($key) + 4;

                        if (is_array($value)) {
                            $values = array();

                            foreach ($value as $index => $property) {
                                $values[] = $index . $this->_delimiter . $property;
                            }

                            $value = join($this->_eol . str_repeat(' ', $indent + $padding), $values);
                        }

                        $content = wordwrap($value, $width - $indent, $this->_eol . str_repeat(' ', $padding + $indent));
                        echo str_repeat(' ', $padding) . $key
                                                       . $this->_delimiter
                                                       . $content
                                                       . $this->_eol;
                    }
                }

                echo $this->_eol;
            }
        }
    }
}
