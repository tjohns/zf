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
 * @package    Zend_Form
 * @subpackage Decorator
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Form_Decorator_HtmlTag */
require_once 'Zend/Form/Decorator/HtmlTag.php';

/**
 * Zend_Form_Decorator_Element_Fieldset
 *
 * Wraps content in a fieldset tag with an optional legend.
 *
 * Options accepted are:
 * - legend: legend to use with fieldset
 *
 * Any other options passed are processed as HTML attributes of the tag.
 * 
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Decorator
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */
class Zend_Form_Decorator_Fieldset extends Zend_Form_Decorator_HtmlTag
{
    /**
     * Render content wrapped in an HTML fieldset
     * 
     * @param  string $content 
     * @return string
     */
    public function render($content)
    {
        $options = $this->getOptions();
        $options['tag'] = 'fieldset';
        if (isset($options['legend'])) {
            if (!empty($options['legend'])) {
                $content = '<legend>' 
                         . (string) $options['legend'] 
                         . '</legend>' 
                         . PHP_EOL . $content;
            }
            unset($options['legend']);
        }
        $this->setOptions($options);
        return parent::render($content);
    }
}
