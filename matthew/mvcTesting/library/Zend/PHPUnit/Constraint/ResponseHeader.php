<?php
require_once 'PHPUnit/Framework/Constraint.php';

class Zend_PHPUnit_Constraint_ResponseHeader extends PHPUnit_Framework_Constraint
{
    const ASSERT_RESPONSE_CODE   = 'assertResponseCode';
    const ASSERT_HEADER          = 'assertHeader';
    const ASSERT_HEADER_CONTAINS = 'assertHeaderContains';
    const ASSERT_HEADER_REGEX    = 'assertHeaderRegex';

    protected $_assertType      = null;
    protected $_assertTypes     = array(
        self::ASSERT_RESPONSE_CODE,
        self::ASSERT_HEADER,
        self::ASSERT_HEADER_CONTAINS,
        self::ASSERT_HEADER_REGEX,
    );

    protected $_code              = 200;
    protected $_header            = null;
    protected $_match             = null;
    protected $_negate            = false;

    /**
     * Constructor; setup constraint state
     * 
     * @return void
     */
    public function __construct()
    {
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
     * @param  Zend_Controller_Response_Abstract $other String to examine
     * @param  null|string Assertion type
     * @return bool
     */
    public function evaluate($other, $assertType = null)
    {
        if (!$other instanceof Zend_Controller_Response_Abstract) {
            require_once 'Zend/PHPUnit/Constraint/Exception.php';
            throw new Zend_PHPUnit_Constraint_Exception('Header constraint assertions require a response object');
        }

        if (strstr($assertType, 'Not')) {
            $this->setNegate(true);
            $assertType = str_replace('Not', '', $assertType);
        }

        if (!in_array($assertType, $this->_assertTypes)) {
            require_once 'Zend/PHPUnit/Constraint/Exception.php';
            throw new Zend_PHPUnit_Constraint_Exception(sprintf('Invalid assertion type "%s" provided to %s constraint', $assertType, __CLASS__));
        }

        $this->_assertType = $assertType;

        $response = $other;
        $argv     = func_get_args();
        $argc     = func_num_args();

        switch ($assertType) {
            case self::ASSERT_RESPONSE_CODE:
                if (3 > $argc) {
                    require_once 'Zend/PHPUnit/Constraint/Exception.php';
                    throw new Zend_PHPUnit_Constraint_Exception('No response code provided against which to match');
                }
                $this->_code = $code = $argv[2];
                return ($this->_negate)
                    ? $this->_notCode($response, $code)
                    : $this->_code($response, $code);
            case self::ASSERT_HEADER:
                if (3 > $argc) {
                    require_once 'Zend/PHPUnit/Constraint/Exception.php';
                    throw new Zend_PHPUnit_Constraint_Exception('No header provided against which to match');
                }
                $this->_header = $header = $argv[2];
                return ($this->_negate)
                    ? $this->_notHeader($response, $header)
                    : $this->_header($response, $header);
            case self::ASSERT_HEADER_CONTAINS:
                if (4 > $argc) {
                    require_once 'Zend/PHPUnit/Constraint/Exception.php';
                    throw new Zend_PHPUnit_Constraint_Exception('Both a header name and content to match are required for ' . __FUNCTION__);
                }
                $this->_header = $header = $argv[2];
                $this->_match  = $match  = $argv[3];
                return ($this->_negate)
                    ? $this->_notHeaderContains($response, $header, $match)
                    : $this->_headerContains($response, $header, $match);
            case self::ASSERT_HEADER_REGEX:
                if (4 > $argc) {
                    require_once 'Zend/PHPUnit/Constraint/Exception.php';
                    throw new Zend_PHPUnit_Constraint_Exception('Both a header name and content to match are required for ' . __FUNCTION__);
                }
                $this->_header = $header = $argv[2];
                $this->_match  = $match  = $argv[3];
                return ($this->_negate)
                    ? $this->_notHeaderRegex($response, $header, $match)
                    : $this->_headerRegex($response, $header, $match);
            default:
                require_once 'Zend/PHPUnit/Constraint/Exception.php';
                throw new Zend_PHPUnit_Constraint_Exception('Invalid assertion type ' . __FUNCTION__);
        }
    }

    /**
     * Report Failure
     * 
     * @see    PHPUnit_Framework_Constraint for implementation details
     * @param  mixed $other 
     * @param  string $description Additional message to display
     * @param  bool $not 
     * @return void
     * @throws PHPUnit_Framework_ExpectationFailedException
     */
    public function fail($other, $description, $not = false)
    {
        require_once 'Zend/PHPUnit/Constraint/Exception.php';
        switch ($this->_assertType) {
            case self::ASSERT_RESPONSE_CODE:
                $failure = 'Failed asserting response code "%s"';
                if ($this->_negate) {
                    $failure = 'Failed asserting response code IS NOT "%s"';
                }
                $failure = sprintf($failure, $this->_code);
                break;
            case self::ASSERT_HEADER:
                $failure = 'Failed asserting response header "%s" found';
                if ($this->_negate) {
                    $failure = 'Failed asserting response response header "%s" WAS NOT found';
                }
                $failure = sprintf($failure, $this->_header);
                break;
            case self::ASSERT_HEADER_CONTAINS:
                $failure = 'Failed asserting response header "%s" exists and contains "%s"';
                if ($this->_negate) {
                    $failure = 'Failed asserting response header "%s" DOES NOT CONTAIN "%s"';
                }
                $failure = sprintf($failure, $this->_header, $this->_match);
                break;
            case self::ASSERT_HEADER_REGEX:
                $failure = 'Failed asserting response header "%s" exists and matches regex "%s"';
                if ($this->_negate) {
                    $failure = 'Failed asserting response header "%s" DOES NOT MATCH regex "%s"';
                }
                $failure = sprintf($failure, $this->_header, $this->_match);
                break;
            default:
                throw new Zend_PHPUnit_Constraint_Exception('Invalid assertion type ' . __FUNCTION__);
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

    protected function _code(Zend_Controller_Response_Abstract $response, $code)
    {
        $test = $this->_getCode($response);
        return ($test == $code);
    }

    protected function _notCode(Zend_Controller_Response_Abstract $response, $code)
    {
        $test = $this->_getCode($response);
        return ($test != $code);
    }

    protected function _getCode(Zend_Controller_Response_Abstract $response)
    {
        $test = $response->getHttpResponseCode();
        if (null === $test) {
            $test = 200;
        }
        return $test;
    }

    protected function _header(Zend_Controller_Response_Abstract $response, $header)
    {
        return (null !== $this->_getHeader($response, $header));
    }

    protected function _notHeader(Zend_Controller_Response_Abstract $response, $header)
    {
        return (null === $this->_getHeader($response, $header));
    }

    protected function _getHeader(Zend_Controller_Response_Abstract $response, $header)
    {
        $headers = $response->sendHeaders();
        $header  = strtolower($header);
        if (array_key_exists($header, $headers)) {
            return $headers[$header];
        }
        return null;
    }

    protected function _headerContains(Zend_Controller_Response_Abstract $response, $header, $match)
    {
        if (null === ($fullHeader = $this->_getHeader($response, $header))) {
            return false;
        }

        $contents = str_replace($header . ': ', '', $fullHeader);

        return (strstr($contents, $match));
    }

    protected function _notHeaderContains(Zend_Controller_Response_Abstract $response, $header, $match)
    {
        if (null === ($fullHeader = $this->_getHeader($response, $header))) {
            return true;
        }

        $contents = str_replace($header . ': ', '', $fullHeader);

        return (!strstr($contents, $match));
    }

    protected function _headerRegex(Zend_Controller_Response_Abstract $response, $header, $pattern)
    {
        if (null === ($fullHeader = $this->_getHeader($response, $header))) {
            return false;
        }

        $contents = str_replace($header . ': ', '', $fullHeader);

        return preg_match($pattern, $contents);
    }

    protected function _notHeaderRegex(Zend_Controller_Response_Abstract $response, $header, $pattern)
    {
        if (null === ($fullHeader = $this->_getHeader($response, $header))) {
            return true;
        }

        $contents = str_replace($header . ': ', '', $fullHeader);

        return !preg_match($pattern, $contents);
    }
}
