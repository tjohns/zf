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
 * @package    Zend_Doctrine2
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once "Zend/Doctrine2/Tool/ProviderAbstract.php";

class Zend_Doctrine2_Tool_Dc2Build extends Zend_Doctrine2_Tool_ProviderAbstract
{
    /**
     * @var array
     */
    protected $_specialties = array('All', 'Db', 'Models', 'Forms', 'Proxies');

    /**
     * @var bool
     */
    protected $_askedForConfirmation = false;

    public function create()
    {
        $this->createDb($module);
        $this->createProxies($module);
    }

    public function drop($dropStrategy="metadata")
    {
        $this->dropDb($module, $dropStrategy);
    }
    
    public function update()
    {
        throw new Zend_Doctrine2_Exception("Not yet implemented.");
    }

    public function validate()
    {
        throw new Zend_Doctrine2_Exception("Not yet implemented.");
    }

    public function validateDb()
    {
        $em = $this->_getEntityManager();
        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($em);

        $response = $this->_registry->getResponse();
        $request = $this->_registry->getRequest();

        $queries = $schemaTool->getUpdateSchemaSql($this->_getAllClassMetadata());

        if(count($queries) > 0) {
            $response->appendContent("Databse Schema is invalid!", array('color' => array('hiWhite', 'bgRed'), 'aligncenter' => true));
            $response->appendContent(count($queries)." outstanding changes have to be applied to sync Database Schema with Model.");

            if($request->isVerbose()) {
                for($i = 0; $i < count($queries); $i++) {
                    $response->appendContent($queries[$i].";", array("indention" => 2));
                }
            }
        } else {
            $response->appendContent("Database Schema is valid!", array("color" => array("hiWhite", "bgGreen"), 'aligncenter' => true));
        }
    }

    public function createDb()
    {
        $this->_executeSchemaAction( "createSchema", "getCreateSchemaSql", "Creating", "created", false);
    }

    public function dropDb($dropStrategy="metadata")
    {
        $this->_executeSchemaAction("dropSchema", "getDropSchemaSql", "Dropping", "dropped", true, $dropStrategy);
    }

    public function updateDb()
    {
        $this->_executeSchemaAction("updateSchema", "getUpdateSchemaSql", "Updating", "update", true);
    }

    /**
     * Template for execution of schema actions update, create and drop.
     *
     * @param string $executeMethodName
     * @param string $showQueriesMethodName
     * @param string $doFragment
     * @param string $doneFragment
     */
    protected function _executeSchemaAction($executeMethodName, $showQueriesMethodName, $doFragment, $doneFragment, $askForConfirmation=false, $mode = null)
    {
        $em = $this->_getEntityManager();
        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($em);

        $response = $this->_registry->getResponse();
        $request = $this->_registry->getRequest();

        if($request->isPretend()) {
            $queries = $schemaTool->$showQueriesMethodName($this->_getAllClassMetadata());

            if($request->isVerbose()) {
                $response->appendContent("The following ".count($queries)." queries will be executed:");
                for($i = 0; $i < count($queries); $i++) {
                    $response->appendContent($queries[$i].";", array("indention" => 2));
                }
            } else {
                $response->appendContent($doFragment." database schema will execute ".count($queries)." against the database.");
                $response->appendContent('Use --verbose to show all the queries.');
            }
        } else {
            $metadata = $this->_getAllClassMetadata();

            if($askForConfirmation && !$this->_askedForConfirmation) {
                $response->appendContent("This command will alter the database and you may lose contained data.");
                $input = $this->_registry->getClient()->promptInteractiveInput("Please confirm alterting the database: [y/N]");
                if(strtolower($input->getContent()) !== "y") {
                    $response->appendContent("Aborted.");
                    return;
                }
                $this->_askedForConfirmation = true;
            }

            $response->appendContent($doFragment.' database schema...');

            $schemaTool->$executeMethodName($metadata, $mode);

            if($request->isVerbose()) {
                $queries = $schemaTool->$showQueriesMethodName($metadata);
                for($i = 0; $i < count($queries); $i++) {
                    $response->appendContent($queries[$i].";", array("indention" => 2));
                }
            }

            $response->appendContent('Schema was '.$doneFragment.' successfully.');
        }
    }

    /**
     * Generate proxies for all entities in the given module.
     *
     * @return void
     */
    public function createProxies()
    {
        $em = $this->_getEntityManager();
        $proxyFactory = $em->getProxyFactory();

        $metadata = $this->_getAllClassMetadata();

        $resp = $this->_registry->getResponse();
        if($this->_registry->getRequest()->isPretend()) {
            $proxyDir = $em->getConfiguration()->getProxyDir();
            $resp->appendContent('Will generate '.count($metadata).' proxies into directory: '.$proxyDir);
            if($this->_registry->getRequest()->isVerbose()) {
                foreach($metadata AS $classMetadata) {
                    $resp->appendContent($classMetadata->name, array("indention" => 2));
                }
            }
        } else {
            if($this->_registry->getRequest()->isVerbose()) {
                $resp->appendContent("Starting to generate ".count($metadata)." proxies.");
                foreach($metadata AS $classMetadata) {
                    $resp->appendContent('Generating Proxy for '.$classMetadata->name.'..', array("indention" => 2));
                    $proxyFactory->generateProxyClasses(array($classMetadata));
                }
            } else {
                $proxyFactory->generateProxyClasses($metadata);
            }
            $resp->appendContent('Successfully generated '.count($metadata).' proxies.');
        }
    }

    public function updateProxies()
    {
        $this->createProxies($module);
    }

    public function reCreate($module=null, $dropStrategy="metadata")
    {
        $this->drop($module, $dropStrategy);
        $this->create($module);
    }

    public function reCreateDb($module, $dropStrategy="metadata")
    {
        $this->dropDb($module, $dropStrategy);
        $this->createDb($module);
    }
}