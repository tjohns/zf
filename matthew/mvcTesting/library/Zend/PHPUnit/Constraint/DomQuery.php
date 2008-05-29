<?php
require_once 'PHPUnit/Framework/Constraint.php';
require_once 'Zend/Dom/Query.php';

class Zend_PHPUnit_Constraint_DomQuery extends PHPUnit_Framework_Constraint
{
    protected $_contentCount      = false;
    protected $_contentCountExact = null;
    protected $_contentCountMax   = null;
    protected $_contentCountMin   = null;
    protected $_contentRegex      = null;
    protected $_contentToMatch    = null;
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
        if (null !== $spec1) {
            if (false === $spec1) {
                $this->_negate = true;
            } elseif (is_string($spec1)) {
                if ('/' == $spec1[0]) {
                    $this->_contentRegex = $spec1;
                } else {
                    $this->_contentToMatch = $spec1;
                }
                if (is_bool($spec2)) {
                    $this->_negate = !$spec2;
                }
            } elseif (is_numeric($spec1)) {
                $this->_contentCount = true;
                if ('max' == strtolower($spec2)) {
                    $this->_contentCountMax   = (int) $spec1;
                } elseif ('exact' == strtolower($spec2)) {
                    $this->_contentCountExact = (int) $spec1;
                } else {
                    $this->_contentCountMin   = (int) $spec1;
                }
            }
        }
    }

    /**
     * Evaluate an object to see if it fits the constraints
     * 
     * @param   $other 
     * @return void
     */
    public function evaluate($other)
    {
        $domQuery = new Zend_Dom_Query($other);
        $result   = $domQuery->query($this->_path);

        if (null !== $this->_contentToMatch) {
            if ($this->_negate) {
                return $this->_notMatchContent($result);
            } else {
                return $this->_matchContent($result);
            }
        } 

        if (null !== $this->_contentRegex) {
            if ($this->_negate) {
                return $this->_notRegexContent($result);
            } else {
                return $this->_regexContent($result);
            }
        }
        
        if ($this->_contentCount) {
            return $this->_countContent($result);
        }

        if ($this->_negate) {
            return (0 == count($result));
        }

        return (0 != count($result));
    }

    /**
     * Report Failure
     * 
     * @see    PHPUnit_Framework_Constraint for implementation details
     * @param  mixed $other 
     * @param  string $description 
     * @param  bool $not 
     * @return void
     * @throws PHPUnit_Framework_ExpectationFailedException
     */
    public function fail($other, $description, $not = false)
    {
        require_once 'Zend/PHPUnit/Constraint/Exception.php';
        if (null !== $this->_contentToMatch) {
            if ($this->_negate) {
                $failure = 'Failed asserting node DENOTED BY %s DOES NOT CONTAIN content "%s"';
            } else {
                $failure = 'Failed asserting node denoted by %s CONTAINS content "%s"';
            }
            throw new Zend_PHPUnit_Constraint_Exception(
                sprintf($failure, $other, $this->_contentToMatch),
                null
            );
        } 

        if (null !== $this->_contentRegex) {
            if ($this->_negate) {
                $failure = 'Failed asserting node DENOTED BY %s DOES NOT CONTAIN content MATCHING "%s"';
            } else {
                $failure = 'Failed asserting node denoted by %s CONTAINS content MATCHING "%s"';
            }
            throw new Zend_PHPUnit_Constraint_Exception(
                sprintf($failure, $other, $this->_contentRegex),
                null
            );
        }
        
        if ($this->_contentCount) {
            if (null !== $this->_contentCountExact) {
                $failure = 'Failed asserting node DENOTED BY %s OCCURS EXACTLY %i times';
                $count   = $this->_contentCountExact;
            }

            if (null !== $this->_contentCountMin) {
                $failure = 'Failed asserting node DENOTED BY %s OCCURS AT LEAST %i times';
                $count   = $this->_contentCountMin;
            }

            if (null !== $this->_contentCountMax) {
                $failure = 'Failed asserting node DENOTED BY %s OCCURS AT MOST %i times';
                $count   = $this->_contentCountMax;
            }
            throw new Zend_PHPUnit_Constraint_Exception(
                sprintf($failure, $other, $count),
                null
            );
        }

        if ($this->_negate) {
            $failure = 'Failed asserting node DENOTED BY %s DOES NOT EXIST';
        } else {
            $failure = 'Failed asserting node DENOTED BY %s EXISTS';
        }
        throw new Zend_PHPUnit_Constraint_Exception(
            sprintf($failure, $other),
            null
        );
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
     * @return bool
     */
    protected function _matchContent($result)
    {
        if (0 == count($result)) {
            return false;
        }

        foreach ($result as $node) {
            $content = $this->_getNodeContent($node);
            if (strstr($content, $this->_contentToMatch)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check to see if content is NOT matched in selected nodes
     * 
     * @param  Zend_Dom_Query_Result $result 
     * @return bool
     */
    protected function _notMatchContent($result)
    {
        if (0 == count($result)) {
            return true;
        }

        foreach ($result as $node) {
            $content = $this->_getNodeContent($node);
            if (strstr($content, $this->_contentToMatch)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check to see if content is matched by regex in selected nodes
     * 
     * @param  Zend_Dom_Query_Result $result 
     * @return bool
     */
    protected function _regexContent($result)
    {
        if (0 == count($result)) {
            return false;
        }

        foreach ($result as $node) {
            $content = $this->_getNodeContent($node);
            if (preg_match($this->_contentRegex, $content)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check to see if content is NOT matched by regex in selected nodes
     * 
     * @param  Zend_Dom_Query_Result $result 
     * @return bool
     */
    protected function _notRegexContent($result)
    {
        if (0 == count($result)) {
            return true;
        }

        foreach ($result as $node) {
            $content = $this->_getNodeContent($node);
            if (preg_match($this->_contentRegex, $content)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine if content count matches criteria
     * 
     * @param  Zend_Dom_Query_Result $result 
     * @return boolean
     */
    protected function _countContent($result)
    {
        $count = count($result);
        if (null !== $this->_contentCountExact) {
            return ($count == $this->_contentCountExact);
        }

        if (null !== $this->_contentCountMin) {
            return ($count >= $this->_contentCountMin);
        }

        if (null !== $this->_contentCountMax) {
            return ($count <= $this->_contentCountMax);
        }

        return false;
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
