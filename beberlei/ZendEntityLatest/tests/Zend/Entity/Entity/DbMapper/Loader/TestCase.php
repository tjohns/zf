<?php

abstract class Zend_Entity_DbMapper_Loader_TestCase extends Zend_Entity_TestCase
{
    /**
     * @var Zend_Entity_Manager
     */
    protected $entityManager = null;

    /**
     * @var Zend_Entity_Fixture_Abstract
     */
    protected $fixture = null;

    /**
     * @var Zend_Entity_IdentityMap
     */
    protected $identityMap = null;

    /**
     * @var Zend_Entity_Metadata_Interface
     */
    protected $resourceMap = null;

    /**
     * @var Zend_Test_DbAdapter
     */
    protected $adapter = null;

    /**
     * @var array
     */
    protected $mappings = array();

    protected $loader = null;

    private $_performanceStart = null;

    private $_fixtureIsInitialized = false;

    abstract public function getFixtureClassName();

    abstract public function getLoaderClassName();

    public function initFixture($fixtureClassName=null)
    {
        if($this->_fixtureIsInitialized == true) {
            return;
        }

        if($fixtureClassName == null) {
            $fixtureClassName = $this->getFixtureClassName();
        }

        $this->adapter = new Zend_Test_DbAdapter();
        $this->fixture = new $fixtureClassName;
        $this->resourceMap = $this->fixture->getResourceMap();

        $this->identityMap = new Zend_Entity_IdentityMap();
        $this->entityManager = new Zend_Entity_Manager(array('identityMap' => $this->identityMap, 'adapter' => $this->adapter));
        $this->entityManager->setMetadataFactory($this->resourceMap);

        $this->_fixtureIsInitialized = true;
    }

    /**
     *
     * @return Zend_Db_Mapper_Loader_LoaderAbstract
     */
    public function createLoader()
    {
        if($this->loader == null) {
            $this->initFixture();

            $loaderClassName = $this->getLoaderClassName();
            $this->mappings = $this->fixture->getResourceMap()->transform('Zend_Db_Mapper_Mapping');

            $this->loader = new $loaderClassName($this->entityManager, $this->mappings);
        }
        return $this->loader;
    }

    protected function createEntityManager($unitOfWork=null, $metadataFactory=null, $identityMap=null, $db=null)
    {
        return $this->entityManager;
    }

    public function startPerformanceMeasuring()
    {
        if(ini_get('xdebug.profiler_enabled') == true) {
            $this->markTestSkipped("Xdebug Profiling enabled makes this test slow enough to fail for sure.");
        }
        if(function_exists('xdebug_code_coverage_started') && xdebug_code_coverage_started() == true) {
            $this->markTestSkipped("Xdebug Code Coverage enabled makes this test slow enough to fail for sure.");
        }

        $this->_performanceStart = microtime(true);
    }

    /**
     * @param float $start
     * @param int|float $tresholdSeconds
     */
    public function assertTookNoLongerThan($tresholdSeconds)
    {
        if($this->_performanceStart == null) {
            $this->fail("Have to call startPerformanceMeasuring() before assertTookNoLongerThan()");
        }

        $diff = microtime(true) - $this->_performanceStart;
        $this->assertTrue(
            $diff <= $tresholdSeconds,
            "Test should not take longer than '".$tresholdSeconds."' seconds, ".
            "but ".number_format($diff, 4)." seconds passed!"
        );
    }
}