<?php

require_once 'PHPUnit/Framework/TestCase.php';

class Zend_PHPUnit_ControllerTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var mixed Bootstrap file path or callback
     */
    public $bootstrap;

    /**
     * @var Zend_Controller_Front
     */
    protected $_controller;

    /**
     * @var Zend_Dom_Query
     */
    protected $_query;

    /**
     * @var Zend_Controller_Request_Abstract
     */
    protected $_request;
    
    /**
     * @var Zend_Controller_Response_Abstract
     */
    protected $_response;

    /**
     * Set up MVC app
     *
     * Calls {@link bootstrap()} by default
     * 
     * @return void
     */
    protected function setUp()
    {
        $this->bootstrap();
    }

    /**
     * Bootstrap the front controller
     *
     * Resets the front controller, and then bootstraps it.
     *
     * If {@link $bootstrap} is a callback, executes it; if it is a file, it include's 
     * it. When done, sets the test case request and response objects into the 
     * front controller.
     * 
     * @return void
     */
    final public function bootstrap()
    {
        $this->reset();
        if (null !== $this->bootstrap) {
            if (is_callable($this->bootstrap)) {
                call_user_func($this->bootstrap);
            } elseif (is_string($this->bootstrap)) {
                require_once 'Zend/Loader.php';
                if (Zend_Loader::isReadable($this->bootstrap)) {
                    include $this->bootstrap;
                }
            }
        }
        $this->getFrontController()
             ->setRequest($this->getRequest())
             ->setResponse($this->getResponse());
    }

    /**
     * Dispatch the MVC
     *
     * If a URL is provided, sets it as the request URI in the request object. 
     * Then sets test case request and response objects in front controller, 
     * disables throwing exceptions, and disables returning the response.
     * Finally, dispatches the front controller.
     * 
     * @param  string|null $url 
     * @return void
     */
    public function dispatch($url = null)
    {
        if (null !== $url) {
            $this->getRequest()->setRequestUri($url);
        }
        $controller = $this->getFrontController();
        $controller->setRequest($this->getRequest())
                   ->setResponse($this->getResponse())
                   ->throwExceptions(false)
                   ->returnResponse(false);
        $controller->dispatch();
    }

    /**
     * Reset MVC state
     * 
     * Creates new request/response objects, resets the front controller 
     * instance, and resets the action helper broker.
     *
     * @todo   Need to update Zend_Layout to add a resetInstance() method
     * @return void
     */
    public function reset()
    {
        $this->getFrontController()->resetInstance();
        Zend_Controller_Action_HelperBroker::resetHelpers();
        $this->_request  = null;
        $this->_response = null;
    }

    /**
     * Assert against DOM selection
     * 
     * @param  string $path CSS selector path
     * @param  string $message
     * @return void
     */
    public function assertSelect($path, $message = '')
    {
        require_once 'Zend/PHPUnit/Constraint/DomQuery.php';
        $constraint = new Zend_PHPUnit_Constraint_DomQuery($path);
        $content    = $this->getResponse()->outputBody();
        if (!$constraint->evaluate($content, __FUNCTION__)) {
            $constraint->fail($path, $message);
        }
    }

    /**
     * Assert against DOM selection
     * 
     * @param  string $path CSS selector path
     * @param  string $message
     * @return void
     */
    public function assertNotSelect($path, $message = '')
    {
        require_once 'Zend/PHPUnit/Constraint/DomQuery.php';
        $constraint = new Zend_PHPUnit_Constraint_DomQuery($path);
        $content    = $this->getResponse()->outputBody();
        if (!$constraint->evaluate($content, __FUNCTION__)) {
            $constraint->fail($path, $message);
        }
    }

    /**
     * Assert against DOM selection; node should contain content
     * 
     * @param  string $path CSS selector path
     * @param  string $match content that should be contained in matched nodes
     * @param  string $message
     * @return void
     */
    public function assertSelectContentContains($path, $match, $message = '')
    {
        require_once 'Zend/PHPUnit/Constraint/DomQuery.php';
        $constraint = new Zend_PHPUnit_Constraint_DomQuery($path);
        $content    = $this->getResponse()->outputBody();
        if (!$constraint->evaluate($content, __FUNCTION__, $match)) {
            $constraint->fail($path, $message);
        }
    }

    /**
     * Assert against DOM selection; node should NOT contain content
     * 
     * @param  string $path CSS selector path
     * @param  string $match content that should NOT be contained in matched nodes
     * @param  string $message
     * @return void
     */
    public function assertNotSelectContentContains($path, $match, $message = '')
    {
        require_once 'Zend/PHPUnit/Constraint/DomQuery.php';
        $constraint = new Zend_PHPUnit_Constraint_DomQuery($path);
        $content    = $this->getResponse()->outputBody();
        if (!$constraint->evaluate($content, __FUNCTION__, $match)) {
            $constraint->fail($path, $message);
        }
    }

    /**
     * Assert against DOM selection; node should match content
     * 
     * @param  string $path CSS selector path
     * @param  string $pattern Pattern that should be contained in matched nodes
     * @param  string $message
     * @return void
     */
    public function assertSelectContentRegex($path, $pattern, $message = '')
    {
        require_once 'Zend/PHPUnit/Constraint/DomQuery.php';
        $constraint = new Zend_PHPUnit_Constraint_DomQuery($path);
        $content    = $this->getResponse()->outputBody();
        if (!$constraint->evaluate($content, __FUNCTION__, $pattern)) {
            $constraint->fail($path, $message);
        }
    }

    /**
     * Assert against DOM selection; node should NOT match content
     * 
     * @param  string $path CSS selector path
     * @param  string $pattern pattern that should NOT be contained in matched nodes
     * @param  string $message
     * @return void
     */
    public function assertNotSelectContentRegex($path, $pattern, $message = '')
    {
        require_once 'Zend/PHPUnit/Constraint/DomQuery.php';
        $constraint = new Zend_PHPUnit_Constraint_DomQuery($path);
        $content    = $this->getResponse()->outputBody();
        if (!$constraint->evaluate($content, __FUNCTION__, $pattern)) {
            $constraint->fail($path, $message);
        }
    }

    /**
     * Assert against DOM selection; should contain exact number of nodes
     * 
     * @param  string $path CSS selector path
     * @param  string $count Number of nodes that should match
     * @param  string $message
     * @return void
     */
    public function assertSelectCount($path, $count, $message = '')
    {
        require_once 'Zend/PHPUnit/Constraint/DomQuery.php';
        $constraint = new Zend_PHPUnit_Constraint_DomQuery($path);
        $content    = $this->getResponse()->outputBody();
        if (!$constraint->evaluate($content, __FUNCTION__, $count)) {
            $constraint->fail($path, $message);
        }
    }

    /**
     * Assert against DOM selection; should NOT contain exact number of nodes
     * 
     * @param  string $path CSS selector path
     * @param  string $count Number of nodes that should NOT match
     * @param  string $message
     * @return void
     */
    public function assertNotSelectCount($path, $count, $message = '')
    {
        require_once 'Zend/PHPUnit/Constraint/DomQuery.php';
        $constraint = new Zend_PHPUnit_Constraint_DomQuery($path);
        $content    = $this->getResponse()->outputBody();
        if (!$constraint->evaluate($content, __FUNCTION__, $count)) {
            $constraint->fail($path, $message);
        }
    }

    /**
     * Assert against DOM selection; should contain at least this number of nodes
     * 
     * @param  string $path CSS selector path
     * @param  string $count Minimum number of nodes that should match
     * @param  string $message
     * @return void
     */
    public function assertSelectCountMin($path, $count, $message = '')
    {
        require_once 'Zend/PHPUnit/Constraint/DomQuery.php';
        $constraint = new Zend_PHPUnit_Constraint_DomQuery($path);
        $content    = $this->getResponse()->outputBody();
        if (!$constraint->evaluate($content, __FUNCTION__, $count)) {
            $constraint->fail($path, $message);
        }
    }

    /**
     * Assert against DOM selection; should contain no more than this number of nodes
     * 
     * @param  string $path CSS selector path
     * @param  string $count Maximum number of nodes that should match
     * @param  string $message
     * @return void
     */
    public function assertSelectCountMax($path, $count, $message = '')
    {
        require_once 'Zend/PHPUnit/Constraint/DomQuery.php';
        $constraint = new Zend_PHPUnit_Constraint_DomQuery($path);
        $content    = $this->getResponse()->outputBody();
        if (!$constraint->evaluate($content, __FUNCTION__, $count)) {
            $constraint->fail($path, $message);
        }
    }

    public function assertResponse($spec1, $spec2 = null)
    {
    }

    public function assertNotResponse($spec1, $spec2 = null)
    {
    }

    public function assertRedirect($spec)
    {
    }

    public function assertNotRedirect($spec)
    {
    }

    /**
     * Retrieve front controller instance
     * 
     * @return Zend_Controller_Front
     */
    public function getFrontController()
    {
        if (null === $this->_controller) {
            require_once 'Zend/Controller/Front.php';
            $this->_controller = Zend_Controller_Front::getInstance();
        }
        return $this->_controller;
    }

    /**
     * Retrieve test case request object
     * 
     * @return Zend_Controller_Request_Abstract
     */
    public function getRequest()
    {
        if (null === $this->_request) {
            require_once 'Zend/Controller/Request/HttpTestCase.php';
            $this->_request = new Zend_Controller_Request_HttpTestCase;
        }
        return $this->_request;
    }

    /**
     * Retrieve test case response object 
     * 
     * @return Zend_Controller_Response_Abstract
     */
    public function getResponse()
    {
        if (null === $this->_response) {
            require_once 'Zend/Controller/Response/HttpTestCase.php';
            $this->_response = new Zend_Controller_Response_HttpTestCase;
        }
        return $this->_response;
    }

    /**
     * Retrieve DOM query object
     * 
     * @return Zend_Dom_Query
     */
    public function getQuery()
    {
        if (null === $this->_query) {
            require_once 'Zend/Dom/Query.php';
            $this->_query = new Zend_Dom_Query;
        }
        return $this->_query;
    }
}
