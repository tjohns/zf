<?php
require_once 'PHPUnit/Framework/Constraint.php';
require_once 'Zend/Dom/Query.php';

class Zend_PHPUnit_Constraint_DomQuery extends PHPUnit_Framework_Constraint
{
    const ASSERT_SELECT           = 'assertSelect';
    const ASSERT_CONTENT_CONTAINS = 'assertSelectContentContains';
    const ASSERT_CONTENT_REGEX    = 'assertSelectContentRegex';
    const ASSERT_CONTENT_COUNT    = 'assertSelectCount';
    const ASSERT_CONTENT_COUNT_MIN= 'assertSelectCountMin';
    const ASSERT_CONTENT_COUNT_MAX= 'assertSelectCountMax';

    protected $_assertType        = null;
    protected $_assertTypes       = array(
        self::ASSERT_SELECT,
        self::ASSERT_CONTENT_CONTAINS,
        self::ASSERT_CONTENT_REGEX,
        self::ASSERT_CONTENT_COUNT,
        self::ASSERT_CONTENT_COUNT_MIN,
        self::ASSERT_CONTENT_COUNT_MAX,
    );

    protected $_content           = null;
    protected $_negate            = false;
    protected $_path              = null;

    /**
     * Constructor; setup constraint state
     * 
     * @param  string $path CSS selector path
     * @param  null|false|string|int $spec1 If false, no nodes should be found; if string, content in or regex of content contained by selected nodes; if int, min or max number of matching nodes
     * @param  null|bool|string $spec2 If $spec1 is a string and $spec2 is false, content should not match; if $spec1 is numeric and $spec2 is 'max' or 'exact', indicate max or exact number of nodes that should be returned
     * @return void
     */
    public function __construct($path, $spec1 = null, $spec2 = null)
    {
        $this->_path = $path;
    }

    /**
     * Indicate negative match
     * 
     * @param  bool $flag 
     * @return void
     */
    public function setNegate($flag = true)
    {
        $this->_negate = $flag;
    }

    /**
     * Evaluate an object to see if it fits the constraints
     * 
     * @param  string $other String to examine
     * @param  null|string Assertion type
     * @return bool
     */
    public function evaluate($other, $assertType = null)
    {
        if (strstr($assertType, 'Not')) {
            $this->setNegate(true);
            $assertType = str_replace('Not', '', $assertType);
        }

        if (!in_array($assertType, $this->_assertTypes)) {
            require_once 'Zend/PHPUnit/Constraint/Exception.php';
            throw new Zend_PHPUnit_Constraint_Exception(sprintf('Invalid assertion type "%s" provided to %s constraint', $assertType, __CLASS__));
        }

        $this->_assertType = $assertType;

        $domQuery = new Zend_Dom_Query($other);
        $result   = $domQuery->query($this->_path);
        $argv     = func_get_args();
        $argc     = func_num_args();

        switch ($assertType) {
            case self::ASSERT_CONTENT_CONTAINS:
                if (3 > $argc) {
                    require_once 'Zend/PHPUnit/Constraint/Exception.php';
                    throw new Zend_PHPUnit_Constraint_Exception('No content provided against which to match');
                }
                $this->_content = $content = $argv[2];
                return ($this->_negate)
                    ? $this->_notMatchContent($result, $content)
                    : $this->_matchContent($result, $content);
            case self::ASSERT_CONTENT_REGEX:
                if (3 > $argc) {
                    require_once 'Zend/PHPUnit/Constraint/Exception.php';
                    throw new Zend_PHPUnit_Constraint_Exception('No pattern provided against which to match');
                }
                $this->_content = $content = $argv[2];
                return ($this->_negate)
                    ? $this->_notRegexContent($result, $content)
                    : $this->_regexContent($result, $content);
            case self::ASSERT_CONTENT_COUNT:
            case self::ASSERT_CONTENT_COUNT_MIN:
            case self::ASSERT_CONTENT_COUNT_MAX:
                if (3 > $argc) {
                    require_once 'Zend/PHPUnit/Constraint/Exception.php';
                    throw new Zend_PHPUnit_Constraint_Exception('No count provided against which to compare');
                }
                $this->_content = $content = $argv[2];
                return $this->_countContent($result, $content, $assertType);
            case self::ASSERT_SELECT:
            default:
                if ($this->_negate) {
                    return (0 == count($result));
                } else {
                    return (0 != count($result));
                }
        }
    }

    /**
     * Report Failure
     * 
     * @see    PHPUnit_Framework_Constraint for implementation details
     * @param  mixed $other CSS selector path
     * @param  string $description 
     * @param  bool $not 
     * @return void
     * @throws PHPUnit_Framework_ExpectationFailedException
     */
    public function fail($other, $description, $not = false)
    {
        require_once 'Zend/PHPUnit/Constraint/Exception.php';
        switch ($this->_assertType) {
            case self::ASSERT_CONTENT_CONTAINS:
                $failure = 'Failed asserting node denoted by %s CONTAINS content "%s"';
                if ($this->_negate) {
                    $failure = 'Failed asserting node DENOTED BY %s DOES NOT CONTAIN content "%s"';
                }
                $failure = sprintf($failure, $other, $this->_content);
                break;
            case self::ASSERT_CONTENT_REGEX:
                $failure = 'Failed asserting node denoted by %s CONTAINS content MATCHING "%s"';
                if ($this->_negate) {
                    $failure = 'Failed asserting node DENOTED BY %s DOES NOT CONTAIN content MATCHING "%s"';
                }
                $failure = sprintf($failure, $other, $this->_content);
                break;
            case self::ASSERT_CONTENT_COUNT:
                $failure = 'Failed asserting node DENOTED BY %s OCCURS EXACTLY %i times';
                if ($this->_negate) {
                    $failure = 'Failed asserting node DENOTED BY %s DOES NOT OCCUR EXACTLY %i times';
                }
                $failure = sprintf($failure, $other, $this->_content);
                break;
            case self::ASSERT_CONTENT_COUNT_MIN:
                $failure = 'Failed asserting node DENOTED BY %s OCCURS AT LEAST %i times';
                $failure = sprintf($failure, $other, $this->_content);
                break;
            case self::ASSERT_CONTENT_COUNT_MAX:
                $failure = 'Failed asserting node DENOTED BY %s OCCURS AT MOST %i times';
                $failure = sprintf($failure, $other, $this->_content);
                break;
            case self::ASSERT_SELECT:
            default:
                $failure = 'Failed asserting node DENOTED BY %s EXISTS';
                if ($this->_negate) {
                    $failure = 'Failed asserting node DENOTED BY %s DOES NOT EXIST';
                }
                $failure = sprintf($failure, $other);
                break;
        }

        if (!empty($description)) {
            $failure = $description . "\n" . $failure;
        }

        throw new Zend_PHPUnit_Constraint_Exception($failure);
    }

    /**
     * Complete implementation
     * 
     * @return string
     */
    public function toString()
    {
        return '';
    }

    /**
     * Check to see if content is matched in selected nodes
     * 
     * @param  Zend_Dom_Query_Result $result 
     * @param  string $match Content to match
     * @return bool
     */
    protected function _matchContent($result, $match)
    {
        if (0 == count($result)) {
            return false;
        }

        foreach ($result as $node) {
            $content = $this->_getNodeContent($node);
            if (strstr($content, $match)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check to see if content is NOT matched in selected nodes
     * 
     * @param  Zend_Dom_Query_Result $result 
     * @param  string $match 
     * @return bool
     */
    protected function _notMatchContent($result, $match)
    {
        if (0 == count($result)) {
            return true;
        }

        foreach ($result as $node) {
            $content = $this->_getNodeContent($node);
            if (strstr($content, $match)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check to see if content is matched by regex in selected nodes
     * 
     * @param  Zend_Dom_Query_Result $result 
     * @param  string $pattern
     * @return bool
     */
    protected function _regexContent($result, $pattern)
    {
        if (0 == count($result)) {
            return false;
        }

        foreach ($result as $node) {
            $content = $this->_getNodeContent($node);
            if (preg_match($pattern, $content)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check to see if content is NOT matched by regex in selected nodes
     * 
     * @param  Zend_Dom_Query_Result $result 
     * @param  string $pattern
     * @return bool
     */
    protected function _notRegexContent($result, $pattern)
    {
        if (0 == count($result)) {
            return true;
        }

        foreach ($result as $node) {
            $content = $this->_getNodeContent($node);
            if (preg_match($pattern, $content)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine if content count matches criteria
     * 
     * @param  Zend_Dom_Query_Result $result 
     * @param  int $test Value against which to test
     * @param  string $type assertion type
     * @return boolean
     */
    protected function _countContent($result, $test, $type)
    {
        $count = count($result);

        switch ($type) {
            case self::ASSERT_CONTENT_COUNT:
                return ($this->_negate)
                    ? ($test != $count)
                    : ($test == $count);
            case self::ASSERT_CONTENT_COUNT_MIN:
                return ($count >= $test);
            case self::ASSERT_CONTENT_COUNT_MAX:
                return ($count <= $test);
            default:
                return false;
        }
    }

    /**
     * Get node content, minus node markup tags
     * 
     * @param  DOMNode $node 
     * @return string
     */
    protected function _getNodeContent(DOMNode $node)
    {
        $doc     = $node->ownerDocument;
        $content = $doc->saveXML($node);
        $tag     = $node->nodeName;
        $regex   = '|</?' . $tag . '[^>]*>|';
        return preg_replace($regex, '', $content);
    }
}
